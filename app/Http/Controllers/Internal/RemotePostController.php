<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\BlogPost; // ajuste para seu model real
use Carbon\Carbon;

class RemotePostController extends Controller
{
    public function store(Request $request)
    {
        // 1) Auth por token (simples, efetivo)
        $token = (string) $request->header('X-REMOTE-TOKEN');
        abort_unless(hash_equals((string) env('REMOTE_POST_TOKEN'), $token), 403);

        // 2) Validação de payload (ajuste os campos conforme o React envia)
        $data = $request->validate([
            'title'         => ['required','string','max:180'],
            'slug'          => ['nullable','string','max:220'],
            'content_html'  => ['required','string'],   // ou content_md
            'excerpt'       => ['nullable','string','max:300'],
            'status'        => ['nullable','in:draft,published,scheduled'],
            'publish_date'  => ['nullable','date'],     // ISO8601 ou "Y-m-d H:i:s"
            'category_id'   => ['nullable','integer'],
            'category_slug' => ['nullable','string','max:120'],
            'tags'          => ['nullable','array'],
            'tags.*'        => ['string','max:60'],
            'cover_image'   => ['nullable','string','max:2048'],
            'canonical_url' => ['nullable','string','max:2048'],
            'lang'          => ['nullable','in:pt,en,es','max:2'],

            // id externo para idempotência
            'external_id'   => ['nullable','string','max:120'],
        ]);

        // 3) Normalizações
        $title = trim($data['title']);
        $slug = trim($data['slug'] ?? '');
        if ($slug === '') $slug = Str::slug($title);

        // garante slug único
        $slug = $this->uniqueSlug($slug, $data['external_id'] ?? null);

        $status = $data['status'] ?? 'draft';

        $publishDate = null;
        if (!empty($data['publish_date'])) {
            $publishDate = Carbon::parse($data['publish_date']);
            // se for scheduled e publish_date <= agora, publica
            if ($status === 'scheduled' && $publishDate->lte(now())) {
                $status = 'published';
            }
        }

        // 4) Resolve categoria (opcional) por slug (se você usa categories)
        $categoryId = $data['category_id'] ?? null;
        if (!$categoryId && !empty($data['category_slug'])) {
            // ajuste o Model se você tiver Category
            // $categoryId = \App\Models\Category::where('slug',$data['category_slug'])->value('id');
            $categoryId = null;
        }

        // 5) Idempotência: se veio external_id, faz upsert por ele
        $post = null;
        if (!empty($data['external_id'])) {
            $post = BlogPost::where('external_id', $data['external_id'])->first();
        }

        if (!$post) $post = new BlogPost();

        // 6) Mapeia campos -> model (ajuste nomes das colunas conforme seu banco)
        $post->title = $title;
        $post->slug = $slug;
        $post->excerpt = $data['excerpt'] ?? null;
        $post->content_html = $data['content_html']; // ajuste: body, content, html
        $post->status = $status;
        $post->publish_date = $publishDate; // se sua coluna for publish_date (datetime)
        $post->category_id = $categoryId;
        $post->cover_image = $data['cover_image'] ?? null;
        $post->canonical_url = $data['canonical_url'] ?? null;
        $post->lang = $data['lang'] ?? 'pt';

        if (!empty($data['external_id'])) {
            $post->external_id = $data['external_id'];
        }

        // tags: se você salva em JSON numa coluna
        if (array_key_exists('tags', $data)) {
            $post->tags = $data['tags']; // coluna JSON (cast array)
        }

        $post->save();

        return response()->json([
            'ok' => true,
            'id' => $post->id,
            'slug' => $post->slug,
            'status' => $post->status,
            'publish_date' => optional($post->publish_date)->toISOString(),
        ], 201);
    }

    private function uniqueSlug(string $baseSlug, ?string $externalId = null): string
    {
        $baseSlug = Str::slug($baseSlug);
        if ($baseSlug === '') $baseSlug = 'post';

        // Se tiver external_id e já existe, mantém o slug dele (idempotência)
        if ($externalId) {
            $existing = BlogPost::where('external_id', $externalId)->first();
            if ($existing && $existing->slug) return $existing->slug;
        }

        $slug = $baseSlug;
        $i = 2;

        while (BlogPost::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $i;
            $i++;
            if ($i > 5000) {
                throw ValidationException::withMessages([
                    'slug' => 'Não foi possível gerar um slug único.',
                ]);
            }
        }

        return $slug;
    }
}
