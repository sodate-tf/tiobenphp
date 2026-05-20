<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\PublicBlogService;

class EnHubController extends Controller
{
    public function __construct(private readonly PublicBlogService $blog)
    {
    }

    public function prayer()
    {
        return view('en.hubs.prayer', $this->blog->prayerHubDataEn());
    }

    public function sacramental()
    {
        return view('en.hubs.sacramental', $this->blog->sacramentalLifeHubDataEn());
    }

    public function faith()
    {
        return view('en.hubs.faith', $this->blog->catholicFaithQuestionsHubDataEn());
    }
}

