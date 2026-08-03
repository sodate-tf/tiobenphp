<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\PublishingPipelineController;
use App\Http\Controllers\Controller;
use App\Models\AdminGenerationRun;
use App\Services\ArticleFormatter;
use App\Services\IndexNowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PipelineGeneratorController extends Controller
{
    private const RUN_STALE_TIMEOUT_MINUTES = 20;

    public function create()
    {
        $this->markStaleRunsAsTimedOut((int) auth()->id());

        $runs = AdminGenerationRun::query()
            ->where('created_by', auth()->id())
            ->latest('created_at')
            ->limit(10)
            ->get();

        return view('admin.pipeline-generator.create', [
            'runs' => $runs,
            'lastRunId' => session('last_generation_run_id'),
        ]);
    }

    public function store(
        Request $request,
    ): RedirectResponse {
        $data = $request->validate([
            'topic' => ['required', 'string', 'max:255'],
            'agent' => ['nullable', 'in:theme,saint'],
            'language' => ['nullable', 'in:pt-BR,en-US'],
            'focusKeywords' => ['nullable', 'string', 'max:2000'],
            'date' => ['nullable', 'date'],
            'sourceText' => ['nullable', 'string'],
            'liturgySource' => ['nullable', 'string'],
        ]);

        $payload = [
            'topic' => (string) $data['topic'],
            'agent' => (string) ($data['agent'] ?? 'theme'),
            'language' => (string) ($data['language'] ?? 'pt-BR'),
            'focusKeywords' => (string) ($data['focusKeywords'] ?? ''),
            'date' => $data['date'] ?? null,
            'sourceText' => (string) ($data['sourceText'] ?? ''),
            'liturgySource' => (string) ($data['liturgySource'] ?? ''),
        ];

        $run = AdminGenerationRun::create([
            'topic' => (string) $payload['topic'],
            'status' => 'queued',
            'stage' => 'queued',
            'message' => 'Aguardando inicio do processamento.',
            'created_by' => auth()->id(),
        ]);

        $jsonRequestFactory = function (array $body): Request {
            return Request::create(
                uri: '/',
                method: 'POST',
                parameters: [],
                cookies: [],
                files: [],
                server: ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'],
                content: json_encode($body, JSON_UNESCAPED_UNICODE)
            );
        };

        dispatch(function () use ($payload, $jsonRequestFactory, $run): void {
            try {
                @ignore_user_abort(true);
                @set_time_limit(1200);

                $run->update([
                    'status' => 'running',
                    'stage' => 'generating',
                    'message' => 'Gerando artigo via IA...',
                    'started_at' => now(),
                ]);

                /** @var PublishingPipelineController $pipeline */
                $pipeline = app(PublishingPipelineController::class);
                /** @var ArticleFormatter $formatter */
                $formatter = app(ArticleFormatter::class);
                /** @var IndexNowService $indexNow */
                $indexNow = app(IndexNowService::class);

                $generateRes = $pipeline->generateArticle($jsonRequestFactory($payload));
                $generateJson = $generateRes->getData(true);
                if (($generateJson['success'] ?? false) !== true || empty($generateJson['id'])) {
                    $run->update([
                        'status' => 'failed',
                        'stage' => 'generating',
                        'message' => 'Falha ao gerar artigo: ' . json_encode($generateJson, JSON_UNESCAPED_UNICODE),
                        'finished_at' => now(),
                    ]);
                    Log::error('Admin generator: falha no generateArticle', ['response' => $generateJson, 'payload' => $payload]);
                    return;
                }

                $articleId = (string) $generateJson['id'];
                $run->update([
                    'pipeline_article_id' => $articleId,
                    'stage' => 'formatting',
                    'message' => 'Artigo gerado. Iniciando formatacao HTML...',
                    'updated_at' => now(),
                ]);

                $formatRes = $pipeline->formatArticle($jsonRequestFactory(['id' => $articleId]), $formatter);
                $formatJson = $formatRes->getData(true);
                if (($formatJson['success'] ?? false) !== true) {
                    $run->update([
                        'status' => 'failed',
                        'stage' => 'formatting',
                        'message' => 'Falha na formatacao: ' . json_encode($formatJson, JSON_UNESCAPED_UNICODE),
                        'finished_at' => now(),
                    ]);
                    Log::error('Admin generator: falha no formatArticle', ['article_id' => $articleId, 'response' => $formatJson]);
                    return;
                }

                $run->update([
                    'stage' => 'publishing',
                    'message' => 'Formatado. Executando SEO e publicacao...',
                    'updated_at' => now(),
                ]);

                $publishRes = $pipeline->seoAndPublish($jsonRequestFactory(['id' => $articleId]), $formatter, $indexNow);
                $publishJson = $publishRes->getData(true);

                $isSuccess = (bool) ($publishJson['success'] ?? false);
                $message = $this->buildPublishMessage($publishJson);

                $run->update([
                    'status' => $isSuccess ? 'completed' : 'failed',
                    'stage' => $isSuccess ? 'completed' : 'publishing',
                    'message' => $message,
                    'finished_at' => now(),
                    'updated_at' => now(),
                ]);

                Log::info('Admin generator: pipeline concluida', [
                    'article_id' => $articleId,
                    'publish_response' => $publishJson,
                ]);
            } catch (\Throwable $e) {
                $run->update([
                    'status' => 'failed',
                    'stage' => 'error',
                    'message' => 'Erro interno: ' . $e->getMessage(),
                    'finished_at' => now(),
                    'updated_at' => now(),
                ]);
                Log::error('Admin generator background error', [
                    'error' => $e->getMessage(),
                    'payload' => $payload,
                ]);
            }
        })->afterResponse();

        return back()->with(
            'success',
            'Processamento iniciado em segundo plano. Acompanhe o andamento nesta mesma tela.'
        )->with('last_generation_run_id', $run->id);
    }

    public function status(string $id)
    {
        $run = AdminGenerationRun::query()
            ->where('id', $id)
            ->where('created_by', auth()->id())
            ->firstOrFail();

        $this->markSingleRunAsTimedOut($run);

        return response()->json([
            'id' => $run->id,
            'status' => $run->status,
            'stage' => $run->stage,
            'message' => $run->message,
            'pipeline_article_id' => $run->pipeline_article_id,
            'started_at' => optional($run->started_at)?->toDateTimeString(),
            'finished_at' => optional($run->finished_at)?->toDateTimeString(),
            'created_at' => optional($run->created_at)?->toDateTimeString(),
        ]);
    }

    private function markStaleRunsAsTimedOut(int $userId): void
    {
        $runs = AdminGenerationRun::query()
            ->where('created_by', $userId)
            ->whereIn('status', ['queued', 'running'])
            ->get();

        foreach ($runs as $run) {
            $this->markSingleRunAsTimedOut($run);
        }
    }

    private function markSingleRunAsTimedOut(AdminGenerationRun $run): void
    {
        $isRunning = in_array($run->status, ['queued', 'running'], true);
        $staleLimit = now()->subMinutes(self::RUN_STALE_TIMEOUT_MINUTES);
        if (!$isRunning || !optional($run->updated_at)->lt($staleLimit)) {
            return;
        }

        $run->status = 'failed';
        $run->stage = 'timeout';
        $run->message = 'Execucao marcada como timeout. O processo ficou sem progresso por mais de '
            . self::RUN_STALE_TIMEOUT_MINUTES
            . ' minutos. Verifique logs e tente novamente.';
        $run->finished_at = now();
        $run->save();
    }

    private function buildPublishMessage(array $publishJson): string
    {
        $base = (string) ($publishJson['message'] ?? 'Concluido.');
        $details = trim((string) ($publishJson['details'] ?? ''));

        if ($details !== '') {
            return $base . ' | details: ' . mb_substr($details, 0, 900);
        }

        if (!empty($publishJson['quality']) && is_array($publishJson['quality'])) {
            $q = $publishJson['quality'];
            return $base
                . ' | quality: words=' . (int) ($q['word_count'] ?? 0)
                . ', h2=' . (int) ($q['h2_count'] ?? 0)
                . ', faq=' . (int) ($q['faq_questions'] ?? 0);
        }

        if (($publishJson['success'] ?? null) === false) {
            return $base . ' | raw: ' . mb_substr(json_encode($publishJson, JSON_UNESCAPED_UNICODE), 0, 900);
        }

        return $base;
    }
}
