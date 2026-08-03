<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DiscoverImageService
{
    private const FINAL_W = 1200;
    private const FINAL_H = 675;

    private const IMAGE_MODEL = 'gpt-image-1-mini';
    private const TEXT_MODEL = 'gpt-5-mini';

    private const QUALITY = 'low';
    private const GEN_SIZE = '1536x1024';

    private const OUTPUT_FORMAT = 'webp';
    private const OUTPUT_COMPRESSION = 72;

    private const SAFE_MARGIN_PCT = 0.08;

    private function fontPlayfair(): ?string
    {
        $p = storage_path('app/fonts/PlayfairDisplay-SemiBold.ttf');
        return is_file($p) ? $p : null;
    }

    private function fontInter(): ?string
    {
        $p = storage_path('app/fonts/Inter-SemiBold.ttf');
        return is_file($p) ? $p : null;
    }

    public function generate(Post $post): ?string
    {
        $apiKey = trim((string) config('services.openai.key'));

        if ($apiKey === '') {
            Log::warning('DiscoverImageService: missing services.openai.key');
            return null;
        }

        $baseUri = rtrim((string) config('services.openai.base_uri', 'https://api.openai.com'), '/');

        $lang = $this->detectLang($post);

        $layout = $this->pick([
            'left-aligned',
            'split-layout',
            'centered',
            'bottom-editorial',
        ]);

        $palette = $this->pick([
            ['base' => '#F8FAFC', 'accent' => '#D4AF37', 'mood' => 'bright airy minimal'],
            ['base' => '#FFF7ED', 'accent' => '#38BDF8', 'mood' => 'sunlit friendly modern'],
            ['base' => '#FFFFFF', 'accent' => '#F59E0B', 'mood' => 'warm daylight optimistic'],
            ['base' => '#EFF6FF', 'accent' => '#C6A75E', 'mood' => 'fresh calm luminous'],
        ]);

        $accentEffect = $this->pick([
            'soft sun flare',
            'gentle pastel glow',
            'subtle warm highlights',
            'clean bright vignette, very subtle',
        ]);

        [$title, $subtitle, $cta] = $this->buildOverlayText($post, $lang);

        $visualBrief = $this->buildTioBenIllustrationBrief($post);

        $prompt = $this->buildNoTextPrompt(
            post: $post,
            lang: $lang,
            layout: $layout,
            base: $palette['base'],
            accent: $palette['accent'],
            mood: (string) ($palette['mood'] ?? 'bright airy'),
            accentEffect: $accentEffect,
            visualBrief: $visualBrief
        );

        $res = Http::withToken($apiKey)
            ->acceptJson()
            ->asJson()
            ->timeout(120)
            ->post($baseUri . '/v1/images/generations', [
                'model' => config('services.openai.image_model', self::IMAGE_MODEL),
                'prompt' => $prompt,
                'size' => self::GEN_SIZE,
                'quality' => self::QUALITY,
                'background' => 'opaque',
                'output_format' => self::OUTPUT_FORMAT,
                'output_compression' => self::OUTPUT_COMPRESSION,
                'n' => 1,
            ]);

        if (!$res->successful()) {
            Log::warning('DiscoverImageService: images/generations failed', [
                'status' => $res->status(),
                'body' => $this->safeBody($res->body() ?: ''),
                'post_id' => $post->id,
                'post_slug' => $post->slug,
            ]);

            return null;
        }

        $b64 = (string) ($res->json('data.0.b64_json') ?? '');

        if ($b64 === '') {
            Log::warning('DiscoverImageService: missing data.0.b64_json', [
                'post_id' => $post->id,
                'post_slug' => $post->slug,
            ]);

            return null;
        }

        $rawBinary = base64_decode($b64, true);

        if ($rawBinary === false) {
            Log::warning('DiscoverImageService: base64 decode failed', [
                'post_id' => $post->id,
                'post_slug' => $post->slug,
            ]);

            return null;
        }

        $tmpDir = storage_path('app/tmp');

        if (!is_dir($tmpDir)) {
            @mkdir($tmpDir, 0775, true);
        }

        $tmpName = (string) Str::uuid() . '.' . self::OUTPUT_FORMAT;
        $tmpPath = $tmpDir . DIRECTORY_SEPARATOR . $tmpName;

        if (@file_put_contents($tmpPath, $rawBinary) === false) {
            Log::warning('DiscoverImageService: failed writing temp file', [
                'tmpPath' => $tmpPath,
                'post_id' => $post->id,
            ]);

            return null;
        }

        $finalBinary = $this->finalizeToDiscoverWebpWithOverlay(
            sourcePath: $tmpPath,
            layout: $layout,
            base: $palette['base'],
            accent: $palette['accent'],
            title: $title,
            subtitle: $subtitle,
            cta: $cta,
            site: 'iatioben.com.br'
        );

        @unlink($tmpPath);

        $filename = Str::slug($post->slug ?: ('post-' . $post->id)) . '-discover.webp';
        $relPath = 'covers/' . now()->format('Y/m') . '/' . $filename;

        $dir = dirname($relPath);

        if (!Storage::disk('public')->exists($dir)) {
            Storage::disk('public')->makeDirectory($dir);
        }

        $ok = Storage::disk('public')->put($relPath, $finalBinary, [
            'visibility' => 'public',
            'ContentType' => 'image/webp',
            'CacheControl' => 'public, max-age=31536000, immutable',
        ]);

        if (!$ok) {
            Log::error('DiscoverImageService: failed to write final image', [
                'relPath' => $relPath,
                'diskRoot' => (string) config('filesystems.disks.public.root'),
                'post_id' => $post->id,
            ]);

            return null;
        }

        $url = Storage::disk('public')->url($relPath);

        return $url . (str_contains($url, '?') ? '' : ('?v=' . time()));
    }

    private function buildTioBenIllustrationBrief(Post $post): string
    {
        $title = Str::limit(trim((string) ($post->title ?? '')), 180, '');
        $description = Str::limit(trim((string) ($post->meta_description ?? '')), 280, '');

        return "Theme: {$title}. Context: {$description}. MANDATORY STYLE: create one active visual metaphor tied to the theme as a bold, warm, non-photorealistic Catholic editorial illustration. Use the Tio Ben IA visual language: warm gold, sky blue, deep navy, off-white, rounded expressive details, hand-drawn linework, layered textures, friendly digital mentor energy and subtle faith symbols. No logo reproduction, character portrait, photorealism or stock-photo look.";
    }

    private function buildAiVisualBrief(
        Post $post,
        string $lang,
        string $apiKey,
        string $baseUri
    ): string {
        $title = trim((string) ($post->title ?? ''));
        $description = trim((string) ($post->meta_description ?? ''));

        $content = $this->cleanArticleTextForPrompt((string) ($post->content ?? ''));
        $content = $this->truncateForPrompt($content, 2800);

        $prompt = $lang === 'en'
            ? $this->visualBriefPromptEn($title, $description, $content)
            : $this->visualBriefPromptPt($title, $description, $content);

        try {
            $res = Http::withToken($apiKey)
                ->acceptJson()
                ->asJson()
                ->timeout(45)
                ->post($baseUri . '/v1/responses', [
                    'model' => config('services.openai.text_model', self::TEXT_MODEL),
                    'input' => $prompt,
                    'max_output_tokens' => 450,
                ]);

            if (!$res->successful()) {
                Log::warning('DiscoverImageService: visual brief failed', [
                    'status' => $res->status(),
                    'body' => $this->safeBody($res->body() ?: ''),
                    'post_id' => $post->id,
                    'post_slug' => $post->slug,
                ]);

                return $this->fallbackVisualBrief($post, $lang);
            }

            $text = trim($this->extractResponsesText($res->json()));

            if ($text === '') {
                return $this->fallbackVisualBrief($post, $lang);
            }

            return $this->truncateForPrompt($text, 1800);
        } catch (\Throwable $e) {
            Log::warning('DiscoverImageService: visual brief exception', [
                'error' => $e->getMessage(),
                'post_id' => $post->id,
                'post_slug' => $post->slug,
            ]);

            return $this->fallbackVisualBrief($post, $lang);
        }
    }

    private function visualBriefPromptPt(string $title, string $description, string $content): string
    {
        return trim("
Você é diretor de arte editorial para um blog católico moderno.

Sua tarefa:
Ler o título, resumo e trecho do artigo e criar um BRIEF VISUAL específico para gerar uma imagem de capa.

Título:
{$title}

Resumo:
{$description}

Trecho do artigo:
{$content}

Regras obrigatórias:
- A imagem precisa representar o tema específico do artigo.
- Não use uma fórmula fixa de Bíblia + vela + terço + cruz.
- Não escolha objetos religiosos genéricos como assunto principal, a menos que o artigo seja especificamente sobre esse objeto.
- Prefira uma cena viva: lugar, gesto, situação humana, caminho, encontro, contraste, ambiente, ação ou símbolo visual forte.
- Pode haver pessoas, mas sem rostos identificáveis.
- Pode haver ambiente católico, mas ele precisa servir ao tema do artigo.
- Se o artigo falar de santo, use símbolos, ambiente, época, missão, virtude ou legado do santo; não gere retrato frontal genérico.
- Se o artigo falar de doutrina, transforme a ideia em uma metáfora visual concreta.
- Se o artigo falar de liturgia, mostre ação litúrgica, comunidade, altar, igreja ou caminho espiritual, não apenas objetos.
- Se o artigo falar de família, amizade, perdão, vocação, esperança ou sofrimento, represente uma situação humana coerente.
- A cena deve funcionar como capa editorial para Google Discover.
- Não sugerir texto dentro da imagem.

Retorne apenas este formato, sem explicações extras:

Tema central:
Cena principal:
Assunto principal:
Ação ou gesto visual:
Primeiro plano:
Fundo:
Símbolo visual:
Emoção:
Composição:
O que evitar:
");
    }

    private function visualBriefPromptEn(string $title, string $description, string $content): string
    {
        return trim("
You are an editorial art director for a modern Catholic blog.

Your task:
Read the article title, summary and excerpt, then create a specific VISUAL BRIEF for a blog cover image.

Title:
{$title}

Summary:
{$description}

Article excerpt:
{$content}

Mandatory rules:
- The image must represent the specific theme of the article.
- Do not use a fixed Bible + candle + rosary + cross formula.
- Do not choose generic religious objects as the main subject unless the article is specifically about that object.
- Prefer a living scene: place, gesture, human situation, journey, encounter, contrast, environment, action or strong visual metaphor.
- People are allowed, but no identifiable faces.
- A Catholic environment is allowed only when it serves the article theme.
- If the article is about a saint, use symbols, setting, era, mission, virtue or legacy; do not generate a generic frontal portrait.
- If the article is about doctrine, transform the idea into a concrete visual metaphor.
- If the article is about liturgy, show liturgical action, community, altar, church or spiritual journey, not only objects.
- If the article is about family, friendship, forgiveness, vocation, hope or suffering, represent a coherent human situation.
- The scene must work as an editorial Google Discover cover.
- Do not suggest text inside the image.

Return only this format, no extra explanation:

Central theme:
Main scene:
Main subject:
Visual action or gesture:
Foreground:
Background:
Visual symbol:
Emotion:
Composition:
What to avoid:
");
    }

    private function fallbackVisualBrief(Post $post, string $lang): string
    {
        $title = trim((string) ($post->title ?? ''));
        $description = trim((string) ($post->meta_description ?? ''));
        $content = $this->truncateForPrompt(
            $this->cleanArticleTextForPrompt((string) ($post->content ?? '')),
            700
        );

        if ($lang === 'en') {
            return trim("
Central theme: {$title}
Article signal: {$description}
Content signal: {$content}
Main scene: create a specific living Catholic editorial scene inspired by the article title and content.
Main subject: a concrete place, gesture, human situation, journey, encounter, contrast or visual metaphor connected to the article.
Visual action or gesture: show movement, encounter, prayer, decision, pilgrimage, reconciliation, service, study, silence or transformation when relevant.
Foreground: contextual subject from the article, not generic religious objects.
Background: Catholic or everyday environment only if it supports the article meaning.
Visual symbol: choose one subtle symbol connected to the article.
Emotion: hopeful, human, reverent, clear and contemporary.
Composition: horizontal 16:9, premium editorial photography, clean negative space for title overlay.
What to avoid: generic still life with Bible, candle, rosary and cross.
");
        }

        return trim("
Tema central: {$title}
Sinal do artigo: {$description}
Sinal do conteúdo: {$content}
Cena principal: criar uma cena editorial católica viva e específica inspirada no título e no conteúdo do artigo.
Assunto principal: um lugar, gesto, situação humana, caminho, encontro, contraste ou metáfora visual concreta conectada ao artigo.
Ação ou gesto visual: mostrar movimento, encontro, oração, decisão, peregrinação, reconciliação, serviço, estudo, silêncio ou transformação quando fizer sentido.
Primeiro plano: assunto contextual do artigo, não objetos religiosos genéricos.
Fundo: ambiente católico ou cotidiano apenas se reforçar o sentido do artigo.
Símbolo visual: escolher um símbolo sutil ligado ao artigo.
Emoção: esperança, humanidade, reverência, clareza e contemporaneidade.
Composição: horizontal 16:9, fotografia editorial premium, espaço negativo limpo para título.
O que evitar: natureza morta genérica com Bíblia, vela, terço e cruz.
");
    }

    private function buildNoTextPrompt(
        Post $post,
        string $lang,
        string $layout,
        string $base,
        string $accent,
        string $mood,
        string $accentEffect,
        string $visualBrief
    ): string {
        $title = trim((string) ($post->title ?? ''));
        $description = trim((string) ($post->meta_description ?? ''));

        $negativeSpace = $this->negativeSpaceInstruction($layout, $lang);

        if ($lang === 'en') {
            return trim("
Dynamic editorial illustration for the IA Tio Ben Catholic blog.
NO TEXT. NO readable words. NO logo. NO watermark.

Article title:
{$title}

Article summary:
{$description}

Specific visual brief generated from the article:
{$visualBrief}

Hard creative rule:
The image must follow the specific visual brief.
Do not fall back to a generic religious still life.
The main subject must be the scene, gesture, place, action or visual metaphor described in the brief.
Bible, rosary, candle or cross may appear only as secondary details, not as the central subject, unless the brief explicitly makes them central.

Composition:
- Horizontal 16:9.
- Layout preference: {$layout}.
- {$negativeSpace}
- Premium contemporary illustration with expressive hand-drawn lines, layered textures and energetic composition.
- Google Discover optimized.
- Strong contextual main subject.
- Visual depth, layered shapes and expressive lighting.
- Use environment, action and atmosphere to communicate the article through illustration.
- No static tabletop composition unless the article specifically demands it.

Visual style:
- Mood: {$mood}.
- Bright color blocks and clean modern Catholic aesthetic.
- Use the Tio Ben IA visual language: warm gold, sky blue, deep navy and off-white, rounded expressive details, friendly digital mentor energy and subtle faith symbols.
- Do not reproduce the logo or a character portrait.
- Subtle accent hints: {$accent}.
- {$accentEffect}.
- Human, hopeful, reverent, contemporary.
- Avoid over-dark, blue-heavy, dramatic or theatrical lighting.

Restrictions:
- No readable text anywhere.
- No identifiable faces.
- No cheap stock-photo look.
- No generic Bible + candle + rosary table scene.
- No photorealism, stock-photo look, realistic portrait, anime or childish illustration.
- No saint, Jesus or Mary with a clearly defined realistic face.
");
        }

        return trim("
Foto editorial católica ultra-realista para capa de blog.
SEM TEXTO. SEM palavras legíveis. SEM logo. SEM marca d'água.

Título do artigo:
{$title}

Resumo do artigo:
{$description}

Brief visual específico gerado a partir do artigo:
{$visualBrief}

Regra criativa obrigatória:
A imagem deve seguir o brief visual específico.
Não voltar para uma natureza morta religiosa genérica.
O assunto principal deve ser a cena, gesto, lugar, ação ou metáfora visual descrita no brief.
Bíblia, terço, vela ou cruz podem aparecer apenas como detalhes secundários, não como centro da imagem, exceto se o brief tornar isso explicitamente central.

Composição:
- Horizontal 16:9.
- Preferência de layout: {$layout}.
- {$negativeSpace}
- Fotografia editorial premium.
- Otimizada para Google Discover.
- Assunto principal contextual e forte.
- Profundidade natural, ambiente real, iluminação verossímil.
- Usar ambiente, ação e atmosfera para comunicar o artigo.
- Não usar composição estática de mesa, exceto se o artigo exigir.

Estilo visual:
- Clima: {$mood}.
- Luz natural clara.
- Estética católica moderna, limpa e acolhedora.
- Detalhes sutis em {$accent}.
- {$accentEffect}.
- Sensação humana, esperançosa, reverente e contemporânea.
- Evitar iluminação escura, azulada, dramática ou teatral.

Restrições:
- Não gerar texto legível em nenhum lugar.
- Não mostrar rostos identificáveis.
- Não parecer banco de imagem barato.
- Não gerar cena genérica de Bíblia + vela + terço em mesa.
- Não usar cartoon, anime, 3D ou ilustração infantil.
- Não representar santo, Jesus ou Maria com rosto realista claramente definido.
");
    }

    private function negativeSpaceInstruction(string $layout, string $lang): string
    {
        $pt = [
            'left-aligned' => 'Deixar a área inferior esquerda visualmente mais limpa para posterior título sobreposto; concentrar o assunto principal mais ao centro/direita.',
            'split-layout' => 'Deixar uma faixa limpa no lado esquerdo/inferior esquerdo para posterior título sobreposto; assunto principal no centro/direita.',
            'centered' => 'Manter a parte inferior central suficientemente limpa para posterior título sobreposto, sem esconder o assunto principal.',
            'bottom-editorial' => 'Manter a faixa inferior com textura simples e pouco ruído visual para posterior título sobreposto.',
            'top-heavy' => 'Manter a faixa inferior com textura simples e pouco ruído visual para posterior título sobreposto.',
        ];

        $en = [
            'left-aligned' => 'Keep the lower-left area visually clean for a future title overlay; place the main subject more toward center/right.',
            'split-layout' => 'Keep a clean left/lower-left area for a future title overlay; place the main subject center/right.',
            'centered' => 'Keep the lower-center area clean enough for a future title overlay without hiding the main subject.',
            'bottom-editorial' => 'Keep the bottom band simple and visually quiet for a future title overlay.',
            'top-heavy' => 'Keep the bottom band simple and visually quiet for a future title overlay.',
        ];

        $map = $lang === 'en' ? $en : $pt;

        return $map[$layout] ?? ($lang === 'en'
            ? 'Keep clean negative space for a future title overlay.'
            : 'Manter espaço negativo limpo para posterior título sobreposto.');
    }

    private function finalizeToDiscoverWebpWithOverlay(
        string $sourcePath,
        string $layout,
        string $base,
        string $accent,
        string $title,
        string $subtitle,
        string $cta,
        string $site
    ): string {
        if (!extension_loaded('imagick')) {
            return $this->finalizeToDiscoverWebp($sourcePath);
        }

        $img = new \Imagick($sourcePath);
        $img->setImageColorspace(\Imagick::COLORSPACE_SRGB);
        $img->cropThumbnailImage(self::FINAL_W, self::FINAL_H);

        $W = self::FINAL_W;
        $H = self::FINAL_H;
        $m = (int) round(min($W, $H) * self::SAFE_MARGIN_PCT);

        $box = $this->textBoxByLayout($layout, $W, $H, $m);

        $panel = new \ImagickDraw();
        $panel->setFillColor(new \ImagickPixel('rgba(255,255,255,0.34)'));
        $panel->setStrokeColor(new \ImagickPixel('rgba(255,255,255,0)'));
        $panel->roundRectangle(
            $box['x'],
            $box['y'],
            $box['x'] + $box['w'],
            $box['y'] + $box['h'],
            22,
            22
        );
        $img->drawImage($panel);

        $line = new \ImagickDraw();
        $line->setStrokeColor(new \ImagickPixel($accent));
        $line->setStrokeWidth(6);
        $line->setStrokeLineCap(\Imagick::LINECAP_ROUND);

        $lineX1 = $box['x'] + 18;
        $lineX2 = $lineX1 + 66;
        $lineY = $box['y'] + 22;
        $line->line($lineX1, $lineY, $lineX2, $lineY);
        $img->drawImage($line);

        $fontTitle = $this->fontPlayfair();
        $fontSans = $this->fontInter();

        $title = $this->limitWords($title, 8);
        $subtitle = $this->limitWords($subtitle, 12);

        $drawTitle = new \ImagickDraw();
        if ($fontTitle) {
            $drawTitle->setFont($fontTitle);
        }
        $drawTitle->setFillColor(new \ImagickPixel('#0B1C2D'));
        $drawTitle->setTextKerning(0.4);

        $drawSub = new \ImagickDraw();
        if ($fontSans) {
            $drawSub->setFont($fontSans);
        }
        $drawSub->setFillColor(new \ImagickPixel('rgba(11,28,45,0.92)'));

        $drawCta = new \ImagickDraw();
        if ($fontSans) {
            $drawCta->setFont($fontSans);
        }
        $drawCta->setFillColor(new \ImagickPixel('#FFFFFF'));

        $drawSite = new \ImagickDraw();
        if ($fontSans) {
            $drawSite->setFont($fontSans);
        }
        $drawSite->setFillColor(new \ImagickPixel('rgba(11,28,45,0.85)'));

        $titleBoxW = $box['w'] - 36;
        $x = $box['x'] + 18;
        $y = $box['y'] + 62;

        [$titleLines, $titleSize] = $this->wrapTextToWidth($img, $drawTitle, $title, $titleBoxW, 64, 40, 2);
        $drawTitle->setFontSize($titleSize);

        foreach ($titleLines as $tline) {
            $img->annotateImage($drawTitle, $x, $y, 0, $tline);
            $y += (int) round($titleSize * 1.15);
        }

        $y += 10;

        [$subLines, $subSize] = $this->wrapTextToWidth($img, $drawSub, $subtitle, $titleBoxW, 30, 22, 2);
        $drawSub->setFontSize($subSize);

        foreach ($subLines as $sline) {
            $img->annotateImage($drawSub, $x, $y, 0, $sline);
            $y += (int) round($subSize * 1.25);
        }

        $pillH = 44;
        $pillPadX = 18;

        $drawCta->setFontSize(22);
        $drawSite->setFontSize(18);

        $ctaMetrics = $img->queryFontMetrics($drawCta, $cta);
        $ctaW = (int) ceil(($ctaMetrics['textWidth'] ?? 80) + ($pillPadX * 2));

        $pillX = $box['x'] + 18;
        $pillY = $box['y'] + $box['h'] - $pillH - 16;

        $pill = new \ImagickDraw();
        $pill->setFillColor(new \ImagickPixel($accent));
        $pill->setStrokeColor(new \ImagickPixel('rgba(0,0,0,0)'));
        $pill->roundRectangle($pillX, $pillY, $pillX + $ctaW, $pillY + $pillH, 18, 18);
        $img->drawImage($pill);

        $ctaX = $pillX + (int) round(($ctaW - ($ctaMetrics['textWidth'] ?? 0)) / 2);
        $ctaY = $pillY + (int) round(($pillH + ($ctaMetrics['ascender'] ?? 16)) / 2) - 2;
        $img->annotateImage($drawCta, $ctaX, $ctaY, 0, $cta);

        $siteMetrics = $img->queryFontMetrics($drawSite, $site);
        $siteW = (int) ceil($siteMetrics['textWidth'] ?? 140);

        $siteX = $box['x'] + $box['w'] - 18 - $siteW;
        $siteY = $pillY + (int) round($pillH * 0.72);
        $img->annotateImage($drawSite, $siteX, $siteY, 0, $site);

        $img->setImageFormat('webp');
        $img->setImageCompressionQuality(self::OUTPUT_COMPRESSION);
        $img->stripImage();

        return $img->getImagesBlob();
    }

    private function finalizeToDiscoverWebp(string $sourcePath): string
    {
        if (extension_loaded('imagick')) {
            $img = new \Imagick($sourcePath);
            $img->setImageColorspace(\Imagick::COLORSPACE_SRGB);
            $img->cropThumbnailImage(self::FINAL_W, self::FINAL_H);
            $img->setImageFormat('webp');
            $img->setImageCompressionQuality(self::OUTPUT_COMPRESSION);
            $img->stripImage();

            return $img->getImagesBlob();
        }

        return (string) @file_get_contents($sourcePath);
    }

    private function textBoxByLayout(string $layout, int $W, int $H, int $m): array
    {
        $x = $m;
        $w = $W - ($m * 2);
        $h = (int) round($H * 0.34);
        $y = $H - $m - $h;

        if ($layout === 'left-aligned') {
            $w = (int) round($W * 0.58);
            $x = $m;
        } elseif ($layout === 'split-layout') {
            $w = (int) round($W * 0.52);
            $x = $m;
        } elseif ($layout === 'centered') {
            $w = (int) round($W * 0.70);
            $x = (int) round(($W - $w) / 2);
        } elseif ($layout === 'bottom-editorial') {
            $w = (int) round($W * 0.68);
            $x = $m;
            $h = (int) round($H * 0.36);
            $y = $H - $m - $h;
        }

        return compact('x', 'y', 'w', 'h');
    }

    private function buildOverlayText(Post $post, string $lang): array
    {
        $title = trim((string) ($post->title ?? ''));

        $h1 = $this->extractFirstTagText((string) ($post->content ?? ''), 'h1');

        if ($h1 !== '') {
            $title = $h1;
        }

        if ($title === '') {
            $title = $lang === 'en' ? 'Daily Catholic Reflection' : 'Reflexão Católica do Dia';
        }

        $subtitle = trim((string) ($post->meta_description ?? ''));

        if ($subtitle === '') {
            $p = $this->extractFirstTagText((string) ($post->content ?? ''), 'p');

            $subtitle = $p !== ''
                ? $p
                : ($lang === 'en'
                    ? 'A practical moment of faith for today.'
                    : 'Um momento prático de fé para hoje.');
        }

        $cta = $lang === 'en' ? 'Read now' : 'Leia agora';

        $title = $this->limitWords($title, 8);
        $subtitle = $this->limitWords($subtitle, 12);

        return [$title, $subtitle, $cta];
    }

    private function extractFirstTagText(string $html, string $tag): string
    {
        if ($html === '') {
            return '';
        }

        if (preg_match('~<' . $tag . '\b[^>]*>(.*?)</' . $tag . '>~is', $html, $m)) {
            $t = trim(strip_tags((string) ($m[1] ?? '')));
            $t = preg_replace('/\s+/', ' ', $t);

            return trim((string) $t);
        }

        return '';
    }

    private function wrapTextToWidth(
        \Imagick $img,
        \ImagickDraw $draw,
        string $text,
        int $maxWidth,
        int $maxFont,
        int $minFont,
        int $maxLines
    ): array {
        $text = trim((string) preg_replace('/\s+/', ' ', $text));

        if ($text === '') {
            return [[''], $minFont];
        }

        for ($font = $maxFont; $font >= $minFont; $font -= 2) {
            $draw->setFontSize($font);
            $lines = $this->wrapByWords($img, $draw, $text, $maxWidth);

            if (count($lines) <= $maxLines) {
                return [$lines, $font];
            }
        }

        $draw->setFontSize($minFont);
        $lines = $this->wrapByWords($img, $draw, $text, $maxWidth);

        if (count($lines) > $maxLines) {
            $lines = array_slice($lines, 0, $maxLines);
            $lines[$maxLines - 1] = rtrim($lines[$maxLines - 1], " \t\n\r\0\x0B.") . '…';
        }

        return [$lines, $minFont];
    }

    private function wrapByWords(\Imagick $img, \ImagickDraw $draw, string $text, int $maxWidth): array
    {
        $words = preg_split('/\s+/', trim($text)) ?: [];
        $lines = [];
        $current = '';

        foreach ($words as $w) {
            $try = $current === '' ? $w : ($current . ' ' . $w);
            $metrics = $img->queryFontMetrics($draw, $try);
            $wpx = (int) ceil($metrics['textWidth'] ?? 0);

            if ($wpx <= $maxWidth) {
                $current = $try;
                continue;
            }

            if ($current !== '') {
                $lines[] = $current;
                $current = $w;
            } else {
                $lines[] = $w;
                $current = '';
            }
        }

        if ($current !== '') {
            $lines[] = $current;
        }

        return $lines;
    }

    private function limitWords(string $text, int $maxWords): string
    {
        $t = trim((string) preg_replace('/\s+/', ' ', $text));

        if ($t === '') {
            return $t;
        }

        $parts = preg_split('/\s+/', $t) ?: [];

        if (count($parts) <= $maxWords) {
            return $t;
        }

        return implode(' ', array_slice($parts, 0, $maxWords));
    }

    private function detectLang(Post $post): string
    {
        $raw = strtolower((string) ($post->lang ?? $post->locale ?? ''));

        if (str_starts_with($raw, 'en')) {
            return 'en';
        }

        $slug = strtolower((string) ($post->slug ?? ''));

        if (str_starts_with($slug, 'en-')) {
            return 'en';
        }

        return 'pt';
    }

    private function pick(array $items)
    {
        return $items[array_rand($items)];
    }

    private function cleanArticleTextForPrompt(string $html): string
    {
        $text = preg_replace('~<script\b[^>]*>.*?</script>~is', ' ', $html);
        $text = preg_replace('~<style\b[^>]*>.*?</style>~is', ' ', (string) $text);
        $text = preg_replace('~<ins\b[^>]*class="[^"]*adsbygoogle[^"]*"[^>]*>.*?</ins>~is', ' ', (string) $text);

        $text = strip_tags((string) $text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        $removePatterns = [
            '/ca-pub-\d+/i',
            '/data-ad-[a-z-]+/i',
            '/adsbygoogle/i',
            '/Blog IA Tio Ben/i',
            '/Por Tio Ben/i',
            '/Leia agora/i',
            '/Read now/i',
        ];

        foreach ($removePatterns as $pattern) {
            $text = preg_replace($pattern, ' ', (string) $text);
        }

        $text = preg_replace('/\s+/', ' ', (string) $text);

        return trim((string) $text);
    }

    private function extractResponsesText(array $json): string
    {
        if (!empty($json['output_text']) && is_string($json['output_text'])) {
            return $json['output_text'];
        }

        $parts = [];

        foreach (($json['output'] ?? []) as $output) {
            foreach (($output['content'] ?? []) as $content) {
                if (isset($content['text']) && is_string($content['text'])) {
                    $parts[] = $content['text'];
                }

                if (isset($content['type'], $content['text']) && is_string($content['text'])) {
                    $parts[] = $content['text'];
                }
            }
        }

        return trim(implode("\n", array_filter($parts)));
    }

    private function truncateForPrompt(string $text, int $maxChars): string
    {
        $t = trim((string) preg_replace('/\s+/', ' ', $text));

        if (mb_strlen($t) <= $maxChars) {
            return $t;
        }

        return mb_substr($t, 0, $maxChars) . '…';
    }

    private function safeBody(string $body): string
    {
        $b = trim($body);

        if (mb_strlen($b) <= 2500) {
            return $b;
        }

        return mb_substr($b, 0, 2500) . '…';
    }
}