<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\IndexNowService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class IndexNowController extends Controller
{
    public function submitDailyLiturgy(Request $request, IndexNowService $indexNow)
    {
        $secret = (string) env('CRON_SECRET', '');

        if ($secret !== '') {
            $auth = (string) $request->bearerToken();

            if (!hash_equals($secret, $auth)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized.',
                ], 401);
            }
        }

        $date = $request->query('date')
            ? Carbon::parse((string) $request->query('date'))
            : now();

        $dateSlug = $date->format('d-m-Y');

        $siteUrl = rtrim((string) config('app.url', env('APP_URL', 'https://www.iatioben.com.br')), '/');

        $urls = [
            "{$siteUrl}/liturgia-diaria/{$dateSlug}",
            "{$siteUrl}/en/daily-mass-readings/{$dateSlug}",
        ];

        $sent = $indexNow->submit($urls);

        return response()->json([
            'success' => $sent,
            'urls' => $urls,
        ]);
    }
}