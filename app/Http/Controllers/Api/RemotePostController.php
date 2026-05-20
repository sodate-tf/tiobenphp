<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RemotePostController extends Controller
{
    /**
     * POST /api/remote-post
     * Replica do Next: recebe title, slug, content, categoryId, etc. e salva.
     * Sem validações "externas" (apenas API key é feita via middleware).
     */
    public function remoteStore(Request $request)
    {
        try {
            $data = $request->json()->all() ?: [];

            // Defaults e normalização leve (sem "bloquear")
            $id = (string) ($data['id'] ?? (string) Str::uuid());
            $title = (string) ($data['title'] ?? '');
            $slug = (string) ($data['slug'] ?? '');

            if ($slug === '' && $title !== '') $slug = Str::slug($title);
            if ($slug === '') $slug = 'post-' . Str::random(8);

            // slug único (se já existe, sufixa)
            $slug = $this->uniqueSlug($slug, $id);

            $content = (string) ($data['content'] ?? '');

            $post = new Post();
            $post->id = $id;
            $post->title = $title;
            $post->slug = $slug;
            $post->content = $content;

            $post->category_id = $data['categoryId'] ?? null;
            $post->category_name = $data['categoryName'] ?? 'Notícias';

            $post->keywords = $data['keywords'] ?? '';
            $post->meta_description = $data['metaDescription'] ?? '';

            $post->cover_image_url = $data['coverImageUrl'] ?? null;

            $post->is_active = array_key_exists('isActive', $data) ? (bool) $data['isActive'] : true;

            $post->publish_date = $this->parseDate($data['publishDate'] ?? null) ?? now();
            $post->expiry_date = $this->parseDate($data['expiryDate'] ?? null);

            // createdAt/updatedAt do Next (opcional)
            // Se vierem, respeita; senão Eloquent usa timestamps no save.
            if (!empty($data['createdAt'])) $post->setCreatedAt($this->parseDate($data['createdAt']) ?? now());
            if (!empty($data['updatedAt'])) $post->setUpdatedAt($this->parseDate($data['updatedAt']) ?? now());

            $post->save();

            return response()->json([
                'success' => true,
                'message' => 'Post salvo com sucesso!',
                'post' => $this->normalizePost($post),
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar post remoto.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /** GET /api/posts/{slug} */
    public function getBySlug(string $slug)
    {
        $row = Post::query()
            ->leftJoin('categories as c', 'posts.category_id', '=', 'c.id')
            ->where('posts.slug', $slug)
            ->select([
                'posts.*',
                'c.name as category_name_join',
            ])
            ->first();

        if (!$row) return response()->json(null, 404);

        // prioridade: join, depois coluna category_name
        $post = Post::find($row->id);
        $payload = $this->normalizePost($post);
        $payload['categoryName'] = $row->category_name_join ?? $payload['categoryName'];

        return response()->json($payload);
    }

    /** GET /api/posts */
    public function index()
    {
        $rows = Post::query()
            ->leftJoin('categories as c', 'posts.category_id', '=', 'c.id')
            ->orderByRaw('COALESCE(posts.publish_date, posts.created_at) DESC')
            ->select(['posts.*', 'c.name as category_name_join'])
            ->get();

        $out = $rows->map(function ($r) {
            // monta como o Next normalizePost
            return [
                'id' => (string) $r->id,
                'title' => $r->title,
                'slug' => $r->slug,
                'keywords' => $r->keywords ?? '',
                'metaDescription' => $r->meta_description ?? '',
                'content' => $r->content ?? '',
                'categoryId' => $r->category_id ? (string) $r->category_id : null,
                'categoryName' => $r->category_name_join ?? ($r->category_name ?? null),
                'isActive' => (bool) $r->is_active,
                'publishDate' => optional($r->publish_date)->toISOString() ?? '',
                'expiryDate' => optional($r->expiry_date)->toISOString(),
                'coverImageUrl' => $r->cover_image_url,
                'createdAt' => optional($r->created_at)->toISOString(),
                'updatedAt' => optional($r->updated_at)->toISOString(),
            ];
        });

        return response()->json($out);
    }

    /** GET /api/posts/sitemap */
    public function sitemap()
    {
        $rows = Post::query()
            ->select(['slug'])
            ->selectRaw('COALESCE(updated_at, created_at) as updated_at')
            ->orderByDesc('updated_at')
            ->get();

        $out = $rows->map(fn($r) => [
            'slug' => $r->slug,
            'updatedAt' => Carbon::parse($r->updated_at)->toISOString(),
        ]);

        return response()->json($out);
    }

    /** GET /api/posts/latest?limit=5 */
    public function latest(Request $request)
    {
        $limit = (int) ($request->query('limit', 5));
        if ($limit < 1) $limit = 1;
        if ($limit > 10) $limit = 10;

        $rows = Post::query()
            ->where('is_active', true)
            ->orderByRaw('COALESCE(publish_date, created_at) DESC')
            ->limit($limit)
            ->get(['slug','title','meta_description']);

        $out = $rows->map(fn($r) => [
            'slug' => $r->slug,
            'title' => $r->title,
            'metaDescription' => $r->meta_description,
        ]);

        return response()->json($out);
    }

    /** PUT /api/posts/{id} */
    public function update(Request $request, string $id)
    {
        $post = Post::find($id);
        if (!$post) return response()->json(['success' => false, 'message' => 'Post não encontrado.'], 404);

        $data = $request->json()->all() ?: [];

        if (array_key_exists('title', $data)) $post->title = (string) $data['title'];
        if (array_key_exists('slug', $data)) $post->slug = $this->uniqueSlug((string) $data['slug'], $post->id);
        if (array_key_exists('content', $data)) $post->content = (string) $data['content'];

        if (array_key_exists('keywords', $data)) $post->keywords = (string) ($data['keywords'] ?? '');
        if (array_key_exists('metaDescription', $data)) $post->meta_description = (string) ($data['metaDescription'] ?? '');

        if (array_key_exists('categoryId', $data)) $post->category_id = $data['categoryId'] ?? null;
        if (array_key_exists('categoryName', $data)) $post->category_name = $data['categoryName'] ?? null;

        if (array_key_exists('isActive', $data)) $post->is_active = (bool) $data['isActive'];

        if (array_key_exists('publishDate', $data)) $post->publish_date = $this->parseDate($data['publishDate']);
        if (array_key_exists('expiryDate', $data)) $post->expiry_date = $this->parseDate($data['expiryDate']);

        if (array_key_exists('coverImageUrl', $data)) $post->cover_image_url = $data['coverImageUrl'] ?? null;

        $post->save();

        return response()->json([
            'success' => true,
            'post' => $this->normalizePost($post),
        ]);
    }

    /** DELETE /api/posts/{id} */
    public function destroy(string $id)
    {
        $post = Post::find($id);
        if (!$post) return response()->json(['success' => false], 404);

        $post->delete();
        return response()->json(['success' => true]);
    }

    // ---------------- helpers ----------------

    private function parseDate($v): ?Carbon
    {
        if ($v === null || $v === '') return null;
        try { return Carbon::parse($v); } catch (\Throwable) { return null; }
    }

    private function uniqueSlug(string $slug, string $keepId): string
    {
        $base = Str::slug($slug);
        if ($base === '') $base = 'post-' . Str::random(8);

        $candidate = $base;
        $i = 2;

        while (
            Post::where('slug', $candidate)
                ->where('id', '!=', $keepId)
                ->exists()
        ) {
            $candidate = $base . '-' . $i;
            $i++;
            if ($i > 5000) break;
        }

        return $candidate;
    }

    private function normalizePost(Post $p): array
    {
        return [
            'id' => (string) $p->id,
            'title' => $p->title,
            'slug' => $p->slug,
            'keywords' => $p->keywords ?? '',
            'metaDescription' => $p->meta_description ?? '',
            'content' => $p->content ?? '',
            'categoryId' => $p->category_id ? (string) $p->category_id : null,
            'categoryName' => $p->category_name ?? null,
            'isActive' => (bool) $p->is_active,
            'publishDate' => optional($p->publish_date)->toISOString() ?? '',
            'expiryDate' => optional($p->expiry_date)->toISOString(),
            'coverImageUrl' => $p->cover_image_url,
            'createdAt' => optional($p->created_at)->toISOString(),
            'updatedAt' => optional($p->updated_at)->toISOString(),
        ];
    }
}
