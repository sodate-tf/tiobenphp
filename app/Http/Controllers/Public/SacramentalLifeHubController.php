<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\PublicBlogService;

class SacramentalLifeHubController extends Controller
{
    public function __construct(private readonly PublicBlogService $blog)
    {
    }

    public function index()
    {
        return view('sacramental.hub', $this->blog->sacramentalLifeHubData());
    }
}

