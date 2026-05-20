<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class LiturgiaApiService
{
    public function fetchByDate(int $day, int $month, int $year): array
    {
        $key = "liturgia:v2:$year-".str_pad((string)$month,2,'0',STR_PAD_LEFT)."-".str_pad((string)$day,2,'0',STR_PAD_LEFT);

        return Cache::remember($key, now()->addHours(24), function () use ($day, $month, $year) {
            $url = "https://liturgia.up.railway.app/v2/";

            $res = Http::retry(2, 250)
                ->timeout(15)
                ->acceptJson()
                ->get($url, [
                    'dia' => $day,
                    'mes' => $month,
                    'ano' => $year,
                ]);

            if (!$res->ok()) {
                throw new \RuntimeException("Liturgia API error: {$res->status()}");
            }

            return $res->json();
        });
    }
}
