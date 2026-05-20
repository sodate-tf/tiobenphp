<?php

namespace App\Http\Controllers\WebStories;

use App\Http\Controllers\Controller;
use App\Services\WebStories\SlugParser;
use App\Services\WebStories\StoryPayloadFactory;
use App\Services\WebStories\StoryRenderer;
use Illuminate\Http\Response;
use InvalidArgumentException;

class WebStoryController extends Controller
{
    public function show(
        string $slug,
        SlugParser $parser,
        StoryRenderer $renderer,
        StoryPayloadFactory $payloadFactory
    ): Response {
        $parsed = $parser->parse($slug);

        abort_if(!$parsed, 404);
        abort_unless(!empty($parsed['kind']) && !empty($parsed['isoDate']), 404);

        try {
            $story = match ($parsed['kind']) {
                'liturgia' => $payloadFactory->buildLiturgiaByIsoDate($parsed['isoDate']),
                'terco' => $payloadFactory->buildTercoByIsoDate($parsed['isoDate']),
                default => null,
            };
        } catch (InvalidArgumentException) {
            abort(404);
        }

        abort_if(!$story, 404);

        $html = $renderer->renderAmpHtml($story);

        return response($html, 200, [
            'Content-Type' => 'text/html; charset=utf-8',
            'Cache-Control' => 'public, s-maxage=3600, stale-while-revalidate=86400',
            'X-Frame-Options' => 'SAMEORIGIN',
            'Content-Security-Policy' => "frame-ancestors 'self';",
        ]);
    }
}