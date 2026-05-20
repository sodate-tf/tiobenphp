<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\PublicBlogService;

class FaithQuestionsHubController extends Controller
{
    public function __construct(private readonly PublicBlogService $blog)
    {
    }

    public function index()
    {
        return view('faith.hub', $this->blog->catholicFaithQuestionsHubData());
    }
}

