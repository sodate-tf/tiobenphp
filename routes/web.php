<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;

use App\Http\Controllers\LiturgiaController;
use App\Http\Controllers\DailyMassReadingsController;
use App\Http\Controllers\Public\LiturgyYearController;
use App\Http\Controllers\Public\LiturgyMonthController;
use App\Http\Controllers\Public\RosaryController;


// BLOG
use App\Http\Controllers\Public\BlogController;
use App\Http\Controllers\Public\EnBlogController;

// ADMIN
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PostCrossLinkController;
use App\Http\Controllers\Admin\PostRelatedController;
use App\Http\Controllers\Admin\PostCoverController;
use App\Http\Controllers\Admin\OpsBackfillController;
use App\Http\Controllers\Public\FinanceHubController;
use App\Http\Controllers\Public\PrayerHubController;
use App\Http\Controllers\Public\SacramentalLifeHubController;
use App\Http\Controllers\Public\FaithQuestionsHubController;
use App\Http\Controllers\Public\EnHubController;

use App\Http\Controllers\SitemapController;

use App\Http\Controllers\WebStories\WebStoryController;

/*
|--------------------------------------------------------------------------
| Breeze: garantir rota nomeada "dashboard"
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->name('dashboard');


/*
|--------------------------------------------------------------------------
| ADMIN (protegido)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->middleware(['web', 'auth', 'is_admin'])
    ->name('admin.')
    ->group(function () {

        Route::view('/', 'admin.dashboard')->name('dashboard');

        // CRUD
        Route::resource('posts', PostController::class)->except(['show']);
        Route::resource('categories', CategoryController::class)->except(['show']);

        // ✅ Gerar cover (botão no admin)
        Route::post('posts/{post}/generate-cover', [PostCoverController::class, 'generate'])
            ->name('posts.generateCover');

        // Liturgia ↔ Posts (post_cross_links)
        Route::resource('liturgy-links', PostCrossLinkController::class)
            ->names('liturgy-links')
            ->parameters(['liturgy-links' => 'liturgy_link'])
            ->except(['show']);

        // Posts Relacionados (post_related_items)
        Route::prefix('related')->name('related.')->group(function () {
            Route::get('/', [PostRelatedController::class, 'index'])->name('index');

            Route::get('/{postId}', [PostRelatedController::class, 'edit'])->name('edit');
            Route::post('/{postId}', [PostRelatedController::class, 'store'])->name('store');

            Route::delete('/{postId}/{id}', [PostRelatedController::class, 'destroy'])->name('destroy');
            Route::post('/{postId}/reorder', [PostRelatedController::class, 'reorder'])->name('reorder');
        });

        // Operações sem SSH (migrate/backfills)
        Route::get('ops/backfills', [OpsBackfillController::class, 'index'])->name('ops.backfills.index');
        Route::post('ops/backfills/migrate', [OpsBackfillController::class, 'migrate'])->name('ops.backfills.migrate');
        Route::post('ops/backfills/english', [OpsBackfillController::class, 'backfillEnglish'])->name('ops.backfills.english');
        Route::post('ops/backfills/pairs', [OpsBackfillController::class, 'backfillPairs'])->name('ops.backfills.pairs');
    });


/*
|--------------------------------------------------------------------------
| Web Routes (Público)
|--------------------------------------------------------------------------
*/

// =====================
// CRISTÃO CATÓLICO E FINANÇAS (HUB)
// =====================
Route::middleware('locale:pt_BR')->group(function () {
    Route::get('/cristao-catolico-e-financas', [FinanceHubController::class, 'index'])
        ->name('finance.hub');
    Route::get('/oracao-catolica-pratica', [PrayerHubController::class, 'index'])
        ->name('prayer.hub');
    Route::get('/vida-sacramental-pratica', [SacramentalLifeHubController::class, 'index'])
        ->name('sacramental.hub');
    Route::get('/duvidas-da-fe-catolica', [FaithQuestionsHubController::class, 'index'])
        ->name('faith.hub');
});

// =====================
// HOME / LOCALE
// =====================
Route::middleware('locale:pt_BR')->group(function () {
    Route::get('/', fn () => view('pt.home'))->name('pt.home');
});

Route::prefix('en')->middleware('locale:en')->group(function () {
    Route::get('/', fn () => view('en.home'))->name('en.home');
});

// =====================
// TERMS
// =====================
Route::get('/termo-de-responsabilidade', fn () => view('pt.termo-responsabilidade'))
    ->name('pt.terms');

Route::get('/en/terms-of-responsibility', fn () => view('en.terms-of-responsibility'))
    ->name('en.terms');

// =====================
// BLOG PT (sem prefix)
// =====================
Route::middleware('locale:pt_BR')->prefix('blog')->group(function () {

    Route::get('/', [BlogController::class, 'portal'])
        ->name('blog.pt.portal');

    Route::get('/posts', [BlogController::class, 'index'])
        ->name('blog.pt.posts');

    Route::get('/categoria/{categorySlug}', [BlogController::class, 'category'])
        ->where('categorySlug', '[A-Za-z0-9\-]+')
        ->name('blog.pt.category');

    Route::get('/{slug}', [BlogController::class, 'show'])
        ->where('slug', '[A-Za-z0-9\-]+')
        ->name('blog.pt.show');
});

// =====================
// LITURGIA DIÁRIA (PT)
// =====================
Route::get('/liturgia-diaria', [LiturgiaController::class, 'home'])
    ->name('liturgia.home');

Route::get('/liturgia-diaria/{data}', [LiturgiaController::class, 'day'])
    ->where('data', '\d{2}-\d{2}-\d{4}')
    ->name('liturgia.day');

Route::prefix('liturgia-diaria/ano')->group(function () {
    Route::get('{ano}', [LiturgyYearController::class, 'ptYear'])
        ->where('ano', '\d{4}')
        ->name('liturgia.year');

    Route::get('{ano}/{mes}', [LiturgyMonthController::class, 'ptMonth'])
        ->where(['ano' => '\d{4}', 'mes' => '\d{2}'])
        ->name('liturgia.month');
});

// =====================
// ROSARY PT
// =====================
Route::get('/santo-terco', [RosaryController::class, 'hubPt'])
    ->name('rosary.pt.hub');

Route::get('/santo-terco/como-rezar-o-terco', fn () => view('terco.como-rezar-o-terco'))
    ->name('terco.como-rezar-o-terco');

Route::get('/santo-terco/misterios-gozosos', [RosaryController::class, 'contentPt'])
    ->defaults('set', 'gozosos')
    ->name('rosary.pt.content.gozosos');

Route::get('/santo-terco/misterios-dolorosos', [RosaryController::class, 'contentPt'])
    ->defaults('set', 'dolorosos')
    ->name('rosary.pt.content.dolorosos');

Route::get('/santo-terco/misterios-gloriosos', [RosaryController::class, 'contentPt'])
    ->defaults('set', 'gloriosos')
    ->name('rosary.pt.content.gloriosos');

Route::get('/santo-terco/misterios-luminosos', [RosaryController::class, 'contentPt'])
    ->defaults('set', 'luminosos')
    ->name('rosary.pt.content.luminosos');

// =====================
// EN (prefix /en)
// =====================
Route::prefix('en')->middleware('locale:en')->group(function () {
    Route::get('/practical-catholic-prayer', [EnHubController::class, 'prayer'])
        ->name('en.hub.prayer');
    Route::get('/practical-sacramental-life', [EnHubController::class, 'sacramental'])
        ->name('en.hub.sacramental');
    Route::get('/catholic-faith-questions', [EnHubController::class, 'faith'])
        ->name('en.hub.faith');

    // BLOG EN
    Route::prefix('blog')->group(function () {

        Route::get('/', [EnBlogController::class, 'portal'])
            ->name('blog.en.portal');

        Route::get('/posts', [EnBlogController::class, 'index'])
            ->name('blog.en.posts');

        Route::get('/category/{categorySlug}', [EnBlogController::class, 'category'])
            ->where('categorySlug', '[A-Za-z0-9\-]+')
            ->name('blog.en.category');

        Route::get('/{slug}', [EnBlogController::class, 'show'])
            ->where('slug', '[A-Za-z0-9\-]+')
            ->name('blog.en.show');
    });

    // ROSARY EN
    Route::get('/rosary', [RosaryController::class, 'hubEn'])
        ->name('rosary.en.hub');

    Route::get('/rosary/how-to-pray-the-rosary', fn () => view('en.rosary.how-to-pray-the-rosary'))
        ->name('en.rosary.how-to-pray-the-rosary');

    Route::get('/rosary/joyful-mysteries', [RosaryController::class, 'contentEn'])
        ->defaults('set', 'gozosos')
        ->name('rosary.en.content.joyful');

    Route::get('/rosary/sorrowful-mysteries', [RosaryController::class, 'contentEn'])
        ->defaults('set', 'dolorosos')
        ->name('rosary.en.content.sorrowful');

    Route::get('/rosary/glorious-mysteries', [RosaryController::class, 'contentEn'])
        ->defaults('set', 'gloriosos')
        ->name('rosary.en.content.glorious');

    Route::get('/rosary/luminous-mysteries', [RosaryController::class, 'contentEn'])
        ->defaults('set', 'luminosos')
        ->name('rosary.en.content.luminous');

    // DAILY MASS READINGS EN
    // DAILY MASS READINGS EN
    Route::prefix('daily-mass-readings')->group(function () {
        Route::get('/', [DailyMassReadingsController::class, 'home'])
            ->name('en.liturgy.home');

        Route::get('year/{year}', [LiturgyYearController::class, 'enYear'])
            ->where('year', '\d{4}')
            ->name('en.liturgy.year');

        Route::get('year/{year}/{month}', [LiturgyMonthController::class, 'enMonth'])
            ->where(['year' => '\d{4}', 'month' => '\d{2}'])
            ->name('en.liturgy.month');

        // Corrige variações com barras:
        // /en/daily-mass-readings/2026/05/07
        // /en/daily-mass-readings/07/05/2026
        // /en/daily-mass-readings/12/31/2026
        Route::get('{a}/{b}/{c}', [DailyMassReadingsController::class, 'redirectDatePartsEn'])
            ->where([
                'a' => '\d{1,4}',
                'b' => '\d{1,2}',
                'c' => '\d{1,4}',
            ])
            ->name('en.liturgy.day.parts.redirect');

        // Aceita variações com hífen para normalizar via controller:
        // 07-05-2026
        // 7-5-2026
        // 2026-05-07
        // 12-31-2026
        Route::get('{data}', [DailyMassReadingsController::class, 'dayEn'])
            ->where('data', '\d{1,4}-\d{1,2}-\d{1,4}')
            ->name('en.liturgy.day');
    });
});


/*
|--------------------------------------------------------------------------
| PROFILE (Breeze)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


/*
|--------------------------------------------------------------------------
| WEB STORIES
|--------------------------------------------------------------------------
*/
Route::get('/web-stories/{slug}/', [WebStoryController::class, 'show'])
  ->where('slug', '(liturgia|terco)-\d{2}-\d{2}-\d{4}')
  ->name('webstories.show');


/*
|--------------------------------------------------------------------------
| SITEMAPS
|--------------------------------------------------------------------------
*/
Route::get('/sitemap.xml', [SitemapController::class, 'index'])
    ->name('sitemap.index');

Route::get('/sitemap-principal.xml', [SitemapController::class, 'main'])
    ->name('sitemap.main');

Route::get('/sitemap-today.xml', [SitemapController::class, 'todaySitemap'])
    ->name('sitemap.today');

Route::get('/sitemap-liturgia-recent.xml', [SitemapController::class, 'liturgyRecent'])
    ->name('sitemap.liturgy.recent');

Route::get('/sitemap-liturgia-archive.xml', [SitemapController::class, 'liturgyArchive'])
    ->name('sitemap.liturgy.archive');

Route::get('/sitemap-en.xml', [SitemapController::class, 'en'])
    ->name('sitemap.en');

Route::get('/sitemap-blog.xml', [SitemapController::class, 'blog'])
    ->name('sitemap.blog');

Route::get('/sitemap-webstories.xml', [SitemapController::class, 'webstories'])
    ->name('sitemap.webstories');

Route::get('/sitemap-terco-webstories.xml', [SitemapController::class, 'tercoWebstories'])
    ->name('sitemap.terco.webstories');

Route::get('/sitemap-recent.xml', [SitemapController::class, 'recent'])
    ->name('sitemap.recent');

// Compatibilidade com o nome antigo que você usava no Next/Vercel
Route::get('/sitemap-terco-webstorie.xml', [SitemapController::class, 'tercoWebstories'])
    ->name('sitemap.terco.webstorie.legacy');

/*
|--------------------------------------------------------------------------
| AUTH ROUTES (Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
