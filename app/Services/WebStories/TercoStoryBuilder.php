<?php

namespace App\Services\WebStories;

use Carbon\Carbon;

class TercoStoryBuilder
{
    private function isoToSlugBR(string $iso): string
    {
        [$y, $m, $d] = explode('-', $iso);

        return "{$d}-{$m}-{$y}";
    }

    private function misterioDoDia(string $iso): array
    {
        $dt = Carbon::createFromFormat('Y-m-d', $iso)->setTime(12, 0, 0);
        $dow = (int) $dt->dayOfWeek; // 0=dom ... 6=sab

        $gozosos = [
            'tipo' => 'Gozoso',
            'titulo' => 'Terço Gozoso',
            'subtitulo' => 'Hoje contemplamos os Mistérios da alegria de Cristo e de Maria.',
            'misterios' => [
                '1º Mistério: A Anunciação do Anjo a Maria',
                '2º Mistério: A Visitação de Maria a Isabel',
                '3º Mistério: O Nascimento de Jesus',
                '4º Mistério: A Apresentação do Menino Jesus no Templo',
                '5º Mistério: O Encontro de Jesus no Templo',
            ],
        ];

        $dolorosos = [
            'tipo' => 'Doloroso',
            'titulo' => 'Terço Doloroso',
            'subtitulo' => 'Hoje meditamos a Paixão do Senhor e aprendemos a perseverar na fé.',
            'misterios' => [
                '1º Mistério: A Agonia de Jesus no Horto',
                '2º Mistério: A Flagelação de Jesus',
                '3º Mistério: A Coroação de espinhos',
                '4º Mistério: Jesus carrega a Cruz',
                '5º Mistério: A Crucificação e Morte de Jesus',
            ],
        ];

        $gloriosos = [
            'tipo' => 'Glorioso',
            'titulo' => 'Terço Glorioso',
            'subtitulo' => 'Hoje celebramos a vitória de Cristo e a glória de Maria na Igreja.',
            'misterios' => [
                '1º Mistério: A Ressurreição de Jesus',
                '2º Mistério: A Ascensão de Jesus ao Céu',
                '3º Mistério: A Vinda do Espírito Santo',
                '4º Mistério: A Assunção de Maria ao Céu',
                '5º Mistério: A Coroação de Maria como Rainha do Céu e da Terra',
            ],
        ];

        $luminosos = [
            'tipo' => 'Luminoso',
            'titulo' => 'Terço Luminoso',
            'subtitulo' => 'Hoje contemplamos a vida pública de Jesus e sua luz para o mundo.',
            'misterios' => [
                '1º Mistério: O Batismo de Jesus no Jordão',
                '2º Mistério: As Bodas de Caná',
                '3º Mistério: O Anúncio do Reino de Deus',
                '4º Mistério: A Transfiguração do Senhor',
                '5º Mistério: A Instituição da Eucaristia',
            ],
        ];

        // Segunda e sábado: Gozosos
        if ($dow === 1 || $dow === 6) {
            return $gozosos;
        }

        // Terça e sexta: Dolorosos
        if ($dow === 2 || $dow === 5) {
            return $dolorosos;
        }

        // Quinta: Luminosos
        if ($dow === 4) {
            return $luminosos;
        }

        // Domingo e quarta: Gloriosos
        return $gloriosos;
    }

    private function assetDefault(string $key, ?string $siteUrl = null): string
    {
        $siteUrl = rtrim((string) $siteUrl, '/');

        $map = [
            'poster' => '/images/stories/terco-default.jpg',
            'posterPortrait' => '/images/stories/terco-poster-640x853.jpg',
            'posterSquare' => '/images/stories/terco-poster-640x640.jpg',
            'posterLandscape' => '/images/stories/terco-poster-853x640.jpg',
            'bgDark' => '/images/stories/liturgia-bg-dark.jpg',
            'bgLight' => '/images/stories/liturgia-bg-light.jpg',
        ];

        $path = $map[$key] ?? '/images/stories/terco-default.jpg';

        return $siteUrl ? ($siteUrl . $path) : $path;
    }

    public function build(array $args): array
    {
        $isoDate = $args['isoDate'];

        $dt = Carbon::createFromFormat('Y-m-d', $isoDate);
        $dateLabel = $dt->format('d/m/Y');

        $siteUrl = rtrim((string) ($args['siteUrl'] ?? config('app.url')), '/');

        $slug = $args['slug'] ?? ('terco-' . $this->isoToSlugBR($isoDate));

        // Canonical correto da Web Story.
        $storyUrl = $args['storyUrl']
            ?? "{$siteUrl}/web-stories/{$slug}/";

        $storyCanonicalUrl = $args['storyCanonicalUrl']
            ?? $args['canonicalUrl']
            ?? $storyUrl;

        // URL da página completa do terço.
        $contentUrl = $args['contentUrl']
            ?? $args['contentCanonicalUrl']
            ?? "{$siteUrl}/santo-terco/";

        $publisherName = $args['publisherName'] ?? 'Tio Ben IA';
        $publisherLogoSrc = $args['publisherLogoSrc'] ?? "{$siteUrl}/images/logo-amp.webp";

        $pack = $this->misterioDoDia($isoDate);

        $posterSrc = $args['posterSrc']
            ?? $args['posterPortraitSrc']
            ?? $this->assetDefault('posterPortrait', $siteUrl);

        $posterPortraitSrc = $args['posterPortraitSrc']
            ?? $posterSrc
            ?? $this->assetDefault('posterPortrait', $siteUrl);

        $posterSquareSrc = $args['posterSquareSrc']
            ?? $this->assetDefault('posterSquare', $siteUrl);

        $posterLandscapeSrc = $args['posterLandscapeSrc']
            ?? $this->assetDefault('posterLandscape', $siteUrl);

        $bgDarkSrc = $args['bgDarkSrc']
            ?? $this->assetDefault('bgDark', $siteUrl);

        $bgLightSrc = $args['bgLightSrc']
            ?? $this->assetDefault('bgLight', $siteUrl);

        $pages = [];

        $pages[] = [
            'id' => 'cover',
            'theme' => 'dark',
            'background' => [
                'type' => 'image',
                'src' => $posterPortraitSrc,
                'alt' => "Capa do {$pack['titulo']}",
            ],
            'heading' => $pack['titulo'],
            'subheading' => $dateLabel,
            'text' => 'Reze conosco em poucos minutos.',
            'cta' => [
                'label' => 'Rezar agora no site',
                'url' => $contentUrl,
            ],
        ];

        $pages[] = [
            'id' => 'invite',
            'theme' => 'dark',
            'background' => [
                'type' => 'image',
                'src' => $bgDarkSrc,
                'alt' => 'Fundo escuro do terço',
            ],
            'heading' => 'Hoje é dia de rezar',
            'subheading' => $pack['titulo'],
            'text' => $pack['subtitulo'],
            'cta' => [
                'label' => 'Abrir Terço completo',
                'url' => $contentUrl,
            ],
        ];

        $pages[] = [
            'id' => 'misterios',
            'theme' => 'dark',
            'background' => [
                'type' => 'image',
                'src' => $bgDarkSrc,
                'alt' => 'Mistérios do terço',
            ],
            'heading' => 'Mistérios do dia',
            'subheading' => $pack['titulo'],
            'bullets' => $pack['misterios'],
            'cta' => [
                'label' => 'Rezar com guia',
                'url' => $contentUrl,
            ],
        ];

        $pages[] = [
            'id' => 'como-rezar',
            'theme' => 'light',
            'background' => [
                'type' => 'image',
                'src' => $bgLightSrc,
                'alt' => 'Como rezar o terço',
            ],
            'heading' => 'Como rezar',
            'bullets' => [
                'Sinal da Cruz e oferecimento.',
                '1 Pai-Nosso, 10 Ave-Marias e 1 Glória em cada mistério.',
                'Medite cada mistério com calma.',
            ],
            'prayer' => 'Jesus, eu confio em Vós.',
            'cta' => [
                'label' => 'Ver passo a passo',
                'url' => $contentUrl,
            ],
        ];

        $pages[] = [
            'id' => 'oracao',
            'theme' => 'light',
            'background' => [
                'type' => 'image',
                'src' => $bgLightSrc,
                'alt' => 'Oração do terço',
            ],
            'heading' => 'Uma intenção para hoje',
            'text' => 'Ofereça este terço por sua família, por quem sofre e pelas necessidades da Igreja.',
            'prayer' => 'Maria, passa à frente e intercede por nós.',
            'cta' => [
                'label' => 'Rezar completo',
                'url' => $contentUrl,
            ],
        ];

        $pages[] = [
            'id' => 'constancia',
            'theme' => 'dark',
            'background' => [
                'type' => 'image',
                'src' => $bgDarkSrc,
                'alt' => 'Constância na oração',
            ],
            'heading' => 'Reze com constância',
            'text' => 'O terço educa o coração para contemplar Cristo com os olhos de Maria.',
            'cta' => [
                'label' => 'Continuar no site',
                'url' => $contentUrl,
            ],
        ];

        $pages[] = [
            'id' => 'cta',
            'theme' => 'light',
            'background' => [
                'type' => 'image',
                'src' => $bgLightSrc,
                'alt' => 'Convite final',
            ],
            'heading' => 'Reze o terço completo',
            'text' => 'Abra o terço no Tio Ben IA e reze com foco, paz e orientação.',
            'cta' => [
                'label' => 'Abrir Terço de hoje',
                'url' => $contentUrl,
            ],
        ];

        return [
            'type' => 'rosary',
            'kind' => 'terco',
            'slug' => $slug,

            'lang' => $args['lang'] ?? 'pt-BR',
            'date' => $isoDate,
            'isoDate' => $isoDate,

            'title' => $args['title'] ?? "{$pack['titulo']} — {$dateLabel}",
            'description' => $args['description'] ?? "{$pack['titulo']} do dia {$dateLabel}. Mistérios e convite para rezar no Tio Ben IA.",

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

            'posterSrc' => $posterSrc,
            'posterPortraitSrc' => $posterPortraitSrc,
            'posterSquareSrc' => $posterSquareSrc,
            'posterLandscapeSrc' => $posterLandscapeSrc,

            'bgDarkSrc' => $bgDarkSrc,
            'bgLightSrc' => $bgLightSrc,

            'poster' => [
                'src' => $posterPortraitSrc,
                'alt' => "Capa do {$pack['titulo']}",
                'width' => 1080,
                'height' => 1920,
            ],

            'ctaLabel' => $args['ctaLabel'] ?? 'Rezar o terço completo',
            'pages' => $pages,
        ];
    }
}