<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $lang = $request->get('lang', 'pt');
        $lang = in_array($lang, ['pt','en'], true) ? $lang : 'pt';

        $active = $request->get('active', 'all'); // all | 1 | 0
        if (!in_array($active, ['all','1','0'], true)) $active = 'all';

        $featured = $request->get('featured', 'all'); // all | 1 | 0
        if (!in_array($featured, ['all','1','0'], true)) $featured = 'all';

        $posts = Post::query()
            ->with('category:id,name')
            ->where('lang', $lang)
            ->when($active !== 'all', fn($qq) => $qq->where('is_active', $active === '1'))
            ->when($featured !== 'all', fn($qq) => $qq->where('is_featured', $featured === '1'))
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('title', 'like', "%{$q}%")
                      ->orWhere('slug', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('publish_date')
            ->paginate(20)
            ->withQueryString();

        return view('admin.posts.index', compact('posts', 'q', 'lang', 'active', 'featured'));
    }

    public function create(Request $request)
    {
        $lang = $request->get('lang', 'pt');
        $lang = in_array($lang, ['pt','en'], true) ? $lang : 'pt';

        $categories = Category::orderBy('name')->get(['id','name']);

        return view('admin.posts.form', [
            'post' => new Post([
                'lang' => $lang,
                'is_active' => true,
                'is_featured' => false,
                'publish_date' => now(),
            ]),
            'categories' => $categories,
            'lang' => $lang,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request, isUpdate: false);

        $data['slug'] = $this->makeUniqueSlug(
            lang: $data['lang'],
            desired: $this->normalizeSlug($data['slug'] ?? null, $data['title']),
            ignoreId: null
        );

        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['is_featured'] = (bool) ($data['is_featured'] ?? false);

        // upload / remove cover
        $this->handleCoverUpload($request, $data, currentCoverUrl: null);

        $post = Post::create($data);

        return redirect()
            ->route('admin.posts.edit', $post)
            ->with('success', 'Post criado.');
    }

    public function edit(Post $post)
    {
        $categories = Category::orderBy('name')->get(['id','name']);

        return view('admin.posts.form', [
            'post' => $post,
            'categories' => $categories,
            'lang' => $post->lang,
        ]);
    }

    public function update(Request $request, Post $post)
    {
        $data = $this->validatedData($request, isUpdate: true);

        $data['slug'] = $this->makeUniqueSlug(
            lang: $data['lang'],
            desired: $this->normalizeSlug($data['slug'] ?? null, $data['title']),
            ignoreId: $post->id
        );

        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['is_featured'] = (bool) ($data['is_featured'] ?? false);

        // upload / remove cover (apaga a antiga se local)
        $this->handleCoverUpload($request, $data, currentCoverUrl: $post->cover_image_url);

        $post->update($data);

        return redirect()
            ->route('admin.posts.edit', $post)
            ->with('success', 'Post atualizado.');
    }

    public function destroy(Post $post)
    {
        $lang = $post->lang;

        // apaga capa local antes de remover o post
        $this->deleteLocalCoverIfAny($post->cover_image_url);

        $post->delete();

        return redirect()
            ->route('admin.posts.index', ['lang' => $lang])
            ->with('success', 'Post removido.');
    }

    public function show(Post $post)
    {
        return redirect()->route('admin.posts.edit', $post);
    }

    // =========================
    // Helpers
    // =========================

    private function validatedData(Request $request, bool $isUpdate): array
    {
        // mesmos campos para store/update (você já usa required em content/publish_date)
        return $request->validate([
            'lang' => ['required', Rule::in(['pt','en'])],
            'title' => ['required','string','max:255'],
            'slug' => ['nullable','string','max:255'],
            'keywords' => ['nullable','string'],
            'meta_description' => ['nullable','string'],

            // mantém compatibilidade com URL externa OU /storage/...
            'cover_image_url'  => ['nullable','string','max:2048'],
            'cover_image_file' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:4096'],
            'remove_cover'     => ['nullable','in:1'],

            'content' => ['required','string'],
            'category_id' => ['nullable','string', Rule::exists('categories','id')],
            'is_active' => ['nullable','boolean'],
            'is_featured' => ['nullable','boolean'],
            'publish_date' => ['required','date'],
            'expiry_date' => ['nullable','date'],
        ]);
    }

    private function normalizeSlug(?string $slugInput, string $title): string
    {
        $raw = trim((string) $slugInput);
        return $raw !== '' ? Str::slug($raw) : Str::slug($title);
    }

    private function makeUniqueSlug(string $lang, string $desired, ?string $ignoreId): string
    {
        $slug = $desired;
        $base = $desired;
        $i = 2;

        while (
            Post::where('lang', $lang)
                ->where('slug', $slug)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }

    private function handleCoverUpload(Request $request, array &$data, ?string $currentCoverUrl = null): void
    {
        // Remover capa (se marcado)
        if ($request->input('remove_cover') === '1') {
            $this->deleteLocalCoverIfAny($currentCoverUrl);
            $data['cover_image_url'] = null;
        }

        // Upload substitui tudo
        if ($request->hasFile('cover_image_file')) {
            // apaga a anterior (se era local)
            $this->deleteLocalCoverIfAny($currentCoverUrl);

            $path = $request->file('cover_image_file')->store('posts/covers', 'public');
            $data['cover_image_url'] = Storage::disk('public')->url($path);
        }
    }

    private function deleteLocalCoverIfAny(?string $coverUrl): void
    {
        if (!$coverUrl) return;

        // Se você salva como /storage/..., converte para path do disk public
        $prefix = '/storage/';
        if (str_starts_with($coverUrl, $prefix)) {
            $relative = substr($coverUrl, strlen($prefix)); // ex: posts/covers/a.webp
            Storage::disk('public')->delete($relative);
        }
    }
}
