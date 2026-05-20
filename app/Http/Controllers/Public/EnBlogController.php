<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\PublicBlogService;
use Illuminate\Http\Request;

class EnBlogController extends Controller
{
    public function __construct(private readonly PublicBlogService $blog)
    {
    }

    /**
     * English blog portal: /en/blog
     */
    public function portal()
    {
        $data = $this->blog->portalData('en');

        // Compatibilidade com a view en.blog.portal atual, que usa $posts.
        $data['posts'] = $this->blog->paginatedPosts('en', null, 12);

        return view('en.blog.portal', $data);
    }

    /**
     * Searchable list: /en/blog/posts?q=...
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $posts = $this->blog->paginatedPosts('en', $q, 12);

        $meta = [
            'html_lang' => 'en',
            'title' => $q !== ''
                ? 'Search for “' . $q . '” on IA Tio Ben Blog'
                : 'All Catholic articles — IA Tio Ben Blog',
            'description' => $q !== ''
                ? 'IA Tio Ben Blog results for “' . $q . '”: daily Mass readings, saints, prayer and Christian life.'
                : 'All IA Tio Ben Blog articles about daily Mass readings, Gospel, saints, prayer, rosary and Christian life.',
            'canonical' => url('/en/blog/posts') . ($q !== '' ? '?q=' . urlencode($q) : ''),
            'robots' => $q !== '' ? 'noindex,follow' : 'index,follow,max-image-preview:large',
            'og_title' => 'All articles — IA Tio Ben Blog',
            'og_description' => 'Daily Mass readings, Gospel, saints, prayer and Christian life.',
            'og_url' => url('/en/blog/posts'),
        ];

        return view('en.blog.index', compact('posts', 'q', 'meta'));
    }

    /**
     * Category EN: /en/blog/category/{categorySlug}
     */
    public function category(string $categorySlug)
    {
        return view('en.blog.category', $this->blog->categoryData($categorySlug, 'en', 12));
    }

    /**
     * Post EN: /en/blog/{slug}
     */
    public function show(string $slug)
    {
        return view('en.blog.show', $this->blog->showData($slug, 'en'));
    }
}
