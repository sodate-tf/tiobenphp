<?php

namespace App\Http\Controllers\WebStories;

use App\Http\Controllers\Controller;
use App\Services\WebStories\StoryPayloadFactory;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

class WebStoryApiController extends Controller
{
    public function liturgia(
        string $date,
        StoryPayloadFactory $payloadFactory
    ): JsonResponse {
        $this->abortUnlessIsoDate($date);

        try {
            $story = $payloadFactory->buildLiturgiaByIsoDate($date);
        } catch (InvalidArgumentException) {
            abort(404);
        }

        return response()->json($story);
    }

    public function terco(
        string $date,
        StoryPayloadFactory $payloadFactory
    ): JsonResponse {
        $this->abortUnlessIsoDate($date);

        try {
            $story = $payloadFactory->buildTercoByIsoDate($date);
        } catch (InvalidArgumentException) {
            abort(404);
        }

        return response()->json($story);
    }

    private function abortUnlessIsoDate(string $date): void
    {
        abort_unless(preg_match('/^\d{4}-\d{2}-\d{2}$/', $date), 404);

        [$year, $month, $day] = array_map('intval', explode('-', $date));

        abort_unless(checkdate($month, $day, $year), 404);
    }
}