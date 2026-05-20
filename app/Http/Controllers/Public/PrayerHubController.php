<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\PublicBlogService;

class PrayerHubController extends Controller
{
    public function __construct(private readonly PublicBlogService $blog)
    {
    }

    public function index()
    {
        return view('prayer.hub', $this->blog->prayerHubData());
    }
}

