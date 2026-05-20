<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostRelatedItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PostRelatedController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $posts = Post::query()
            ->select(['id', 'title', 'lang', 'publish_date'])
            ->when($q !== '', fn ($qr) => $qr->where('title', 'like', "%{$q}%"))
            ->orderByDesc('publish_date')
            ->paginate(20)
            ->withQueryString();

        return view('admin.related.index', compact('posts', 'q'));
    }

    public function edit(string $postId)
    {
        $post = Post::query()
            ->select(['id', 'title', 'lang', 'publish_date'])
            ->findOrFail($postId);

        $items = PostRelatedItem::query()
            ->with(['relatedPost:id,title,lang,publish_date'])
            ->where('post_id', $post->id)
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->get();

        // opções para adicionar (exclui ele mesmo)
        $availablePosts = Post::query()
            ->select(['id', 'title', 'lang', 'publish_date'])
            ->where('id', '!=', $post->id)
            ->orderByDesc('publish_date')
            ->limit(500) // evita select gigante; ajuste se quiser
            ->get();

        return view('admin.related.edit', compact('post', 'items', 'availablePosts'));
    }

    public function store(Request $request, string $postId)
    {
        $post = Post::query()->select(['id'])->findOrFail($postId);

        $data = $request->validate([
            'related_post_id' => ['required', 'integer', 'exists:posts,id'],
            'sort_order'      => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        if ($data['related_post_id'] === $post->id) {
            return back()->with('error', 'Você não pode relacionar o post com ele mesmo.');
        }

        $exists = PostRelatedItem::query()
            ->where('post_id', $post->id)
            ->where('related_post_id', $data['related_post_id'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Este post já está relacionado.');
        }

        PostRelatedItem::create([
            'post_id'         => $post->id,
            'related_post_id' => $data['related_post_id'],
            'sort_order'      => (int) ($data['sort_order'] ?? 0),
            'created_at'      => Carbon::now(),
        ]);

        return redirect()
            ->route('admin.related.edit', $post->id)
            ->with('success', 'Relacionado adicionado.');
    }

    public function updateOrder(Request $request, string $postId)
    {
        $post = Post::query()->select(['id'])->findOrFail($postId);

        $data = $request->validate([
            'orders'              => ['required', 'array'],
            'orders.*.id'         => ['required', 'integer', 'exists:post_related_items,id'],
            'orders.*.sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
        ]);

        // segurança: atualiza apenas itens deste post
        $ids = collect($data['orders'])->pluck('id')->all();
        $items = PostRelatedItem::query()
            ->where('post_id', $post->id)
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        foreach ($data['orders'] as $row) {
            $item = $items->get($row['id']);
            if ($item) {
                $item->sort_order = (int) $row['sort_order'];
                $item->save();
            }
        }

        return back()->with('success', 'Ordem atualizada.');
    }

    public function destroy(string $postId, int $itemId)
    {
        $post = Post::query()->select(['id'])->findOrFail($postId);

        PostRelatedItem::query()
            ->where('post_id', $post->id)
            ->where('id', $itemId)
            ->delete();

        return back()->with('success', 'Relacionado removido.');
    }
}
