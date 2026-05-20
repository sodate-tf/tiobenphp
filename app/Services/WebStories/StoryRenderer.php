<?php

namespace App\Services\WebStories;

class StoryRenderer
{
    private function esc(mixed $s): string
    {
        return htmlspecialchars((string) ($s ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public function renderAmpHtml(array $story): string
    {
        $pagesHtml = $this->renderPagesHtml($story);

        $lang = $this->esc($story['lang'] ?? 'pt-BR');

        $title = (string) ($story['title'] ?? 'Web Story — Tio Ben IA');
        $description = (string) ($story['description'] ?? '');

        $canonicalUrl = $this->getStoryCanonicalUrl($story);
        $posterPortraitSrc = $this->getPosterPortraitSrc($story);
        $posterSquareSrc = $this->getPosterSquareSrc($story);
        $posterLandscapeSrc = $this->getPosterLandscapeSrc($story);

        $publisherName = (string) ($story['publisherName'] ?? 'Tio Ben IA');
        $publisherLogoSrc = $this->absUrl($story['publisherLogoSrc'] ?? '', config('app.url')) ?? '';

        // JSON-LD deve ser inserido cru. Não use htmlspecialchars aqui.
        $jsonLd = $this->buildJsonLd($story, [
            'canonicalUrl' => $canonicalUrl,
            'posterPortraitSrc' => $posterPortraitSrc,
            'posterSquareSrc' => $posterSquareSrc,
            'posterLandscapeSrc' => $posterLandscapeSrc,
            'publisherLogoSrc' => $publisherLogoSrc,
        ]);

        $posterSquareAttr = $posterSquareSrc
            ? "\n      poster-square-src=\"" . $this->esc($posterSquareSrc) . '"'
            : '';

        $posterLandscapeAttr = $posterLandscapeSrc
            ? "\n      poster-landscape-src=\"" . $this->esc($posterLandscapeSrc) . '"'
            : '';

        return '<!doctype html>
<html ⚡ lang="' . $lang . '">
  <head>
    <meta charset="utf-8" />
    <title>' . $this->esc($title) . '</title>
    <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1" />

    <link rel="canonical" href="' . $this->esc($canonicalUrl) . '" />

    <meta name="description" content="' . $this->esc($description) . '" />

    <meta property="og:title" content="' . $this->esc($title) . '" />
    <meta property="og:description" content="' . $this->esc($description) . '" />
    <meta property="og:type" content="article" />
    <meta property="og:url" content="' . $this->esc($canonicalUrl) . '" />
    <meta property="og:image" content="' . $this->esc($posterPortraitSrc) . '" />

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="' . $this->esc($title) . '" />
    <meta name="twitter:description" content="' . $this->esc($description) . '" />
    <meta name="twitter:image" content="' . $this->esc($posterPortraitSrc) . '" />

    <script async src="https://cdn.ampproject.org/v0.js"></script>
    <script async custom-element="amp-story" src="https://cdn.ampproject.org/v0/amp-story-1.0.js"></script>

    <style amp-boilerplate>
      body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}
      @-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}
      @-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}
      @-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}
      @-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}
      @keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}
    </style>
    <noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>

    <style amp-custom>
      :root{--pad-top:92px;--pad-x:34px;--pad-bottom:56px}
      body,.pad,.brand,.h1,.h2,.meta,.ref,.text,.quote,.pill,.bullets,.prayer,.btn{
        font-family:ui-sans-serif,system-ui,-apple-system,"Segoe UI",Roboto,Helvetica,Arial,"Apple Color Emoji","Segoe UI Emoji";
        -webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale
      }
      .pad{padding:var(--pad-top) var(--pad-x) var(--pad-bottom)}
      .brand{font-size:16px;letter-spacing:.3px;opacity:.95;font-weight:700}
      .h1{font-size:56px;line-height:1.02;font-weight:900;letter-spacing:-.6px}
      .h2{font-size:34px;line-height:1.08;font-weight:900;letter-spacing:-.3px}
      .meta{font-size:20px;margin-top:10px;opacity:.95;font-weight:700}
      .ref{font-size:22px;margin-top:14px;font-weight:900;opacity:.98}
      .text{font-size:22px;line-height:1.22;margin-top:16px;font-weight:650}
      .bullets{margin-top:16px;font-size:22px;line-height:1.22;padding-left:0}
      .bullets li{margin:10px 0;list-style:none}
      .pill{display:inline-block;padding:10px 14px;border-radius:999px;margin-top:14px;font-size:18px;font-weight:900}
      .quote{margin-top:16px;padding:14px 16px;border-left:4px solid rgba(255,255,255,.85);border-radius:14px;font-size:22px;line-height:1.22;font-weight:750}
      .prayer{margin-top:14px;font-size:22px;line-height:1.22;font-style:italic;opacity:.96;font-weight:650}
      .btn{display:inline-block;margin-top:22px;padding:14px 18px;border-radius:14px;font-weight:950;text-decoration:none;font-size:18px;letter-spacing:.2px}
      .theme-dark .overlay{background:linear-gradient(180deg,rgba(0,0,0,.48) 0%,rgba(0,0,0,.30) 45%,rgba(0,0,0,.40) 100%)}
      .theme-light .overlay{background:linear-gradient(180deg,rgba(255,255,255,.18) 0%,rgba(0,0,0,.10) 55%,rgba(0,0,0,.20) 100%)}

      .theme-dark .brand,.theme-dark .text,.theme-dark .meta,.theme-dark .ref,.theme-dark .prayer{color:#f6f6f6}
      .theme-dark .h1,.theme-dark .h2{color:#f3d48b;text-shadow:0 2px 10px rgba(0,0,0,.75)}
      .theme-dark .quote{background:rgba(0,0,0,.40);color:#f6f6f6;border-left-color:rgba(243,212,139,.9)}
      .theme-dark .pill{background:rgba(243,212,139,.18);border:1px solid rgba(243,212,139,.48);color:#f3d48b}
      .theme-dark .bullets li{background:rgba(0,0,0,.38);color:#f6f6f6;padding:10px 12px;border-radius:12px}
      .theme-dark .btn{background:rgba(245,245,245,.94);color:#111}

      .theme-light .brand,.theme-light .text,.theme-light .meta,.theme-light .ref,.theme-light .prayer{color:#171717;text-shadow:0 1px 6px rgba(255,255,255,.35)}
      .theme-light .h1,.theme-light .h2{color:#2b1e0c;text-shadow:0 2px 12px rgba(255,255,255,.45)}
      .theme-light .quote{background:rgba(255,255,255,.60);color:#171717;border-left-color:rgba(43,30,12,.55)}
      .theme-light .pill{background:rgba(43,30,12,.12);border:1px solid rgba(43,30,12,.22);color:#2b1e0c}
      .theme-light .bullets li{background:rgba(255,255,255,.62);color:#171717;padding:10px 12px;border-radius:12px}
      .theme-light .btn{background:rgba(43,30,12,.94);color:#fff}
    </style>

    <script type="application/ld+json">' . $jsonLd . '</script>
  </head>

  <body>
    <amp-story
      standalone
      title="' . $this->esc($title) . '"
      publisher="' . $this->esc($publisherName) . '"
      publisher-logo-src="' . $this->esc($publisherLogoSrc) . '"
      poster-portrait-src="' . $this->esc($posterPortraitSrc) . '"' . $posterSquareAttr . $posterLandscapeAttr . '
    >
      ' . $pagesHtml . '
    </amp-story>
  </body>
</html>';
    }

    private function renderPagesHtml(array $story): string
    {
        $pages = $story['pages'] ?? [];
        $out = [];

        foreach ($pages as $idx => $p) {
            $pageNumber = $idx + 1;

            $id = $this->esc($this->normalizePageId($p['id'] ?? ('p' . $pageNumber)));
            $themeClass = (($p['theme'] ?? 'dark') === 'light') ? 'theme-light' : 'theme-dark';

            $bgSrc = $this->esc(
                $this->absUrl(
                    data_get($p, 'background.src', data_get($story, 'poster.src', $story['posterPortraitSrc'] ?? '')),
                    config('app.url')
                ) ?? ''
            );

            $bgAlt = $this->esc(
                data_get($p, 'background.alt', data_get($story, 'poster.alt', $story['title'] ?? 'Background'))
            );

            $heading = $this->esc($p['heading'] ?? '');
            $subheading = $this->esc($p['subheading'] ?? '');
            $reference = $this->esc($p['reference'] ?? '');
            $text = $this->esc($p['text'] ?? '');
            $quote = $this->esc($p['quote'] ?? '');
            $refrain = $this->esc($p['refrain'] ?? '');
            $prayer = $this->esc($p['prayer'] ?? '');

            $bullets = is_array($p['bullets'] ?? null) ? array_slice($p['bullets'], 0, 5) : [];
            $bulletsHtml = '';

            if (count($bullets)) {
                $lis = array_map(fn ($b) => '<li>' . $this->esc((string) $b) . '</li>', $bullets);
                $bulletsHtml = '<ul class="bullets">' . implode('', $lis) . '</ul>';
            }

            $ctaUrl = data_get($p, 'cta.url');

            // Segurança extra:
            // se algum builder ainda mandar canonicalUrl como CTA, força contentUrl quando existir.
            if ($ctaUrl && isset($story['contentUrl']) && $ctaUrl === ($story['canonicalUrl'] ?? null)) {
                $ctaUrl = $story['contentUrl'];
            }

            $ctaUrl = $this->absUrl($ctaUrl, config('app.url'));
            $ctaLabel = $this->esc(data_get($p, 'cta.label', $story['ctaLabel'] ?? 'Abrir'));

            $ctaHtml = $ctaUrl
                ? '<a class="btn" href="' . $this->esc($ctaUrl) . '">' . $ctaLabel . '</a>'
                : '';

            $out[] = trim('
<amp-story-page id="' . $id . '" class="' . $themeClass . '">
  <amp-story-grid-layer template="fill">
    <amp-img src="' . $bgSrc . '" width="1080" height="1920" layout="responsive" alt="' . $bgAlt . '"></amp-img>
  </amp-story-grid-layer>

  <amp-story-grid-layer template="fill" class="overlay"></amp-story-grid-layer>

  <amp-story-grid-layer template="vertical" class="pad">
    <div class="brand">' . $this->esc($story['publisherName'] ?? 'Tio Ben IA') . '</div>

    <div class="' . ($pageNumber === 1 ? 'h1' : 'h2') . '">' . $heading . '</div>
    ' . ($subheading ? '<div class="meta">' . $subheading . '</div>' : '') . '
    ' . ($reference ? '<div class="ref">' . $reference . '</div>' : '') . '
    ' . ($text ? '<div class="text">' . $text . '</div>' : '') . '
    ' . ($quote ? '<div class="quote">' . $quote . '</div>' : '') . '
    ' . ($refrain ? '<div class="pill">' . $refrain . '</div>' : '') . '
    ' . $bulletsHtml . '
    ' . ($prayer ? '<div class="prayer">' . $prayer . '</div>' : '') . '
    ' . $ctaHtml . '
  </amp-story-grid-layer>
</amp-story-page>');
        }

        return implode("\n", $out);
    }

    private function getStoryCanonicalUrl(array $story): string
    {
        $base = rtrim((string) config('app.url'), '/');

        $url = $story['storyCanonicalUrl']
            ?? $story['storyUrl']
            ?? $story['canonicalUrl']
            ?? '';

        return $this->absUrl($url, $base) ?? '';
    }

    private function getPosterPortraitSrc(array $story): string
    {
        $base = rtrim((string) config('app.url'), '/');

        $url = $story['posterPortraitSrc']
            ?? data_get($story, 'poster.src')
            ?? $story['posterSrc']
            ?? '';

        return $this->absUrl($url, $base) ?? '';
    }

    private function getPosterSquareSrc(array $story): string
    {
        $base = rtrim((string) config('app.url'), '/');

        $url = $story['posterSquareSrc']
            ?? data_get($story, 'poster.square')
            ?? '';

        return $this->absUrl($url, $base) ?? '';
    }

    private function getPosterLandscapeSrc(array $story): string
    {
        $base = rtrim((string) config('app.url'), '/');

        $url = $story['posterLandscapeSrc']
            ?? data_get($story, 'poster.landscape')
            ?? '';

        return $this->absUrl($url, $base) ?? '';
    }

    private function absUrl(mixed $url, mixed $fallbackBase = null): ?string
    {
        $url = trim((string) ($url ?? ''));

        if ($url === '') {
            return null;
        }

        if (preg_match('#^https?://#i', $url)) {
            return $url;
        }

        if (str_starts_with($url, '//')) {
            return 'https:' . $url;
        }

        if (str_starts_with($url, '/')) {
            $base = rtrim((string) ($fallbackBase ?? config('app.url')), '/');

            if ($base === '') {
                return $url;
            }

            return $base . $url;
        }

        return $url;
    }

    private function normalizePageId(mixed $id): string
    {
        $id = strtolower(trim((string) ($id ?? 'p1')));
        $id = preg_replace('/[^a-z0-9\-_]+/', '-', $id);
        $id = trim($id, '-_');

        if ($id === '') {
            return 'p1';
        }

        if (preg_match('/^[0-9]/', $id)) {
            return 'p-' . $id;
        }

        return $id;
    }

    private function buildJsonLd(array $story, array $resolved = []): string
    {
        $base = rtrim((string) config('app.url'), '/');

        $canonicalUrl = $resolved['canonicalUrl']
            ?? $this->getStoryCanonicalUrl($story);

        $posterPortraitSrc = $resolved['posterPortraitSrc']
            ?? $this->getPosterPortraitSrc($story);

        $posterSquareSrc = $resolved['posterSquareSrc']
            ?? $this->getPosterSquareSrc($story);

        $posterLandscapeSrc = $resolved['posterLandscapeSrc']
            ?? $this->getPosterLandscapeSrc($story);

        $publisherLogoSrc = $resolved['publisherLogoSrc']
            ?? ($this->absUrl($story['publisherLogoSrc'] ?? '', $base) ?? '');

        $contentUrl = $this->absUrl(
            $story['contentUrl'] ?? $story['contentCanonicalUrl'] ?? '',
            $base
        );

        $isoDate = $story['isoDate']
            ?? $story['date']
            ?? null;

        $images = array_values(array_filter([
            $posterPortraitSrc,
            $posterSquareSrc,
            $posterLandscapeSrc,
        ]));

        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $story['title'] ?? '',
            'description' => $story['description'] ?? '',
            'url' => $canonicalUrl,
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $canonicalUrl,
            ],
            'image' => $images,
            'author' => [
                '@type' => 'Organization',
                'name' => $story['publisherName'] ?? 'Tio Ben IA',
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => $story['publisherName'] ?? 'Tio Ben IA',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => $publisherLogoSrc,
                ],
            ],
        ];

        if ($isoDate) {
            $jsonLd['datePublished'] = $isoDate;
            $jsonLd['dateModified'] = $isoDate;
        }

        if ($contentUrl) {
            $jsonLd['isBasedOn'] = [
                '@type' => 'WebPage',
                '@id' => $contentUrl,
            ];
        }

        return json_encode(
            $jsonLd,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
        ) ?: '{}';
    }
}