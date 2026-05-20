<?php

namespace App\Services;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;

class ArticleFormatter
{
    private array $DS;

    private array $LITURGIA_LINK_TEXTS = [
        "Ver a liturgia de hoje",
        "Ler a liturgia do dia",
        "Acompanhar a liturgia de hoje",
        "Ir para a Liturgia Diária",
        "Abrir a liturgia do dia",
        "Conferir a liturgia de hoje",
        "Ler as leituras de hoje",
        "Ver leituras e salmos de hoje",
        "Liturgia de hoje: abrir agora",
        "Ler a Palavra de hoje",
        "Consultar a liturgia do dia",
        "Veja a liturgia de hoje",
        "Acesse a liturgia diária",
        "Ver a liturgia e as leituras",
        "Liturgia do dia: ver agora",
    ];

    private array $TERCO_LINK_TEXTS = [
        "Rezar o terço do dia",
        "Rezar o santo terço hoje",
        "Ir para o terço de hoje",
        "Meditar os mistérios no terço",
        "Reze o terço agora",
        "Acompanhar o terço do dia",
        "Terço de hoje: começar",
        "Iniciar o terço do dia",
        "Rezando juntos: terço",
        "Rezar o terço com calma",
        "Terço do dia: rezar agora",
        "Acessar o Santo Terço",
        "Rezar e meditar no terço",
        "Abrir o terço do dia",
        "Rezar o terço hoje",
    ];

    private array $GENERIC_LINK_TEXTS = [
        "Abrir link",
        "Acessar agora",
        "Ver mais",
        "Ir para a página",
        "Continuar",
        "Saiba mais",
        "Abrir conteúdo",
        "Ver detalhes",
        "Ler agora",
        "Acessar conteúdo",
        "Abrir página",
        "Conferir",
        "Veja aqui",
        "Clique para abrir",
        "Ir agora",
    ];

    private array $LITURGIA_CARD_TITLES = [
        "Liturgia do dia",
        "Liturgia de hoje",
        "Leituras da missa de hoje",
        "A Palavra de hoje",
        "Liturgia diária (hoje)",
        "Liturgia: hoje",
        "Leituras e salmos de hoje",
        "Liturgia e leituras",
        "Liturgia para hoje",
        "Liturgia do dia (missa)",
        "Liturgia: leituras do dia",
        "Liturgia do dia e reflexões",
        "Liturgia de hoje (rápido)",
        "Liturgia diária",
        "Liturgia de hoje (missa)",
    ];

    private array $TERCO_CARD_TITLES = [
        "Terço do dia",
        "Terço de hoje",
        "Santo terço: hoje",
        "Reze o terço hoje",
        "Terço para hoje",
        "Terço e mistérios",
        "Momento do terço",
        "Terço: meditação de hoje",
        "Terço do dia (mistérios)",
        "Terço de hoje (começar)",
        "Terço: rezar agora",
        "Terço diário",
        "Terço de hoje (guiado)",
        "Santo terço",
        "Terço do dia (orar)",
    ];

    public function __construct()
    {
        $this->DS = [
            'article' =>
                'post-santo mx-auto w-full max-w-3xl px-3 sm:px-4 lg:max-w-5xl lg:px-6 py-6 bg-white font-sans text-gray-800 leading-relaxed min-h-screen overflow-x-hidden',

            'header' => [
                'wrap'       => 'mb-10 border-b border-gray-200 pb-6',
                'metaLine'   => 'text-sm text-gray-500',
                'authorLine' => 'mt-1 text-sm text-gray-500',
                'h1'         => 'mt-2 text-3xl sm:text-4xl font-extrabold tracking-tight text-gray-900 leading-snug',
                'excerpt'    => 'mt-3 text-lg text-gray-700 leading-[1.9] break-words',
            ],

            'typography' => [
                'h2' => 'mt-14 mb-6 pl-4 text-xl sm:text-2xl font-bold text-gray-800 border-l-4 border-amber-300 leading-snug scroll-mt-28',
                'h3' => 'mt-10 mb-4 text-lg sm:text-xl font-semibold text-gray-900 leading-snug scroll-mt-24',
                'h4' => 'mt-8 mb-3 text-base sm:text-lg font-semibold text-gray-900 leading-snug scroll-mt-24',

                'p'  => 'my-5 text-[17px] leading-[1.95] text-gray-700 break-words',
                'ul' => 'my-5 pl-6 list-disc space-y-2 text-[17px] leading-[1.95] text-gray-700 break-words',
                'ol' => 'my-5 pl-6 list-decimal space-y-2 text-[17px] leading-[1.95] text-gray-700 break-words',
                'li' => 'text-[17px] leading-[1.95] text-gray-700 break-words',

                'a'      => 'font-semibold text-amber-800 underline decoration-amber-300 hover:decoration-amber-600 break-words underline-offset-2',
                'strong' => 'text-gray-900 font-semibold',

                'blockquote' => 'my-7 rounded-xl border border-gray-200 bg-gray-50 px-5 py-4 text-gray-700 break-words',
                'hr'         => 'my-10 border-gray-200',
                'img'        => 'my-8 rounded-2xl shadow-sm border border-gray-200 max-w-full h-auto',

                'codeInline' => 'px-1.5 py-0.5 rounded-md bg-gray-100 border border-gray-200 text-[0.95em] text-gray-900 break-words',
                'pre'        => 'my-7 overflow-x-auto rounded-2xl border border-gray-200 bg-gray-950 p-5 text-gray-100 text-sm leading-relaxed',

                'tableWrap' => 'my-8 overflow-x-auto rounded-2xl border border-gray-200',
                'table'     => 'w-full border-collapse text-left text-sm',
                'thead'     => 'bg-gray-50',
                'th'        => 'px-4 py-3 font-semibold text-gray-900 border-b border-gray-200 whitespace-nowrap',
                'tbody'     => 'divide-y divide-gray-200',
                'td'        => 'px-4 py-3 text-gray-700 align-top break-words',
            ],

            'toc' => [
                'wrap'  => 'my-8 rounded-xl border border-amber-200 bg-amber-50/60 p-5 shadow-sm overflow-hidden',
                'title' => 'text-sm font-semibold text-amber-900 tracking-wide',
                'list'  => 'mt-4 grid gap-2 sm:grid-cols-2',
                'link'  => 'inline-flex w-full min-w-0 items-center justify-between gap-3 rounded-lg border border-amber-100 bg-white/70 px-3 py-2 text-sm font-semibold text-amber-900 hover:bg-amber-100 transition whitespace-normal break-words',
            ],

            'special' => [
                'default' => ['wrap' => 'border-gray-200 bg-white', 'title' => 'text-gray-900'],
                'amber'   => ['wrap' => 'border-amber-200 bg-[#fffaf1]', 'title' => 'text-amber-900'],
                'sky'     => ['wrap' => 'border-sky-200 bg-sky-50', 'title' => 'text-sky-900'],
            ],

            'longRead' => [
                'articleBody' => 'max-w-none overflow-hidden',
            ],
        ];
    }

    public function formatArticleToHtml(string $articleMarkdown): string
    {
        $input = $this->normalizeNewlines(trim($articleMarkdown));
        if ($input === '') return '<article></article>';

        // ✅ remove wrapper externo ```markdown ... ``` (causa do <pre><code class="language-markdown">...)
        $input = $this->unwrapOuterMarkdownFence($input);

        // 1) remove TODOS os blocos [SEO]...[/SEO]
        $seoRemoved = $this->extractAllBlocks($input, 'SEO');
        $mdNoSeo = $seoRemoved['cleaned'];

        // 2) extrai liturgia/terco
        $special = $this->extractSpecialBlocks($mdNoSeo);
        $mdClean = $special['mdClean'];
        $liturgia = $special['liturgia'];
        $terco = $special['terco'];

        // 3) título + excerpt
        $te = $this->extractTitleAndExcerpt($mdClean);
        $title = $te['title'] ?: 'Tio Ben';
        $excerpt = $te['excerpt'] ?: 'Hoje, caminhemos juntos pela fé: uma leitura que ilumina, consola e nos aproxima de Deus na vida concreta.';

        // 4) corpo limpo
        $mdAfterFirstH1 = $this->stripFirstH1($mdClean);
        $mdNoExcerpt = $this->stripExcerptFromBody($mdAfterFirstH1, $te['excerpt']);
        $mdBody = $this->stripAllH1($mdNoExcerpt);

        // ✅ também remove wrapper externo se o corpo ficou embrulhado (caso raro)
        $mdBody = $this->unwrapOuterMarkdownFence(trim($mdBody));

        // 5) TOC
        $toc = $this->buildToc($mdBody);
        $tocHtml = $this->renderToc($toc, 4);

        // 6) Markdown -> HTML
        $bodyHtmlRaw = $this->markdownToHtml($mdBody);

        // 7) IDs + classes DS
        $bodyHtml = $this->applyFormattingToBodyHtml($bodyHtmlRaw, $toc);

        // 8) cards especiais (títulos randomizados)
        $litTitle = $this->pickRandom($this->LITURGIA_CARD_TITLES, 'Liturgia do dia');
        $terTitle = $this->pickRandom($this->TERCO_CARD_TITLES, 'Terço do dia');

        $liturgiaHtml = $this->renderSpecialCard($litTitle, $liturgia, 'amber');
        $tercoHtml    = $this->renderSpecialCard($terTitle, $terco, 'sky');

        $specialsGridHtml = $this->renderSpecialBlocksGrid($liturgiaHtml, $tercoHtml);

        $publishedISO = date('Y-m-d');
        $safeTitle = e($title);
        $safeExcerpt = e($excerpt);

        return trim("
<article class=\"{$this->DS['article']}\" itemscope itemtype=\"https://schema.org/Article\">
  <header class=\"{$this->DS['header']['wrap']}\">
    <p class=\"{$this->DS['header']['metaLine']}\">
      <time datetime=\"{$publishedISO}\" itemprop=\"datePublished\">{$publishedISO}</time>
    </p>

    <p class=\"{$this->DS['header']['authorLine']}\" itemprop=\"author\" itemscope itemtype=\"https://schema.org/Person\">
      <span itemprop=\"name\">Tio Ben</span>
    </p>

    <h1 class=\"{$this->DS['header']['h1']}\" itemprop=\"headline\">{$safeTitle}</h1>

    <p class=\"{$this->DS['header']['excerpt']}\" itemprop=\"description\">{$safeExcerpt}</p>
  </header>

  {$tocHtml}

  <div class=\"my-8 flex justify-center overflow-hidden\">
    <ins class=\"adsbygoogle\"
      style=\"display:block;\"
      data-ad-client=\"ca-pub-8819996017476509\"
      data-ad-slot=\"3041346283\"
      data-ad-format=\"fluid\"
      data-full-width-responsive=\"true\">
    </ins>
  </div>

  <div itemprop=\"articleBody\" class=\"{$this->DS['longRead']['articleBody']}\">
    {$bodyHtml}
  </div>

  {$specialsGridHtml}
</article>
");
    }

    public function analyzeSeoAndExtractMetadata(string $articleMarkdown, string $focusKeywords): array
    {
        $seo = $this->extractAllBlocks($articleMarkdown, 'SEO');
        $seoBlock = $seo['firstValue'];
        $mdNoSeo = $seo['cleaned'];

        if ($seoBlock) {
            $parsed = $this->safeJsonParse($seoBlock);

            $keywords = [];
            if (is_array($parsed) && isset($parsed['keywords']) && is_array($parsed['keywords'])) {
                $keywords = array_values(array_filter(array_map(
                    fn($x) => trim((string)$x),
                    $parsed['keywords']
                )));
                $keywords = array_slice($keywords, 0, 12);
            }

            $meta = '';
            if (is_array($parsed) && isset($parsed['metaDescription']) && is_string($parsed['metaDescription'])) {
                $meta = $this->clampMeta($parsed['metaDescription'], 160);
            }

            if (count($keywords) >= 6 && $meta !== '') {
                return ['keywords' => $keywords, 'metaDescription' => $meta];
            }
        }

        $te = $this->extractTitleAndExcerpt($mdNoSeo);
        $excerpt = $te['excerpt'] ?: 'Reflexão católica do dia, com fé e esperança para viver o Evangelho na vida real.';

        $baseKeywords = $this->normalizeKeywordsFromFocus($focusKeywords);
        $keywords = array_slice($baseKeywords, 0, 8);

        if (count($keywords) < 6) {
            $keywords = array_slice(array_merge($keywords, ['liturgia diária', 'oração católica']), 0, 8);
        }

        return [
            'keywords' => $keywords,
            'metaDescription' => $this->clampMeta($excerpt, 160),
        ];
    }

    private function normalizeNewlines(string $s): string
    {
        return str_replace(["\r\n", "\r"], "\n", $s);
    }

    /**
     * ✅ Remove wrapper externo:
     * ```markdown
     * ...conteúdo...
     * ```
     * (ou ``` / ```), quando envolve o texto inteiro.
     */
    private function unwrapOuterMarkdownFence(string $s): string
    {
        $src = trim($this->normalizeNewlines($s));
        if ($src === '') return $src;

        // aceita ```markdown, ```md, ```text, ou ``` puro
        if (!preg_match('/^\s*```([a-z0-9_-]+)?\s*\n([\s\S]*?)\n```(\s*)$/i', $src, $m)) {
            return $src;
        }

        $inside = (string)($m[2] ?? '');
        $inside = trim($inside);

        // proteção: só desembrulha se “parece artigo”
        // (tem pelo menos um heading ou parágrafo grande)
        $looksLikeArticle =
            (bool) preg_match('/^#\s+/m', $inside) ||
            (mb_strlen($inside) > 400);

        return $looksLikeArticle ? $inside : $src;
    }

    private function extractAllBlocks(string $text, string $tag): array
    {
        $re = '/\[' . preg_quote($tag, '/') . '\]([\s\S]*?)\[\/' . preg_quote($tag, '/') . '\]/i';
        preg_match_all($re, $text, $m);

        $firstValue = '';
        if (!empty($m[1]) && isset($m[1][0])) $firstValue = trim((string)$m[1][0]);

        $cleaned = preg_replace($re, '', $text);
        $cleaned = trim((string)$cleaned);

        return ['firstValue' => $firstValue, 'cleaned' => $cleaned];
    }

    private function extractSpecialBlocks(string $md): array
    {
        $cleaned = $md;

        $lit = $this->extractAllBlocks($cleaned, 'liturgia');
        $cleaned = $lit['cleaned'];

        $ter = $this->extractAllBlocks($cleaned, 'terco');
        $cleaned = $ter['cleaned'];

        return [
            'mdClean'   => trim($cleaned),
            'liturgia'  => $lit['firstValue'],
            'terco'     => $ter['firstValue'],
        ];
    }

    private function extractTitleAndExcerpt(string $md): array
    {
        $lines = explode("\n", $md);
        $title = '';
        $i = 0;

        for (; $i < count($lines); $i++) {
            if (preg_match('/^#\s+(.+)\s*$/', $lines[$i], $m)) {
                $title = trim($m[1]);
                $i++;
                break;
            }
        }

        $buff = [];
        for (; $i < count($lines); $i++) {
            $line = $lines[$i];
            if (preg_match('/^#{1,6}\s+/', $line)) break;

            if (trim($line) === '') {
                if (!empty($buff)) break;
                continue;
            }

            $buff[] = trim($line);
        }

        $excerpt = trim(preg_replace('/\s+/', ' ', implode(' ', $buff)));

        return ['title' => $title, 'excerpt' => $excerpt];
    }

    private function stripFirstH1(string $md): string
    {
        $src = trim($this->normalizeNewlines($md));
        $lines = explode("\n", $src);

        foreach ($lines as $idx => $l) {
            if (preg_match('/^#\s+/', $l)) {
                array_splice($lines, $idx, 1);
                break;
            }
        }

        return trim(implode("\n", $lines));
    }

    private function stripAllH1(string $md): string
    {
        $src = $this->normalizeNewlines($md);
        $src = preg_replace('/^#\s+.*$/m', '', $src);
        return trim((string)$src);
    }

    private function stripExcerptFromBody(string $bodyAfterH1, string $excerpt): string
    {
        $body = ltrim($this->normalizeNewlines($bodyAfterH1));
        $ex = trim($this->normalizeNewlines($excerpt));

        if ($ex === '') return trim($body);

        if (str_starts_with($body, $ex)) {
            $rest = ltrim(substr($body, strlen($ex)));
            $rest = ltrim((string)preg_replace("/^\n+/", "", $rest));
            return trim($rest);
        }

        if (preg_match('/^([\s\S]*?)(\n{2,}|$)/', $body, $m)) {
            $firstPara = trim((string)($m[1] ?? ''));
            if ($firstPara !== '' && $firstPara === $ex) {
                $rest = ltrim(substr($body, strlen((string)$m[0])));
                return trim($rest);
            }
        }

        return trim($body);
    }

    private function buildToc(string $mdBody): array
    {
        $lines = explode("\n", $mdBody);
        $toc = [];
        $sec = 0;

        foreach ($lines as $line) {
            if (preg_match('/^##\s+(.+)\s*$/', $line, $m)) {
                $sec++;
                $toc[] = ['id' => "sec-{$sec}", 'title' => trim($m[1])];
            }
        }

        return $toc;
    }

    private function renderToc(array $toc, int $minH2 = 4): string
    {
        if (count($toc) < $minH2) return '';

        $items = '';
        foreach ($toc as $t) {
            $id = e($t['id']);
            $title = e($t['title']);
            $items .= trim("
<li>
  <a class=\"{$this->DS['toc']['link']}\" href=\"#{$id}\">
    <span class=\"min-w-0 flex-1 break-words\">{$title}</span>
    <span aria-hidden=\"true\" class=\"shrink-0\">→</span>
  </a>
</li>
");
        }

        return trim("
<nav class=\"{$this->DS['toc']['wrap']}\">
  <h4 class=\"{$this->DS['toc']['title']}\">Neste artigo</h4>
  <ul class=\"{$this->DS['toc']['list']}\">
    {$items}
  </ul>
</nav>
");
    }

    private function markdownToHtml(string $md): string
    {
        $env = new Environment();
        $env->addExtension(new CommonMarkCoreExtension());

        $converter = new CommonMarkConverter([], $env);
        return (string) $converter->convert($md);
    }

    private function applyFormattingToBodyHtml(string $html, array $toc): string
    {
        $doc = new \DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);

        $doc->loadHTML('<?xml encoding="UTF-8"><div id="__wrap__">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $wrap = $doc->getElementById('__wrap__');
        if (!$wrap) return $html;

        $h2s = $wrap->getElementsByTagName('h2');
        $i = 0;
        foreach ($h2s as $h2) {
            $i++;
            $id = $toc[$i - 1]['id'] ?? "sec-{$i}";
            if ($h2 instanceof \DOMElement) {
                if (!$h2->hasAttribute('id')) $h2->setAttribute('id', $id);
                $this->appendClass($h2, $this->DS['typography']['h2']);
            }
        }

        $this->applyTagClass($wrap, 'h3', $this->DS['typography']['h3']);
        $this->applyTagClass($wrap, 'h4', $this->DS['typography']['h4']);
        $this->applyTagClass($wrap, 'p',  $this->DS['typography']['p']);
        $this->applyTagClass($wrap, 'ul', $this->DS['typography']['ul']);
        $this->applyTagClass($wrap, 'ol', $this->DS['typography']['ol']);
        $this->applyTagClass($wrap, 'li', $this->DS['typography']['li']);
        $this->applyTagClass($wrap, 'a',  $this->DS['typography']['a']);
        $this->applyTagClass($wrap, 'strong', $this->DS['typography']['strong']);
        $this->applyTagClass($wrap, 'blockquote', $this->DS['typography']['blockquote']);
        $this->applyTagClass($wrap, 'hr', $this->DS['typography']['hr']);
        $this->applyTagClass($wrap, 'img', $this->DS['typography']['img']);
        $this->applyTagClass($wrap, 'pre', $this->DS['typography']['pre']);

        $codes = $wrap->getElementsByTagName('code');
        foreach ($codes as $code) {
            if (!($code instanceof \DOMElement)) continue;

            $parent = $code->parentNode;
            $insidePre = false;
            while ($parent instanceof \DOMElement) {
                if (strtolower($parent->tagName) === 'pre') { $insidePre = true; break; }
                $parent = $parent->parentNode;
            }
            if (!$insidePre) {
                $this->appendClass($code, $this->DS['typography']['codeInline']);
            }
        }

        $tables = $wrap->getElementsByTagName('table');
        $tableNodes = [];
        foreach ($tables as $t) $tableNodes[] = $t;

        foreach ($tableNodes as $table) {
            if (!($table instanceof \DOMElement)) continue;

            $this->appendClass($table, $this->DS['typography']['table']);

            $div = $doc->createElement('div');
            $div->setAttribute('class', $this->DS['typography']['tableWrap']);

            $table->parentNode->insertBefore($div, $table);
            $div->appendChild($table);

            foreach ($table->getElementsByTagName('thead') as $thead) if ($thead instanceof \DOMElement) $this->appendClass($thead, $this->DS['typography']['thead']);
            foreach ($table->getElementsByTagName('th') as $th) if ($th instanceof \DOMElement) $this->appendClass($th, $this->DS['typography']['th']);
            foreach ($table->getElementsByTagName('tbody') as $tbody) if ($tbody instanceof \DOMElement) $this->appendClass($tbody, $this->DS['typography']['tbody']);
            foreach ($table->getElementsByTagName('td') as $td) if ($td instanceof \DOMElement) $this->appendClass($td, $this->DS['typography']['td']);
        }

        return $this->innerHtml($wrap);
    }

    private function applyTagClass(\DOMElement $root, string $tag, string $class): void
    {
        $nodes = $root->getElementsByTagName($tag);
        foreach ($nodes as $node) {
            if ($node instanceof \DOMElement) $this->appendClass($node, $class);
        }
    }

    private function appendClass(\DOMElement $el, string $classToAdd): void
    {
        $current = trim((string)$el->getAttribute('class'));
        if ($current === '') {
            $el->setAttribute('class', $classToAdd);
            return;
        }
        $next = trim((string)preg_replace('/\s+/', ' ', $current . ' ' . $classToAdd));
        $el->setAttribute('class', $next);
    }

    private function innerHtml(\DOMElement $el): string
    {
        $html = '';
        foreach ($el->childNodes as $child) {
            $html .= $el->ownerDocument->saveHTML($child);
        }
        return $html;
    }

    private function renderSpecialCard(string $title, string $markdown, string $variant): string
    {
        $markdown = trim((string)$markdown);
        if ($markdown === '') return '';

        $pal = $this->DS['special'][$variant] ?? $this->DS['special']['default'];

        $primaryText = $variant === 'amber'
            ? $this->pickRandom($this->LITURGIA_LINK_TEXTS, 'Ver a liturgia de hoje')
            : ($variant === 'sky'
                ? $this->pickRandom($this->TERCO_LINK_TEXTS, 'Rezar o terço do dia')
                : $this->pickRandom($this->GENERIC_LINK_TEXTS, 'Abrir link'));

        $genericText = $this->pickRandom($this->GENERIC_LINK_TEXTS, 'Abrir link');

        $md = $this->linkifyBareUrlsSmart($markdown, $primaryText, $genericText);

        $inner = $this->markdownToHtml($md);
        $inner = $this->applyFormattingToBodyHtml($inner, []);

        $safeTitle = e($title);

        return trim("
<section class=\"my-5 rounded-xl border {$pal['wrap']} p-5 sm:p-6 shadow-sm overflow-hidden\">
  <h3 class=\"{$this->DS['typography']['h3']} {$pal['title']} !mt-0\">{$safeTitle}</h3>
  <div class=\"{$this->DS['longRead']['articleBody']} break-words\">
    {$inner}
  </div>
</section>
");
    }

    private function renderSpecialBlocksGrid(string $liturgiaHtml, string $tercoHtml): string
    {
        return trim("
<section class=\"mt-10\" aria-label=\"Liturgia e Terço\">
  <div class=\"grid gap-4 md:grid-cols-2\">
    {$liturgiaHtml}
    {$tercoHtml}
  </div>
</section>
");
    }

    private function linkifyBareUrlsSmart(string $md, string $primaryText, string $genericText): string
    {
        $src = (string)$md;
        if (trim($src) === '') return '';

        $fenceMap = [];
        $src = preg_replace_callback('/```[\s\S]*?```/m', function ($m) use (&$fenceMap) {
            $key = '__FENCE_' . count($fenceMap) . '__';
            $fenceMap[$key] = $m[0];
            return $key;
        }, $src);

        $inlineMap = [];
        $src = preg_replace_callback('/`[^`]*`/', function ($m) use (&$inlineMap) {
            $key = '__INLINE_' . count($inlineMap) . '__';
            $inlineMap[$key] = $m[0];
            return $key;
        }, $src);

        $seen = 0;
        $orig = $src;

        $src = preg_replace_callback('/(https?:\/\/[^\s)<]+)([).,;:!?]*)/i', function ($m) use (&$seen, $primaryText, $genericText, $orig) {
            $url = (string)$m[1];
            $trail = (string)($m[2] ?? '');

            if (preg_match('/\]\(\s*' . preg_quote($url, '/') . '/i', $orig)) {
                return $m[0];
            }

            $seen++;
            $text = ($seen === 1) ? $primaryText : $genericText;

            return '[' . $text . '](' . $url . ')' . $trail;
        }, $src);

        if (!empty($inlineMap)) $src = strtr($src, $inlineMap);
        if (!empty($fenceMap))  $src = strtr($src, $fenceMap);

        return $src;
    }

    private function safeJsonParse(string $s): ?array
    {
        try {
            $j = json_decode($s, true, 512, JSON_THROW_ON_ERROR);
            return is_array($j) ? $j : null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function normalizeKeywordsFromFocus(string $focus): array
    {
        $parts = preg_split('/[,;\n|]+/', (string)$focus) ?: [];
        $out = [];
        $seen = [];

        foreach ($parts as $p) {
            $p = trim((string)$p);
            if ($p === '') continue;
            $k = mb_strtolower($p);
            if (isset($seen[$k])) continue;
            $seen[$k] = true;
            $out[] = $p;
        }

        return $out;
    }

    private function clampMeta(string $s, int $max = 160): string
    {
        $one = trim((string)preg_replace('/\s+/', ' ', (string)$s));
        if (mb_strlen($one) <= $max) return $one;

        $cut = mb_substr($one, 0, $max);
        $lastSpace = mb_strrpos($cut, ' ');
        if ($lastSpace !== false && $lastSpace > 80) {
            $cut = mb_substr($cut, 0, $lastSpace);
        }

        return trim($cut);
    }

    private function pickRandom(array $arr, string $fallback): string
    {
        if (empty($arr)) return $fallback;
        $idx = random_int(0, count($arr) - 1);
        $v = $arr[$idx] ?? $fallback;
        $v = trim((string)$v);
        return $v !== '' ? $v : $fallback;
    }
}