<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateDiscoverCoverJob;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PostCoverController extends Controller
{
    public function generate(Request $request, Post $post): RedirectResponse
    {
        // Evita spam de clique se já estiver processando
        if (($post->cover_generation_status ?? '') === 'processing') {
            return back()->with('status', 'Capa já está em processamento.');
        }

        // Marca como processing (com sync faz mais sentido que queued)
        $post->update([
            'cover_generation_status' => 'processing',
            'cover_generated_at' => null,
        ]);

        try {
            // Com QUEUE_CONNECTION=sync, isso executa imediatamente
            GenerateDiscoverCoverJob::dispatchSync($post->id);

            // Recarrega (caso o job tenha atualizado cover_image_url)
            $post->refresh();

            $post->update([
                'cover_generation_status' => 'done',
                'cover_generated_at' => now(),
            ]);

            return back()->with('status', 'Capa gerada com sucesso.');
        } catch (\Throwable $e) {
            $post->update([
                'cover_generation_status' => 'failed',
            ]);

            // Se quiser, logue o erro
            report($e);

            return back()->with('status', 'Falha ao gerar a capa. Tente novamente.');
        }
    }
}