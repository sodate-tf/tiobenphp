<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostCrossLink;
use Illuminate\Http\Request;

class PostCrossLinkController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $date = trim((string) $request->get('date', ''));

        $links = PostCrossLink::query()
            ->with('post:id,title')
            ->when($q !== '', function ($qr) use ($q) {
                $qr->whereHas('post', fn($p) => $p->where('title', 'like', "%{$q}%"));
            })
            ->when($date !== '', fn($qr) => $qr->whereDate('link_date', $date))
            ->orderByRaw('COALESCE(link_date, created_at) DESC')
            ->orderBy('sort_order')
            ->paginate(20)
            ->withQueryString();

        return view('admin.liturgy-links.index', compact('links', 'q', 'date'));
    }

    public function create()
    {
        $posts = Post::query()
            ->select(['id', 'title'])
            ->orderBy('title')
            ->limit(3000)
            ->get();

        return view('admin.liturgy-links.create', compact('posts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'post_id'    => ['required', 'integer', 'exists:posts,id'],
            'link_date'  => ['required', 'date'],
            'paragraph'  => ['required', 'string'],
            'is_active'  => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        // NÃO pedir outro post:
        $data['linked_post_id'] = $data['post_id'];

        $data['is_active'] = (bool) $request->boolean('is_active', true);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        PostCrossLink::create($data);

        return redirect()->route('admin.liturgy-links.index')
            ->with('success', 'Vínculo de Liturgia criado.');
    }

    public function edit(PostCrossLink $liturgy_link)
    {
        $posts = Post::query()
            ->select(['id', 'title'])
            ->orderBy('title')
            ->limit(3000)
            ->get();

        return view('admin.liturgy-links.edit', [
            'link' => $liturgy_link->load('post:id,title'),
            'posts' => $posts,
        ]);
    }

    public function update(Request $request, PostCrossLink $liturgy_link)
    {
        $data = $request->validate([
            'post_id'    => ['required', 'integer', 'exists:posts,id'],
            'link_date'  => ['required', 'date'],
            'paragraph'  => ['required', 'string'],
            'is_active'  => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        // mantém lógica: linked_post_id = post_id
        $data['linked_post_id'] = $data['post_id'];

        $data['is_active'] = (bool) $request->boolean('is_active', true);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        $liturgy_link->update($data);

        return redirect()->route('admin.liturgy-links.index')
            ->with('success', 'Vínculo atualizado.');
    }

    public function destroy(PostCrossLink $liturgy_link)
    {
        // “delete seguro”: desativa
        $liturgy_link->update(['is_active' => false]);

        return redirect()->route('admin.liturgy-links.index')
            ->with('success', 'Vínculo desativado.');
    }
}
