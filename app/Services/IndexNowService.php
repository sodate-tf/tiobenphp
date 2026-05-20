<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IndexNowService
{
    public function submit(array|string $urls): bool
    {
        $urls = is_array($urls) ? $urls : [$urls];

        $urls = array_values(array_unique(array_filter(array_map(function ($url) {
            return trim((string) $url);
        }, $urls))));

        if (empty($urls)) {
            return false;
        }

        $key = trim((string) config('services.indexnow.key', env('INDEXNOW_KEY')));
        $keyLocation = trim((string) config('services.indexnow.key_location', env('INDEXNOW_KEY_LOCATION')));
        $host = trim((string) config('services.indexnow.host', env('INDEXNOW_HOST', 'www.iatioben.com.br')));
        $endpoint = trim((string) config('services.indexnow.endpoint', env('INDEXNOW_ENDPOINT', 'https://api.indexnow.org/indexnow')));

        if ($key === '' || $keyLocation === '' || $host === '') {
            Log::warning('IndexNow não enviado: configuração incompleta.', [
                'has_key' => $key !== '',
                'has_key_location' => $keyLocation !== '',
                'host' => $host,
            ]);

            return false;
        }

        try {
            $response = Http::timeout(20)
                ->acceptJson()
                ->asJson()
                ->post($endpoint, [
                    'host' => $host,
                    'key' => $key,
                    'keyLocation' => $keyLocation,
                    'urlList' => $urls,
                ]);

            Log::info('IndexNow submit', [
                'status' => $response->status(),
                'urls' => $urls,
                'body' => $response->body(),
            ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('IndexNow submit failed', [
                'error' => $e->getMessage(),
                'urls' => $urls,
            ]);

            return false;
        }
    }
}