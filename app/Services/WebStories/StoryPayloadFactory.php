<?php

namespace App\Services\WebStories;

use App\Services\LiturgiaApiService;
use App\Services\LiturgiaNormalizer;
use Carbon\Carbon;
use InvalidArgumentException;

class StoryPayloadFactory
{
    public function __construct(
        private readonly LiturgiaApiService $liturgiaApi,
        private readonly LiturgiaNormalizer $liturgiaNormalizer,
        private readonly LiturgiaStoryBuilder $liturgiaBuilder,
        private readonly TercoStoryBuilder $tercoBuilder,
    ) {}

    public function buildLiturgiaByIsoDate(string $isoDate): array
    {
        $date = $this->validateIsoDate($isoDate);

        $site = $this->siteUrl();
        $slugDate = $date->format('d-m-Y');
        $slug = "liturgia-{$slugDate}";

        $storyUrl = "{$site}/web-stories/{$slug}/";
        $contentUrl = "{$site}/liturgia-diaria/{$slugDate}";

        $day = (int) $date->format('d');
        $month = (int) $date->format('m');
        $year = (int) $date->format('Y');

        $raw = $this->liturgiaApi->fetchByDate($day, $month, $year);
        $liturgia = $this->liturgiaNormalizer->normalize($raw, $day, $month, $year);

        $payload = [
            'kind' => 'liturgia',
            'lang' => 'pt-BR',

            'isoDate' => $isoDate,
            'slugDate' => $slugDate,
            'slug' => $slug,

            'siteUrl' => $site,

            // Canonical correto da Web Story.
            'storyUrl' => $storyUrl,
            'storyCanonicalUrl' => $storyUrl,

            // Mantido por compatibilidade com renderer antigo.
            // Atenção: agora canonicalUrl aponta para a própria Story.
            'canonicalUrl' => $storyUrl,

            // Página normal relacionada.
            'contentUrl' => $contentUrl,
            'contentCanonicalUrl' => $contentUrl,

            'title' => "Liturgia Diária de {$slugDate}",
            'description' => "Web Story com a liturgia diária, leituras, salmo, evangelho e reflexão para {$slugDate}.",

            'publisherName' => $this->publisherName(),
            'publisherLogoSrc' => $this->publisherLogoSrc(),

            'posterPortraitSrc' => config(
                'webstories.liturgia_poster_portrait',
                "{$site}/images/stories/liturgia-poster-640x853.jpg"
            ),
            'posterSquareSrc' => config(
                'webstories.liturgia_poster_square',
                "{$site}/images/stories/liturgia-poster-640x640.jpg"
            ),
            'posterLandscapeSrc' => config(
                'webstories.liturgia_poster_landscape',
                "{$site}/images/stories/liturgia-poster-853x640.jpg"
            ),

            'ctaLabel' => 'Ler liturgia completa',
            'liturgia' => $liturgia,
        ];

        $story = $this->liturgiaBuilder->build($payload);

        return $this->mergeRequiredMeta($story, $payload);
    }

    public function buildTercoByIsoDate(string $isoDate): array
    {
        $date = $this->validateIsoDate($isoDate);

        $site = $this->siteUrl();
        $slugDate = $date->format('d-m-Y');
        $slug = "terco-{$slugDate}";

        $storyUrl = "{$site}/web-stories/{$slug}/";
        $contentUrl = "{$site}/santo-terco/";

        $payload = [
            'kind' => 'terco',
            'lang' => 'pt-BR',

            'isoDate' => $isoDate,
            'slugDate' => $slugDate,
            'slug' => $slug,

            'siteUrl' => $site,

            // Canonical correto da Web Story.
            'storyUrl' => $storyUrl,
            'storyCanonicalUrl' => $storyUrl,

            // Mantido por compatibilidade com renderer antigo.
            // Atenção: agora canonicalUrl aponta para a própria Story.
            'canonicalUrl' => $storyUrl,

            // Página normal relacionada.
            'contentUrl' => $contentUrl,
            'contentCanonicalUrl' => $contentUrl,

            'title' => "Santo Terço de {$slugDate}",
            'description' => "Web Story para rezar o Santo Terço com os mistérios do dia, orações e meditação.",

            'publisherName' => $this->publisherName(),
            'publisherLogoSrc' => $this->publisherLogoSrc(),

            'posterSrc' => config(
                'webstories.terco_poster',
                "{$site}/images/stories/terco-default.jpg"
            ),
            'posterPortraitSrc' => config(
                'webstories.terco_poster_portrait',
                "{$site}/images/stories/terco-poster-640x853.jpg"
            ),
            'posterSquareSrc' => config(
                'webstories.terco_poster_square',
                "{$site}/images/stories/terco-poster-640x640.jpg"
            ),
            'posterLandscapeSrc' => config(
                'webstories.terco_poster_landscape',
                "{$site}/images/stories/terco-poster-853x640.jpg"
            ),

            'bgDarkSrc' => config(
                'webstories.terco_bg_dark',
                "{$site}/images/stories/liturgia-bg-dark.jpg"
            ),
            'bgLightSrc' => config(
                'webstories.terco_bg_light',
                "{$site}/images/stories/liturgia-bg-light.jpg"
            ),

            'ctaLabel' => 'Rezar o terço completo',
        ];

        $story = $this->tercoBuilder->build($payload);

        return $this->mergeRequiredMeta($story, $payload);
    }

    private function validateIsoDate(string $isoDate): Carbon
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $isoDate)) {
            throw new InvalidArgumentException('Data inválida.');
        }

        [$year, $month, $day] = array_map('intval', explode('-', $isoDate));

        if (!checkdate($month, $day, $year)) {
            throw new InvalidArgumentException('Data inexistente.');
        }

        return Carbon::create($year, $month, $day, 12, 0, 0, config('app.timezone', 'America/Sao_Paulo'));
    }

    private function siteUrl(): string
    {
        return rtrim((string) config('app.url'), '/');
    }

    private function publisherName(): string
    {
        return (string) config('webstories.publisher_name', 'Tio Ben IA');
    }

    private function publisherLogoSrc(): string
    {
        $site = $this->siteUrl();

        return (string) config('webstories.publisher_logo', "{$site}/images/logo-amp.webp");
    }

    private function mergeRequiredMeta(array $story, array $payload): array
    {
        // Garante que dados críticos não sejam perdidos caso algum Builder não os repasse.
        foreach ($payload as $key => $value) {
            if (!array_key_exists($key, $story)) {
                $story[$key] = $value;
            }
        }

        // Força canonical correto da Web Story.
        $story['storyCanonicalUrl'] = $payload['storyCanonicalUrl'];
        $story['canonicalUrl'] = $payload['storyCanonicalUrl'];
        $story['storyUrl'] = $payload['storyUrl'];
        $story['contentUrl'] = $payload['contentUrl'];
        $story['contentCanonicalUrl'] = $payload['contentCanonicalUrl'];

        return $story;
    }
}