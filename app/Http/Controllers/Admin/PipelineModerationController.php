<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\PublishingPipelineController;
use App\Http\Controllers\Controller;
use App\Models\PipelineArticle;
use App\Services\ArticleFormatter;
use App\Services\IndexNowService;
use Illuminate\Http\Request;

class PipelineModerationController extends Controller
{
    public function index(Request $request)
    {
        $status = (string) $request->get('status', 'needs_review');
        $allowed = ['needs_review', 'rejected', 'approved_manual', 'auto_published', 'pending'];
        if (!in_array($status, $allowed, true)) {
            $status = 'needs_review';
        }

        $q = trim((string) $request->get('q', ''));

        $articles = PipelineArticle::query()
            ->when($status !== 'all', fn ($query) => $query->where('moderation_status', $status))
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('topic', 'like', "%{$q}%")
                        ->orWhere('title', 'like', "%{$q}%")
                        ->orWhere('slug', 'like', "%{$q}%");
                });
            })
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.pipeline-moderation.index', compact('articles', 'status', 'q'));
    }

    public function show(string $id)
    {
        $article = PipelineArticle::query()->findOrFail($id);
        return view('admin.pipeline-moderation.show', compact('article'));
    }

    public function approve(
        Request $request,
        string $id,
        PublishingPipelineController $pipelineController,
        ArticleFormatter $formatter,
        IndexNowService $indexNow
    ) {
        $article = PipelineArticle::query()->findOrFail($id);

        $result = $pipelineController->publishPipelineArticle(
            article: $article,
            formatter: $formatter,
            indexNow: $indexNow,
            bypassQualityGate: true
        );

        $article->moderation_status = 'approved_manual';
        $article->review_notes = (string) $request->input('review_notes', '');
        $article->reviewed_at = now();
        $article->reviewed_by = auth()->id();
        $article->save();

        return redirect()
            ->route('admin.pipeline-moderation.show', $article->id)
            ->with('success', 'Artigo aprovado e publicado manualmente.')
            ->with('publish_result', $result);
    }

    public function reject(Request $request, string $id)
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        $article = PipelineArticle::query()->findOrFail($id);
        $article->moderation_status = 'rejected';
        $article->rejection_reason = (string) $request->input('rejection_reason');
        $article->reviewed_at = now();
        $article->reviewed_by = auth()->id();
        $article->save();

        return redirect()
            ->route('admin.pipeline-moderation.show', $article->id)
            ->with('success', 'Artigo rejeitado na moderação.');
    }
}

