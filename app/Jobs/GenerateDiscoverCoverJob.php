<?php

namespace App\Jobs;

use App\Models\Post;
use App\Services\DiscoverImageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateDiscoverCoverJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 240;
    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(public int $postId) {}

    public function handle(DiscoverImageService $service): void
    {
        $post = Post::find($this->postId);
        if (!$post) {
            return;
        }

        // Se já está pronto e tem URL, não faz nada.
        if (!empty($post->cover_image_url) && (($post->cover_generation_status ?? '') === 'done')) {
            return;
        }

        // NÃO dependa de fillable: forceFill + save
        $post->forceFill([
            'cover_generation_status' => 'processing',
            'cover_generated_at' => null,
        ])->save();

        try {
            // Esperado: service retorna URL pública (ex: https://.../storage/covers/....webp)
            $url = $service->generate($post);

            if (!empty($url)) {
                // Cache-buster pra evitar 404 cacheado no Cloudflare
                $versionedUrl = $this->appendVersion($url);

                $post->forceFill([
                    'cover_image_url' => $versionedUrl,
                    'cover_generated_at' => now(),
                    'cover_generation_status' => 'done',
                ])->save();
            } else {
                $post->forceFill([
                    'cover_generation_status' => 'failed',
                ])->save();
            }
        } catch (\Throwable $e) {
            Log::error('GenerateDiscoverCoverJob failed', [
                'postId' => $this->postId,
                'error'  => $e->getMessage(),
            ]);

            $post->forceFill([
                'cover_generation_status' => 'failed',
            ])->save();

            throw $e;
        }
    }

    private function appendVersion(string $url): string
    {
        // Se já tem querystring, não duplica.
        if (str_contains($url, '?')) {
            return $url;
        }
        return $url . '?v=' . time();
    }
}