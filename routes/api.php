<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\Api\PerguntarController;
use App\Http\Controllers\WebStories\WebStoryApiController;
use App\Http\Controllers\Api\RemotePostController;
use App\Http\Controllers\Api\PublishingPipelineController;
use App\Http\Controllers\Api\IndexNowController;

/*
|--------------------------------------------------------------------------
| API – Geral
|--------------------------------------------------------------------------
*/

Route::post('/perguntar', [PerguntarController::class, 'store'])
    ->name('api.perguntar');

/*
|--------------------------------------------------------------------------
| Web Stories API (JSON)
|--------------------------------------------------------------------------
*/
Route::get('/cron/indexnow-liturgia-diaria', [IndexNowController::class, 'submitDailyLiturgy']);

Route::get('/web-stories/liturgia/{date}', [WebStoryApiController::class, 'liturgia'])
    ->where('date', '\d{4}-\d{2}-\d{2}')
    ->name('api.webstories.liturgia');

Route::get('/web-stories/terco/{date}', [WebStoryApiController::class, 'terco'])
    ->where('date', '\d{4}-\d{2}-\d{2}')
    ->name('api.webstories.terco');

/*
|--------------------------------------------------------------------------
| Remote Post API (protegida por x-api-key)
|--------------------------------------------------------------------------
| Apps Script envia header: x-api-key: <SUA_CHAVE>
*/

Route::middleware(['remote.key'])->group(function () {

    // (opcional) ping para validar middleware + chave do servidor
    Route::get('/__ping', function () {
        return response()->json([
            'ok' => true,
            'has_config_key' => config('services.remote_post_api_key') !== '' ? 'YES' : 'NO',
        ]);
    })->name('api.pipeline.ping');

    // Pipeline de publicação (IA Writer -> Format -> SEO -> Publish)
    Route::post('/generate-article', [PublishingPipelineController::class, 'generateArticle'])
        ->name('api.pipeline.generate');

    Route::post('/format-article', [PublishingPipelineController::class, 'formatArticle'])
        ->name('api.pipeline.format');

    Route::post('/seo-and-publish', [PublishingPipelineController::class, 'seoAndPublish'])
        ->name('api.pipeline.publish');
});

/*
|--------------------------------------------------------------------------
| Posts API públicas (leitura)
|--------------------------------------------------------------------------
*/

Route::get('/posts', [RemotePostController::class, 'index'])
    ->name('api.posts.index');

Route::get('/posts/slug/{slug}', [RemotePostController::class, 'getBySlug'])
    ->where('slug', '[A-Za-z0-9\-]+')
    ->name('api.posts.show');

Route::get('/posts/sitemap', [RemotePostController::class, 'sitemap'])
    ->name('api.posts.sitemap');

Route::get('/posts/latest', [RemotePostController::class, 'latest'])
    ->name('api.posts.latest');