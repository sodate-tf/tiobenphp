<?php

namespace App\Services\WebStories;

use Carbon\Carbon;

class LiturgiaStoryBuilder
{
    private function compact(?string $s): string
    {
        return trim(preg_replace('/\s+/', ' ', (string) $s));
    }

    private function limitChars(string $s, int $max = 220): string
    {
        $t = $this->compact($s);

        if ($t === '') {
            return '';
        }

        if (mb_strlen($t) <= $max) {
            return $t;
        }

        $cut = mb_substr($t, 0, $max);
        $cut = rtrim($cut, " ,.;:!?");

        return $cut . '…';
    }

    private function pickQuote(string $text): string
    {
        $t = $this->compact($text);

        if ($t === '') {
            return '';
        }

        $parts = preg_split('/[.?!]\s+/', $t) ?: [];
        $parts = array_values(array_filter(array_map(fn ($x) => $this->compact($x), $parts)));

        $candidate = '';

        foreach ($parts as $p) {
            $len = mb_strlen($p);

            if ($len >= 60 && $len <= 160) {
                $candidate = $p;
                break;
            }
        }

        if (!$candidate) {
            $candidate = $parts[0] ?? '';
        }

        return $candidate ? '“' . $candidate . '”' : '';
    }

    private function isoToSlugBR(string $iso): string
    {
        [$y, $m, $d] = explode('-', $iso);

        return "{$d}-{$m}-{$y}";
    }

    private function pickBlock($liturgia, string $key, array $fallbackPaths = []): array
    {
        $ref = $this->compact(data_get($liturgia, "leiturasFull.$key.0.referencia") ?? '');
        $texto = $this->compact(data_get($liturgia, "leiturasFull.$key.0.texto") ?? '');
        $refrao = $this->compact(data_get($liturgia, "leiturasFull.$key.0.refrao") ?? '');

        if ($ref === '' && $texto === '' && $refrao === '') {
            $ref = $this->compact(data_get($liturgia, "leituras.$key.0.referencia") ?? '');
            $texto = $this->compact(data_get($liturgia, "leituras.$key.0.texto") ?? '');
            $refrao = $this->compact(data_get($liturgia, "leituras.$key.0.refrao") ?? '');
        }

        if ($ref === '' && $texto === '' && $refrao === '' && $fallbackPaths) {
            foreach ($fallbackPaths as $p) {
                $v = $this->compact(data_get($liturgia, $p) ?? '');

                if ($v === '') {
                    continue;
                }

                if (str_contains($p, 'referencia')) {
                    $ref = $v;
                } elseif (str_contains($p, 'refrao')) {
                    $refrao = $v;
                } else {
                    $texto = $v;
                }
            }
        }

        return [
            'referencia' => $ref,
            'texto' => $texto,
            'refrao' => $refrao,
        ];
    }

    public function build(array $params): array
    {
        $isoDate = $params['isoDate'];
        $liturgia = $params['liturgia'];

        $siteUrl = rtrim((string) ($params['siteUrl'] ?? config('app.url')), '/');

        // Canonical correto da Web Story.
        $storyUrl = $params['storyUrl']
            ?? "{$siteUrl}/web-stories/liturgia-{$this->isoToSlugBR($isoDate)}/";

        $storyCanonicalUrl = $params['storyCanonicalUrl']
            ?? $params['canonicalUrl']
            ?? $storyUrl;

        // URL da página completa da liturgia.
        $contentUrl = $params['contentUrl']
            ?? $params['contentCanonicalUrl']
            ?? "{$siteUrl}/liturgia-diaria/{$this->isoToSlugBR($isoDate)}";

        $lang = $params['lang'] ?? 'pt-BR';

        $publisherName = $params['publisherName'] ?? 'Tio Ben IA';
        $publisherLogoSrc = $params['publisherLogoSrc'] ?? "{$siteUrl}/images/logo-amp.webp";

        $posterPortraitSrc = $params['posterPortraitSrc']
            ?? "{$siteUrl}/images/stories/liturgia-poster-640x853.jpg";

        $posterSquareSrc = $params['posterSquareSrc']
            ?? "{$siteUrl}/images/stories/liturgia-poster-640x640.jpg";

        $posterLandscapeSrc = $params['posterLandscapeSrc']
            ?? "{$siteUrl}/images/stories/liturgia-poster-853x640.jpg";

        $bgDarkSrc = $params['bgDarkSrc']
            ?? "{$siteUrl}/images/stories/liturgia-bg-dark.jpg";

        $bgLightSrc = $params['bgLightSrc']
            ?? "{$siteUrl}/images/stories/liturgia-bg-light.jpg";

        $dateLabel = Carbon::createFromFormat('Y-m-d', $isoDate)->format('d/m/Y');
        $slug = $params['slug'] ?? ('liturgia-' . $this->isoToSlugBR($isoDate));

        $celebration = $this->compact(
            data_get($liturgia, 'celebration')
            ?? data_get($liturgia, 'liturgia')
            ?? data_get($liturgia, 'celebracao')
            ?? ''
        );

        $primeira = $this->pickBlock($liturgia, 'primeiraLeitura', [
            'primeiraLeitura.referencia',
            'primeiraLeitura.texto',
        ]);

        $segunda = $this->pickBlock($liturgia, 'segundaLeitura', [
            'segundaLeitura.referencia',
            'segundaLeitura.texto',
        ]);

        $salmo = $this->pickBlock($liturgia, 'salmo', [
            'salmo.referencia',
            'salmo.refrao',
            'salmo.texto',
        ]);

        $evangelho = $this->pickBlock($liturgia, 'evangelho', [
            'evangelho.0.referencia',
            'evangelho.0.texto',
            'evangelho.referencia',
            'evangelho.texto',
        ]);

        $gospelText = $evangelho['texto'];
        $gospelRef = $evangelho['referencia'];

        $themeText = $this->compact(
            data_get($liturgia, 'themeText')
            ?? data_get($liturgia, 'reflexaoResumo')
            ?? ''
        );

        if ($themeText === '') {
            $themeText = 'A Palavra de hoje nos convida a acolher a graça de Deus e viver com fidelidade.';
        }

        $pages = [];

        $pages[] = [
            'id' => 'cover',
            'background' => [
                'type' => 'image',
                'src' => $posterPortraitSrc,
                'alt' => 'Liturgia',
            ],
            'heading' => 'Liturgia de Hoje',
            'subheading' => $dateLabel,
            'text' => 'Leituras • Salmo • Evangelho',
            'cta' => [
                'label' => 'Ler no site',
                'url' => $contentUrl,
            ],
        ];

        $pages[] = [
            'id' => 'theme',
            'background' => [
                'type' => 'image',
                'src' => $bgDarkSrc,
                'alt' => 'Liturgia escura',
            ],
            'heading' => 'Hoje na Igreja',
            'subheading' => $celebration ?: $dateLabel,
            'text' => $this->limitChars($themeText, 200),
            'quote' => $this->pickQuote($gospelText) ?: null,
            'cta' => [
                'label' => 'Rezar no site',
                'url' => $contentUrl,
            ],
        ];

        $pages[] = [
            'id' => 'reading1',
            'background' => [
                'type' => 'image',
                'src' => $bgDarkSrc,
                'alt' => 'Liturgia escura',
            ],
            'heading' => '1ª Leitura',
            'reference' => $primeira['referencia'] ?: '1ª Leitura',
            'text' => $primeira['texto']
                ? $this->limitChars($primeira['texto'], 210)
                : 'Leia a passagem completa e reze com calma no Tio Ben IA.',
            'cta' => [
                'label' => 'Abrir 1ª leitura',
                'url' => $contentUrl,
            ],
        ];

        if ($segunda['referencia'] || $segunda['texto']) {
            $pages[] = [
                'id' => 'reading2',
                'background' => [
                    'type' => 'image',
                    'src' => $bgDarkSrc,
                    'alt' => 'Liturgia escura',
                ],
                'heading' => '2ª Leitura',
                'reference' => $segunda['referencia'] ?: '2ª Leitura',
                'text' => $segunda['texto']
                    ? $this->limitChars($segunda['texto'], 210)
                    : 'Leia a passagem completa e reze com calma no Tio Ben IA.',
                'cta' => [
                    'label' => 'Abrir 2ª leitura',
                    'url' => $contentUrl,
                ],
            ];
        }

        $pages[] = [
            'id' => 'psalm',
            'background' => [
                'type' => 'image',
                'src' => $bgDarkSrc,
                'alt' => 'Liturgia escura',
            ],
            'heading' => 'Salmo',
            'reference' => $salmo['referencia'] ?: 'Salmo',
            'refrain' => $this->limitChars($salmo['refrao'] ?: 'Refrão do salmo', 140),
            'text' => $salmo['texto']
                ? $this->limitChars($salmo['texto'], 190)
                : 'Reze o salmo completo no Tio Ben IA.',
            'cta' => [
                'label' => 'Rezar o Salmo',
                'url' => $contentUrl,
            ],
        ];

        $pages[] = [
            'id' => 'gospel',
            'background' => [
                'type' => 'image',
                'src' => $bgDarkSrc,
                'alt' => 'Liturgia escura',
            ],
            'heading' => 'Evangelho',
            'reference' => $gospelRef ?: 'Evangelho',
            'text' => $gospelText
                ? $this->limitChars($gospelText, 220)
                : 'Abra o Evangelho completo e medite com calma.',
            'cta' => [
                'label' => 'Ler o Evangelho',
                'url' => $contentUrl,
            ],
        ];

        $pages[] = [
            'id' => 'apply',
            'theme' => 'light',
            'background' => [
                'type' => 'image',
                'src' => $bgLightSrc,
                'alt' => 'Liturgia clara',
            ],
            'heading' => 'Para viver hoje',
            'bullets' => [
                'Reserve 5 minutos de silêncio e oração antes de começar o dia.',
                'Releia o Evangelho e escolha uma atitude concreta para praticar.',
                'Faça um gesto de caridade ou reconciliação ainda hoje.',
            ],
            'prayer' => 'Senhor, ajuda-me a viver tua Palavra hoje.',
            'cta' => [
                'label' => 'Ver Liturgia Completa',
                'url' => $contentUrl,
            ],
        ];

        $pages[] = [
            'id' => 'cta',
            'theme' => 'light',
            'background' => [
                'type' => 'image',
                'src' => $bgLightSrc,
                'alt' => 'Liturgia clara',
            ],
            'heading' => 'Reze a liturgia completa',
            'text' => 'Leituras, salmo, Evangelho, orações e antífonas — tudo em um só lugar.',
            'cta' => [
                'label' => 'Abrir Liturgia Completa',
                'url' => $contentUrl,
            ],
        ];

        return [
            'type' => 'liturgy',
            'kind' => 'liturgia',
            'slug' => $slug,

            'lang' => $lang,
            'date' => $isoDate,
            'isoDate' => $isoDate,

            'title' => $params['title'] ?? "Liturgia — {$dateLabel}",
            'description' => $params['description'] ?? "Liturgia do dia {$dateLabel} no Tio Ben IA.",

            // Canonical da própria Web Story.
            'canonicalUrl' => $storyCanonicalUrl,
            'storyCanonicalUrl' => $storyCanonicalUrl,
            'storyUrl' => $storyUrl,

            // Página normal relacionada.
            'contentUrl' => $contentUrl,
            'contentCanonicalUrl' => $contentUrl,

            'siteUrl' => $siteUrl,
            'publisherName' => $publisherName,
            'publisherLogoSrc' => $publisherLogoSrc,

            'posterPortraitSrc' => $posterPortraitSrc,
            'posterSquareSrc' => $posterSquareSrc,
            'posterLandscapeSrc' => $posterLandscapeSrc,

            'poster' => [
                'src' => $posterPortraitSrc,
                'alt' => 'Liturgia',
                'width' => 1080,
                'height' => 1920,
            ],

            'ctaLabel' => $params['ctaLabel'] ?? 'Ler liturgia completa',
            'pages' => $pages,
        ];
    }
}