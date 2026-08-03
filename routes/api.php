<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\Api\PerguntarController;
use App\Http\Controllers\WebStories\WebStoryApiController;
use App\Http\Controllers\Api\RemotePostController;
use App\Http\Controllers\Api\PublishingPipelineController;
use App\Http\Controllers\Api\IndexNowController;
use App\Http\Controllers\Api\AppMobileController;

/*
|--------------------------------------------------------------------------
| API – Geral
|--------------------------------------------------------------------------
*/

Route::post('/perguntar', [PerguntarController::class, 'store'])
    ->name('api.perguntar');

/*
|--------------------------------------------------------------------------
| App mobile publico
|--------------------------------------------------------------------------
*/

Route::prefix('app')->name('api.app.')->group(function () {
    Route::get('/home', [AppMobileController::class, 'home'])->name('home');
    Route::get('/posts', [AppMobileController::class, 'posts'])->name('posts.index');
    Route::get('/posts/{slug}', [AppMobileController::class, 'post'])
        ->where('slug', '[A-Za-z0-9\-]+')
        ->name('posts.show');
    Route::get('/liturgy/today', [AppMobileController::class, 'liturgyToday'])->name('liturgy.today');
    Route::get('/liturgy/{date}', [AppMobileController::class, 'liturgy'])
        ->where('date', '\d{2}-\d{2}-\d{4}')
        ->name('liturgy.show');
    Route::get('/rosary/today', [AppMobileController::class, 'rosaryToday'])->name('rosary.today');
    Route::get('/rosary/{set}', [AppMobileController::class, 'rosary'])
        ->where('set', '[A-Za-z0-9\-]+')
        ->name('rosary.show');
});

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
