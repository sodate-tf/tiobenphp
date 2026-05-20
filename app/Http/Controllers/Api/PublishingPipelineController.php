<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\PipelineArticle;
use App\Models\Post;
use App\Services\ArticleFormatter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Jobs\GenerateDiscoverCoverJob;
use App\Services\IndexNowService;

// ✅ se esse Job existir no seu projeto, garanta o import correto
// use App\Jobs\GenerateDiscoverCoverJob;

class PublishingPipelineController extends Controller
{
    private const MIN_ARTICLE_WORDS = 1100;
    private const MIN_H2_SECTIONS = 4;
    private const MIN_FAQ_QUESTIONS = 3;

    /**
     * ✅ PROMPT (JORNALISTA JOVEM / STORYTELLING / BLOCOS [liturgia] [terco] [SEO])
     */
    private const YOUNG_LITURGY_WRITER_PROMPT = <<<PROMPT
Você é Tio Ben, jornalista católico contemporâneo e colaborador do Blog IA Tio Ben.

Seu estilo é:

• humano, acolhedor, pastoral  
• fiel ao ensinamento da Igreja Católica  
• jovem no espírito, mas maduro na linguagem  
• leve e acessível, sem linguagem acadêmica  
• natural, sem exagero de gírias ou analogias  

Evite:
- gírias forçadas
- excesso de comparações com cultura pop
- tom caricatural ou performático
- linguagem infantilizada

Use analogias apenas quando forem orgânicas ao tema.
Use emojis com muita moderação (máx. 1 por seção, nunca em excesso).
Nunca faça homilia direta ou explicação técnica bíblica.
Aprofunde o tema através de narrativas humanas e situações reais.

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
MISSÃO (FOCO PRINCIPAL)
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Recebendo [LITURGIA] e [KEYWORDS], escreva imediatamente um artigo completo (1500–2000 palavras) que:

• Identifique o tema central unificador das leituras  
• Desenvolva esse tema aplicando à vida cotidiana contemporânea  
• Trabalhe profundidade espiritual com linguagem clara  
• Use storytelling natural, sem exageros  

Referências bíblicas devem ser sutis e orgânicas.
Não cite capítulos ou versículos.

Tamanho ideal: 1700–1900 palavras.

Retorne SOMENTE o texto final em MARKDOWN.
Não explique nada.
Não peça confirmação.
Não envolva em blocos de código.

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
ENTRADA
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
[LITURGIA]
📅 DD/MM/YYYY
1ª Leitura: ...
Salmo: ...
Evangelho: ...
[/LITURGIA]

[KEYWORDS] ... [/KEYWORDS]

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
ESTRUTURA DO ARTIGO
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

# Título envolvente e reflexivo  
Por Tio Ben, Blog IA Tio Ben

Introdução:
Comece com uma situação real ou pergunta existencial.
Conecte naturalmente com o tema central.

Desenvolvimento:
• 4 a 6 seções com subtítulos claros  
• Storytelling principal (600–900 palavras)  
• Reflexões práticas e aplicáveis  
• 6–10 aplicações concretas para a vida  

FAQs:
Inclua 3–5 perguntas reais que as pessoas fariam sobre o tema.

Fundamentação católica:
Inclua um bloco curto com base em:
- Catecismo da Igreja Católica (sem inventar número)
- Tradição e vida sacramental
- Exemplo de santo(s) quando fizer sentido

Conclusão:
Encerramento pastoral forte, convidando à ação interior.

CTA final obrigatório:
"Bora mergulhar nas leituras de hoje? Acesse:
https://www.iatioben.com.br/liturgia-diaria/[DATA-FORMATO-DD-MM-YYYY]
e continue essa jornada. 🙏"

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
BLOCO FIXO — LITURGIA DO DIA
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
[liturgia]
Texto breve (2–4 linhas) convidando à escuta diária da Palavra.

Inclua:
https://www.iatioben.com.br/liturgia-diaria/[DATA-FORMATO-DD-MM-YYYY]
[/liturgia]

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
BLOCO FIXO — TERÇO DO DIA
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
[terco]
Convite simples e reverente ao Santo Terço.

Inclua:
https://www.iatioben.com.br/santo-terco
[/terco]

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
BLOCO FINAL — SEO
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
[SEO]
{
"keywords": ["6-8 palavras-chave naturais integrando [KEYWORDS]"],
"metaDescription": "Frase pastoral clara, até 160 caracteres."
}
[/SEO]

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
DIRETRIZES DE QUALIDADE
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

• Tom equilibrado: jovem + respeitoso  
• Linguagem natural, sem teatralidade  
• Profundidade espiritual sem moralismo  
• Clareza > criatividade exagerada  
• Acessível para qualquer idade  

Entregue SOMENTE o Markdown final.
PROMPT;

    /**
     * ✅ PROMPT — TRADUTOR (PT->EN) mantendo MARKDOWN e blocos
     */
    private const EN_TRANSLATOR_PROMPT = <<<PROMPT
You are a professional Catholic editor and translator for the "IA Tio Ben" blog.

TASK:
Translate the provided Portuguese content into natural, modern English, suitable for a Catholic spirituality website.

INPUTS:
- Portuguese title, slug, keywords, metaDescription
- Full article in Markdown (including required blocks like [liturgia], [terco], [SEO])

RULES (STRICT):
1) Output ONLY the final translated Markdown. No explanations.
2) Preserve Markdown structure, headings, lists, emphasis, and links.
3) Preserve the block tags exactly: [liturgia] [/liturgia], [terco] [/terco], [SEO] [/SEO]
   - Inside those blocks, translate the text naturally, but KEEP the URLs exactly as provided.
4) Keep the CTA link to the liturgy page as-is (Portuguese URL is OK).
5) Translate/Adapt the title to English (keep it engaging).
6) Generate a NEW [SEO] JSON in English:
   {
     "keywords": ["6-8 English keywords (include translated focus keywords if possible)"],
     "metaDescription": "Pastoral sentence in English, <= 160 chars."
   }
7) DO NOT wrap the whole output inside code fences (no ``` or ```markdown).

OUTPUT FORMAT:
# <English Title>
By Tio Ben, IA Tio Ben Blog

<...full translated markdown...>

[SEO]
{ ...json... }
[/SEO]

Return ONLY the markdown.
PROMPT;

    /**
     * POST /api/generate-article
     */
    public function generateArticle(Request $request)
    {
        try {
            $data = $request->json()->all() ?: [];

            $topic         = (string) ($data['topic'] ?? '');
            $agent         = (string) ($data['agent'] ?? 'theme');
            $language      = (string) ($data['language'] ?? 'pt-BR');
            $focusKeywords = (string) ($data['focusKeywords'] ?? '');

            $article = new PipelineArticle();
            $article->topic          = $topic;
            $article->agent          = $agent ?: 'theme';
            $article->language       = $language ?: 'pt-BR';
            $article->focus_keywords = $focusKeywords ?: null;

            if (!empty($data['date'])) {
                $article->date = Carbon::parse($data['date'])->toDateString();
            }

            $article->source_text    = $data['sourceText'] ?? null;
            $article->liturgy_source = $data['liturgySource'] ?? null;

            // ✅ Gera e valida qualidade mínima antes de salvar
            $article->content_raw = $this->generateHighQualityMarkdown($article);

            $article->save();

            return response()->json([
                'success' => true,
                'id'      => $article->id,
            ], 200);

        } catch (\Throwable $e) {
            Log::error('generateArticle failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar artigo via IA.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/format-article
     */
    public function formatArticle(Request $request, ArticleFormatter $formatter)
    {
        try {
            $id = (string) ($request->json('id') ?? '');
            $article = PipelineArticle::find($id);

            if (!$article) {
                return response()->json(['success' => false, 'message' => 'ID não encontrado.'], 200);
            }

            $rawMd = (string) ($article->content_raw ?? '');
            if (trim($rawMd) === '') {
                return response()->json(['success' => false, 'message' => 'Artigo sem content_raw.'], 200);
            }

            // ✅ sanitiza antes de formatar (defensivo)
            $rawMd = $this->sanitizeAiMarkdown($rawMd);

            $article->content_html = $formatter->formatArticleToHtml($rawMd);
            $article->save();

            return response()->json(['success' => true], 200);

        } catch (\Throwable $e) {
            Log::error('formatArticle failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao formatar artigo.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/seo-and-publish
     */
   public function seoAndPublish(Request $request, ArticleFormatter $formatter, IndexNowService $indexNow)
   {
    try {
        $id = (string) ($request->json('id') ?? '');
        $article = PipelineArticle::find($id);

        if (!$article) {
            return response()->json(['success' => false, 'message' => 'ID não encontrado.'], 200);
        }

        $rawMd = (string) ($article->content_raw ?? '');
        if (trim($rawMd) === '') {
            return response()->json(['success' => false, 'message' => 'Artigo sem content_raw.'], 200);
        }

        // ✅ sanitiza antes de tudo (evita publicar quebrado)
        $rawMd = $this->sanitizeAiMarkdown($rawMd);
        $quality = $this->qualityScore($rawMd);
        if (($quality['approved'] ?? false) !== true) {
            Log::warning('seoAndPublish bloqueado por qualidade mínima', [
                'article_id' => $article->id,
                'topic' => (string) $article->topic,
                'score' => $quality,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Artigo abaixo do mínimo de qualidade. Gere novamente antes de publicar.',
                'quality' => $quality,
            ], 422);
        }

        // ✅ SEO do bloco [SEO] com fallback
        $seo = $formatter->analyzeSeoAndExtractMetadata(
            $rawMd,
            (string) ($article->focus_keywords ?? '')
        );

        $keywordsArr = is_array($seo['keywords'] ?? null) ? $seo['keywords'] : [];
        $meta = (string) ($seo['metaDescription'] ?? '');

        // ✅ título (do H1 do markdown) com fallback pro topic
        $title = $article->title ?: $this->extractH1TitleFromMarkdown($rawMd) ?: $this->buildTitle($article);

        // slug único
        $slug  = $article->slug ?: Str::slug($title);
        $slug  = $this->uniqueSlug($slug);

        // salva meta no pipeline
        $article->title = $title;
        $article->slug  = $slug;
        $article->meta_description = $meta ?: $this->buildMetaDescription($article);
        $article->keywords = implode(', ', array_map('strval', $keywordsArr));
        $article->content_raw = $rawMd; // guarda sanitizado
        $article->save();

        // idioma (pt/en)
        $lang = 'pt';
        $rawLang = strtolower((string) ($article->language ?? 'pt-br'));
        if (str_starts_with($rawLang, 'en')) $lang = 'en';

        // categoria por agent
        $categoryName = $article->agent === 'saint' ? 'Santos' : 'Reflexões';
        $categoryId = Category::where('name', $categoryName)->value('id');

        if (!$categoryId) {
            $cat = new Category();
            $cat->name = $categoryName;
            $cat->save();
            $categoryId = $cat->id;
        }

        // ✅ HTML final: usa o já formatado; se não existir, formata agora
        $html = (string) ($article->content_html ?? '');
        if (trim($html) === '') {
            $html = $formatter->formatArticleToHtml($rawMd);
        }

        // ✅ guard rail: não publique se ainda estiver embrulhado como code block
        if (str_contains($html, '<code class="language-markdown">')) {
            $html = $formatter->formatArticleToHtml($rawMd);
            if (str_contains($html, '<code class="language-markdown">')) {
                throw new \RuntimeException('HTML ainda contém <code class="language-markdown">. Verifique sanitize/IA output.');
            }
        }

        // publicar no Post (PT)
        $post = new Post();
        $post->title = $title;
        $post->slug  = $slug;
        $post->content = $html;

        $post->category_id      = $categoryId;
        $post->keywords         = implode(', ', array_map('strval', $keywordsArr));
        $post->meta_description = $article->meta_description ?? $meta;
        $post->cover_image_url  = $article->cover_image_url ?: null;

        $post->is_active = true;

        $publishAt = $article->date ? Carbon::parse($article->date)->startOfDay() : now();
        $post->publish_date = $publishAt;
        $post->expiry_date = null;
        $post->lang = $lang;

        $post->uuid = (string) Str::uuid();
        $post->save();

        // ✅ CORREÇÃO: volta a gerar capa do PT
        GenerateDiscoverCoverJob::dispatch($post->id);

        $article->published_at = now();
        $article->content_html = $html;
        $article->save();

        // publica EN (e o método corrigido despacha capa EN)
        $postEn = $this->publishEnglishVersion($article, $formatter, $rawMd, $seo, $post);

        $siteUrl = rtrim((string) config('app.url', env('APP_URL', 'https://www.iatioben.com.br')), '/');

        $indexNowUrls = [
            "{$siteUrl}/blog/{$post->slug}",
            "{$siteUrl}/en/blog/{$postEn->slug}",
        ];

        $indexNow->submit($indexNowUrls);

        return response()->json([
            'success' => true,
            'message' => 'Publicado com sucesso (PT + EN).',
            'post' => [
                'pt' => ['id' => $post->id, 'slug' => $post->slug],
                'en' => ['id' => $postEn->id, 'slug' => $postEn->slug],
            ],
        ], 200);

    } catch (\Throwable $e) {
        Log::error('seoAndPublish failed', ['error' => $e->getMessage()]);
        return response()->json([
            'success' => false,
            'message' => 'Erro ao publicar artigo.',
            'details' => $e->getMessage(),
        ], 500);
    }
}

    // ---------------------------------------------------------------------
    // ✅ AGENTE VIA OPENAI (Responses API)
    // ---------------------------------------------------------------------

    private function runAgentViaOpenAI(PipelineArticle $a): string
    {
        $apiKey = trim((string) env('OPENAI_API_KEY', ''));
        if ($apiKey === '') {
            throw new \RuntimeException('OPENAI_API_KEY não configurada no .env.');
        }

        $model = trim((string) env('OPENAI_MODEL', 'gpt-4.1-mini'));
        $timeout = (int) env('OPENAI_TIMEOUT', 180);

        $prompt = $this->buildYoungLiturgyPrompt($a);

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
            throw new \RuntimeException("OpenAI retornou HTTP {$res->status()}: {$res->body()}");
        }

        $json = $res->json() ?: [];

        $text = '';
        foreach (($json['output'] ?? []) as $item) {
            if (($item['type'] ?? '') !== 'message') continue;
            foreach (($item['content'] ?? []) as $c) {
                if (($c['type'] ?? '') === 'output_text') {
                    $text .= (string) ($c['text'] ?? '');
                }
            }
        }

        $text = trim($text);
        if ($text === '') {
            throw new \RuntimeException('OpenAI retornou texto vazio.');
        }

        if (!str_contains($text, '[liturgia]') || !str_contains($text, '[/liturgia]')) {
            throw new \RuntimeException('IA retornou sem bloco obrigatório [liturgia].');
        }
        if (!str_contains($text, '[terco]') || !str_contains($text, '[/terco]')) {
            throw new \RuntimeException('IA retornou sem bloco obrigatório [terco].');
        }
        if (!str_contains($text, '[SEO]') || !str_contains($text, '[/SEO]')) {
            throw new \RuntimeException('IA retornou sem bloco obrigatório [SEO].');
        }

        return $text;
    }

    private function generateHighQualityMarkdown(PipelineArticle $article): string
    {
        $raw = $this->runAgentViaOpenAI($article);
        $sanitized = $this->sanitizeAiMarkdown($raw);
        $firstScore = $this->qualityScore($sanitized);

        Log::info('Pipeline quality gate: tentativa 1', [
            'topic' => (string) $article->topic,
            'agent' => (string) $article->agent,
            'score' => $firstScore,
        ]);

        if (($firstScore['approved'] ?? false) === true) {
            return $sanitized;
        }

        Log::warning('Pipeline quality gate: primeira geração abaixo do mínimo, tentando novamente.', [
            'topic' => (string) $article->topic,
            'agent' => (string) $article->agent,
        ]);

        $retry = $this->runAgentViaOpenAI($article);
        $retrySanitized = $this->sanitizeAiMarkdown($retry);
        $retryScore = $this->qualityScore($retrySanitized);

        Log::info('Pipeline quality gate: tentativa 2', [
            'topic' => (string) $article->topic,
            'agent' => (string) $article->agent,
            'score' => $retryScore,
        ]);

        return $retrySanitized;
    }

    private function qualityScore(string $markdown): array
    {
        $clean = trim($markdown);
        $withoutSeo = preg_replace('/\[SEO\][\s\S]*?\[\/SEO\]/i', '', $clean) ?? $clean;
        $wordCount = str_word_count(strip_tags($withoutSeo));
        $h2Count = preg_match_all('/^##\s+/m', $withoutSeo);
        $faqQuestions = preg_match_all('/^\s*[-*]\s*.*\?\s*$/m', $withoutSeo);
        $hasLiturgia = str_contains($clean, '[liturgia]');
        $hasTerco = str_contains($clean, '[terco]');
        $hasSeo = str_contains($clean, '[SEO]');

        $approved = $wordCount >= self::MIN_ARTICLE_WORDS
            && $h2Count >= self::MIN_H2_SECTIONS
            && $faqQuestions >= self::MIN_FAQ_QUESTIONS
            && $hasLiturgia
            && $hasTerco
            && $hasSeo;

        return [
            'approved' => $approved,
            'word_count' => $wordCount,
            'h2_count' => (int) $h2Count,
            'faq_questions' => (int) $faqQuestions,
            'has_liturgia_block' => $hasLiturgia,
            'has_terco_block' => $hasTerco,
            'has_seo_block' => $hasSeo,
            'thresholds' => [
                'min_words' => self::MIN_ARTICLE_WORDS,
                'min_h2' => self::MIN_H2_SECTIONS,
                'min_faq_questions' => self::MIN_FAQ_QUESTIONS,
            ],
        ];
    }

    private function passesQualityGate(string $markdown): bool
    {
        return (bool) ($this->qualityScore($markdown)['approved'] ?? false);
    }

    private function runTranslatorViaOpenAI(array $payload): string
    {
        $apiKey = trim((string) env('OPENAI_API_KEY', ''));
        if ($apiKey === '') {
            throw new \RuntimeException('OPENAI_API_KEY não configurada no .env.');
        }

        $model = trim((string) env('OPENAI_MODEL_TRANSLATOR', env('OPENAI_MODEL', 'gpt-4.1-mini')));
        $timeout = (int) env('OPENAI_TIMEOUT', 180);

        $prompt =
            self::EN_TRANSLATOR_PROMPT
            . "\n\n"
            . "PORTUGUESE TITLE:\n" . ($payload['title'] ?? '') . "\n\n"
            . "PORTUGUESE SLUG:\n" . ($payload['slug'] ?? '') . "\n\n"
            . "PORTUGUESE KEYWORDS:\n" . ($payload['keywords'] ?? '') . "\n\n"
            . "PORTUGUESE META DESCRIPTION:\n" . ($payload['metaDescription'] ?? '') . "\n\n"
            . "PORTUGUESE MARKDOWN ARTICLE:\n"
            . ($payload['markdown'] ?? '');

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
            throw new \RuntimeException("OpenAI retornou HTTP {$res->status()}: {$res->body()}");
        }

        $json = $res->json() ?: [];

        $text = '';
        foreach (($json['output'] ?? []) as $item) {
            if (($item['type'] ?? '') !== 'message') continue;
            foreach (($item['content'] ?? []) as $c) {
                if (($c['type'] ?? '') === 'output_text') {
                    $text .= (string) ($c['text'] ?? '');
                }
            }
        }

        $text = trim($text);
        if ($text === '') {
            throw new \RuntimeException('Translator retornou texto vazio.');
        }

        if (!str_contains($text, '[liturgia]') || !str_contains($text, '[/liturgia]')) {
            throw new \RuntimeException('Translator retornou sem bloco obrigatório [liturgia].');
        }
        if (!str_contains($text, '[terco]') || !str_contains($text, '[/terco]')) {
            throw new \RuntimeException('Translator retornou sem bloco obrigatório [terco].');
        }
        if (!str_contains($text, '[SEO]') || !str_contains($text, '[/SEO]')) {
            throw new \RuntimeException('Translator retornou sem bloco obrigatório [SEO].');
        }

        return $text;
    }

    private function buildYoungLiturgyPrompt(PipelineArticle $a): string
    {
        $date = $a->date ? Carbon::parse($a->date) : now();
        $ddmmyyyy   = $date->format('d/m/Y');
        $dd_mm_yyyy = $date->format('d-m-Y');

        $liturgy = trim((string) ($a->liturgy_source ?? ''));
        if ($liturgy === '') {
            $liturgy = "📅 {$ddmmyyyy}\n1ª Leitura: (não informada)\nSalmo: (não informado)\nEvangelho: (não informado)";
        }

        $keywords = trim((string) ($a->focus_keywords ?? ''));
        if ($keywords === '') {
            $keywords = trim((string) ($a->topic ?? ''));
        }

        $base = str_replace('[DATA-FORMATO-DD-MM-YYYY]', $dd_mm_yyyy, self::YOUNG_LITURGY_WRITER_PROMPT);

        return $base . "\n\n"
            . "[LITURGIA]\n{$liturgy}\n[/LITURGIA]\n\n"
            . "[KEYWORDS]\n{$keywords}\n[/KEYWORDS]\n";
    }

    // ---------------------------------------------------------------------
    // ✅ helpers
    // ---------------------------------------------------------------------

    /**
     * ✅ impede que o output venha embrulhado em ```markdown ... ```
     */
    private function sanitizeAiMarkdown(string $text): string
    {
        $s = trim((string)$text);
        if ($s === '') return $s;

        if (preg_match('/^\s*```[a-zA-Z0-9_-]*\s*\n([\s\S]*?)\n```\s*$/', $s, $m)) {
            $s = trim((string)$m[1]);
        }

        $s = (string)preg_replace('/^\s*```[a-zA-Z0-9_-]*\s*\n/', '', $s);
        $s = (string)preg_replace('/\n```\s*$/', '', $s);

        return trim($s);
    }

    private function publishEnglishVersion(
    PipelineArticle $article,
    ArticleFormatter $formatter,
    string $rawMdPt,
    array $seoPt,
    Post $postPt
): Post {
    // 1) Traduz markdown completo
    $translatedMd = $this->runTranslatorViaOpenAI([
        'title' => $article->title ?? $this->extractH1TitleFromMarkdown($rawMdPt) ?? '',
        'slug'  => $article->slug ?? $postPt->slug ?? '',
        'keywords' => $article->keywords ?? implode(', ', (array) ($seoPt['keywords'] ?? [])),
        'metaDescription' => $article->meta_description ?? (string) ($seoPt['metaDescription'] ?? ''),
        'markdown' => $rawMdPt,
    ]);

    // 2) Extrai SEO do [SEO] traduzido (ou fallback)
    $seoEn = $formatter->analyzeSeoAndExtractMetadata(
        $translatedMd,
        (string) ($article->focus_keywords ?? '')
    );

    $keywordsArrEn = is_array($seoEn['keywords'] ?? null) ? $seoEn['keywords'] : [];
    $metaEn = (string) ($seoEn['metaDescription'] ?? '');

    // 3) Título EN do H1
    $titleEn = $this->extractH1TitleFromMarkdown($translatedMd);
    if (trim($titleEn) === '') {
        $titleEn = ($article->title ? ($article->title . ' (EN)') : 'Article');
    }

    // 4) Slug EN (ideal: slug do título EN; fallback: slug PT + "-en")
    $baseSlugEn = Str::slug($titleEn);
    if ($baseSlugEn === '') {
        $baseSlugEn = Str::slug(($postPt->slug ?? 'post') . '-en');
    }
    $slugEn = $this->uniqueSlug($baseSlugEn);

    // 5) Formata HTML EN
    $htmlEn = $formatter->formatArticleToHtml($translatedMd);

    // 6) Categoria EN
    $categoryNameEn = $article->agent === 'saint' ? 'Saints' : 'Reflections';
    $categoryIdEn = Category::where('name', $categoryNameEn)->value('id');
    if (!$categoryIdEn) {
        $cat = new Category();
        $cat->name = $categoryNameEn;
        $cat->save();
        $categoryIdEn = $cat->id;
    }

    // 7) Publica Post EN
    $postEn = new Post();
    $postEn->title = $titleEn;
    $postEn->slug  = $slugEn;
    $postEn->content = $htmlEn;

    $postEn->category_id      = $categoryIdEn;
    $postEn->keywords         = implode(', ', array_map('strval', $keywordsArrEn));
    $postEn->meta_description = $metaEn !== '' ? $metaEn : 'Catholic reflection on today’s theme at IA Tio Ben.';
    $postEn->cover_image_url  = $article->cover_image_url ?: null;

    $postEn->is_active = true;

    // mesma data do PT
    $publishAt = $article->date ? Carbon::parse($article->date)->startOfDay() : now();
    $postEn->publish_date = $publishAt;
    $postEn->expiry_date = null;
    $postEn->lang = 'en';

    // Mantém o mesmo uuid para parear PT<->EN de forma confiável.
    $postEn->uuid = (string) ($postPt->uuid ?: Str::uuid());
    $postEn->save();

    // ✅ CORREÇÃO: despacha capa para o post EN recém criado
    GenerateDiscoverCoverJob::dispatch($postEn->id);

    return $postEn;
}

    private function extractH1TitleFromMarkdown(string $md): string
    {
        $md = str_replace(["\r\n", "\r"], "\n", (string)$md);
        foreach (explode("\n", $md) as $line) {
            if (preg_match('/^#\s+(.+)\s*$/', trim($line), $m)) {
                return trim((string)($m[1] ?? ''));
            }
        }
        return '';
    }

    private function buildTitle(PipelineArticle $a): string
    {
        return $a->topic ?: 'Artigo';
    }

    private function buildMetaDescription(PipelineArticle $a): string
    {
        $t = trim((string) $a->topic);
        if ($t === '') $t = 'tema de hoje';
        return "Reflexão católica sobre {$t} no IA Tio Ben.";
    }

    private function uniqueSlug(string $base): string
    {
        $base = Str::slug($base);
        if ($base === '') $base = 'post-' . Str::random(8);

        $slug = $base;
        $i = 2;

        while (Post::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i;
            $i++;
            if ($i > 5000) break;
        }

        return $slug;
    }
}
