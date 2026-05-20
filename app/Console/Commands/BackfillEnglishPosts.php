<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class BackfillEnglishPosts extends Command
{
    protected $signature = 'posts:backfill-english
        {--write : Persiste posts EN (sem essa flag roda em dry-run)}
        {--limit=20 : Limite de posts PT a processar}
        {--from-date= : Processa posts com publish_date >= YYYY-MM-DD}
        {--sleep-ms=250 : Pausa entre chamadas de IA para reduzir rate limit}';

    protected $description = 'Gera versões EN retroativas para posts PT sem par EN, preservando uuid para hreflang';

    public function handle(): int
    {
        $write = (bool) $this->option('write');
        $limit = max(1, (int) $this->option('limit'));
        $fromDate = (string) ($this->option('from-date') ?? '');
        $sleepMs = max(0, (int) $this->option('sleep-ms'));

        $this->info($write ? 'Modo WRITE: posts EN serão salvos.' : 'Modo DRY-RUN: nada será salvo.');

        $hasUuidColumn = Schema::hasColumn('posts', 'uuid');
        if (!$hasUuidColumn) {
            $this->warn('Coluna posts.uuid não encontrada. Rodando em modo fallback sem pareamento por uuid.');
        }

        $query = Post::query()
            ->where('lang', 'pt')
            ->where('is_active', true)
            ->orderByDesc('publish_date')
            ->orderByDesc('id');

        if ($fromDate !== '') {
            $query->whereDate('publish_date', '>=', $fromDate);
        }

        $ptPosts = $query->limit($limit)->get();

        $processed = 0;
        $created = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($ptPosts as $pt) {
            $processed++;

            $paired = $hasUuidColumn
                ? Post::query()
                    ->where('lang', 'en')
                    ->where('uuid', (string) $pt->uuid)
                    ->exists()
                : Post::query()
                    ->where('lang', 'en')
                    ->where(function ($q) use ($pt) {
                        $q->where('slug', (string) $pt->slug)
                            ->orWhere('slug', Str::slug((string) $pt->title));
                    })
                    ->exists();

            if ($paired) {
                $skipped++;
                $this->line("SKIP {$this->postLabel($pt)}: já possui EN pareado.");
                continue;
            }

            try {
                $translated = $this->translateToEnglish($pt);
                $enSlug = $this->uniqueSlug(Str::slug((string) ($translated['slug'] ?? '')));
                $categoryIdEn = $this->resolveEnglishCategoryId($pt);

                $this->line("OK {$this->postLabel($pt)} -> EN slug={$enSlug}");

                if ($write) {
                    $en = new Post();
                    $en->title = (string) ($translated['title'] ?? 'Catholic Reflection');
                    $en->slug = $enSlug;
                    $en->keywords = implode(', ', $translated['keywords'] ?? []);
                    $en->meta_description = (string) ($translated['meta_description'] ?? '');
                    $en->cover_image_url = $pt->cover_image_url;
                    $en->content = (string) ($translated['content_html'] ?? '');
                    $en->category_id = $categoryIdEn;
                    $en->is_active = true;
                    $en->is_featured = (bool) $pt->is_featured;
                    $en->publish_date = $pt->publish_date;
                    $en->expiry_date = $pt->expiry_date;
                    $en->lang = 'en';
                    if ($hasUuidColumn) {
                        $en->uuid = (string) $pt->uuid;
                    }
                    $en->save();
                }

                $created++;
            } catch (\Throwable $e) {
                $errors++;
                $this->error("ERRO {$this->postLabel($pt)}: {$e->getMessage()}");
            }

            if ($sleepMs > 0) {
                usleep($sleepMs * 1000);
            }
        }

        $this->newLine();
        $this->info('Resumo:');
        $this->line("- PT processados: {$processed}");
        $this->line("- EN criados: {$created}");
        $this->line("- Pulados: {$skipped}");
        $this->line("- Erros: {$errors}");

        if (!$write) {
            $this->comment('Execute novamente com --write para persistir.');
        }

        return self::SUCCESS;
    }

    private function translateToEnglish(Post $pt): array
    {
        $apiKey = trim((string) env('OPENAI_API_KEY', ''));
        if ($apiKey === '') {
            throw new \RuntimeException('OPENAI_API_KEY não configurada.');
        }

        $model = trim((string) env('OPENAI_MODEL_TRANSLATOR', env('OPENAI_MODEL', 'gpt-4.1-mini')));
        $timeout = (int) env('OPENAI_TIMEOUT', 180);

        $prompt = $this->buildTranslationPrompt($pt);

        $res = Http::timeout($timeout)
            ->withToken($apiKey)
            ->acceptJson()
            ->asJson()
            ->post('https://api.openai.com/v1/responses', [
                'model' => $model,
                'input' => [
                    [
                        'role' => 'user',
                        'content' => [
                            ['type' => 'input_text', 'text' => $prompt],
                        ],
                    ],
                ],
                'text' => [
                    'format' => ['type' => 'text'],
                ],
                'max_output_tokens' => 2500,
            ]);

        if (!$res->ok()) {
            throw new \RuntimeException("OpenAI HTTP {$res->status()}: {$res->body()}");
        }

        $json = $res->json() ?: [];
        $text = '';

        foreach (($json['output'] ?? []) as $item) {
            if (($item['type'] ?? '') !== 'message') {
                continue;
            }
            foreach (($item['content'] ?? []) as $chunk) {
                if (($chunk['type'] ?? '') === 'output_text') {
                    $text .= (string) ($chunk['text'] ?? '');
                }
            }
        }

        $payload = $this->decodeJsonPayload($text);

        $contentHtml = trim((string) ($payload['content_html'] ?? ''));
        if ($contentHtml === '') {
            throw new \RuntimeException('content_html vazio no retorno da IA.');
        }

        $keywords = $payload['keywords'] ?? [];
        if (!is_array($keywords)) {
            $keywords = [];
        }

        $keywords = array_values(array_filter(array_map(
            fn ($k) => trim((string) $k),
            $keywords
        )));

        return [
            'title' => trim((string) ($payload['title'] ?? 'Catholic Reflection')),
            'slug' => trim((string) ($payload['slug'] ?? 'catholic-reflection')),
            'meta_description' => trim((string) ($payload['meta_description'] ?? '')),
            'keywords' => $keywords,
            'content_html' => $contentHtml,
        ];
    }

    private function buildTranslationPrompt(Post $pt): string
    {
        $title = (string) ($pt->title ?? '');
        $slug = (string) ($pt->slug ?? '');
        $meta = (string) ($pt->meta_description ?? '');
        $keywords = (string) ($pt->keywords ?? '');
        $html = (string) ($pt->content ?? '');

        return <<<PROMPT
You are an expert Catholic content editor for IA Tio Ben.

Translate and adapt a Portuguese Catholic blog post into English with excellent readability and SEO.

Requirements:
1) Keep the theological meaning faithful to Catholic teaching.
2) Preserve HTML structure (headings, paragraphs, lists, links, blockquotes) and return valid HTML in content_html.
3) Keep links exactly as they are.
4) Produce natural English title and slug.
5) Return ONLY valid JSON with this exact shape:
{
  "title": "string",
  "slug": "string-kebab-case",
  "meta_description": "max 160 chars",
  "keywords": ["kw1","kw2","kw3","kw4","kw5","kw6"],
  "content_html": "<article>...</article or plain html fragment>"
}
6) Do not include markdown fences or extra commentary.

INPUT:
PT_TITLE: {$title}
PT_SLUG: {$slug}
PT_META: {$meta}
PT_KEYWORDS: {$keywords}
PT_CONTENT_HTML:
{$html}
PROMPT;
    }

    private function decodeJsonPayload(string $raw): array
    {
        $text = trim($raw);
        if ($text === '') {
            throw new \RuntimeException('IA retornou vazio.');
        }

        $text = preg_replace('/^\s*```json\s*/i', '', $text) ?? $text;
        $text = preg_replace('/\s*```\s*$/', '', $text) ?? $text;

        $decoded = json_decode($text, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        if (preg_match('/\{[\s\S]*\}/', $text, $m)) {
            $decoded = json_decode((string) $m[0], true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        throw new \RuntimeException('Não foi possível decodificar JSON do retorno da IA.');
    }

    private function resolveEnglishCategoryId(Post $pt): int
    {
        $ptName = (string) optional($pt->category)->name;
        $enName = match (Str::lower($ptName)) {
            'santos' => 'Saints',
            'reflexões', 'reflexoes' => 'Reflections',
            default => 'Reflections',
        };

        $existing = Category::query()->where('name', $enName)->value('id');
        if ($existing) {
            return (int) $existing;
        }

        $new = new Category();
        $new->name = $enName;
        $new->save();

        return (int) $new->id;
    }

    private function uniqueSlug(string $base): string
    {
        $base = trim($base);
        if ($base === '') {
            $base = 'catholic-reflection';
        }

        $slug = $base;
        $i = 2;
        while (Post::query()->where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
            if ($i > 5000) {
                break;
            }
        }

        return $slug;
    }

    private function postLabel(Post $post): string
    {
        $rawId = (string) ($post->getRawOriginal('id') ?? '');
        $attrId = (string) ($post->getAttribute('id') ?? '');
        $slug = (string) ($post->slug ?? '');

        $id = $rawId !== '' ? $rawId : ($attrId !== '' ? $attrId : 'sem-id');
        $slugPart = $slug !== '' ? " slug={$slug}" : '';

        return "PT id={$id}{$slugPart}";
    }
}
