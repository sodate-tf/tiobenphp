<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PostCrossLinkStoreRequest;
use App\Http\Requests\Admin\PostCrossLinkUpdateRequest;
use App\Models\Post;
use App\Models\PostCrossLink;
use Illuminate\Http\Request;

class LiturgyLinkController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $date = trim((string) $request->get('date', ''));

        $links = PostCrossLink::query()
    ->with(['post:id,title'])
    ->when($date !== '', fn ($qr) => $qr->whereDate('link_date', $date))
    ->when($q !== '', function ($qr) use ($q) {
        $qr->whereHas('post', function ($p) use ($q) {
            $p->where('title', 'like', "%{$q}%");
        });
    })
    ->orderByDesc('link_date')
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
            ->limit(2000)
            ->get();

        return view('admin.liturgy-links.create', compact('posts'));
    }

    public function store(PostCrossLinkStoreRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = (bool) ($request->boolean('is_active', true));
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        PostCrossLink::create($data);

        return redirect()
            ->route('admin.liturgy-links.index')
            ->with('success', 'Vínculo criado com sucesso.');
    }

    public function edit(PostCrossLink $liturgy_link)
    {
        $posts = Post::query()
            ->select(['id', 'title'])
            ->orderBy('title')
            ->limit(2000)
            ->get();

        return view('admin.liturgy-links.edit', [
            'link' => $liturgy_link->load(['post:id,title']),
            'posts' => $posts,
        ]);
    }

    public function update(PostCrossLinkUpdateRequest $request, PostCrossLink $liturgy_link)
    {
        $data = $request->validated();
        $data['is_active'] = (bool) ($request->boolean('is_active', true));
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        $liturgy_link->update($data);

        return redirect()
            ->route('admin.liturgy-links.index')
            ->with('success', 'Vínculo atualizado.');
    }

    public function destroy(PostCrossLink $liturgy_link)
    {
        $liturgy_link->update(['is_active' => false]);

        return redirect()
            ->route('admin.liturgy-links.index')
            ->with('success', 'Vínculo desativado.');
    }
}
