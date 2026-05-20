<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\PublicBlogService;

class FinanceHubController extends Controller
{
    public function __construct(private readonly PublicBlogService $blog)
    {
    }

    public function index()
    {
        return view('finance.hub', $this->blog->financeHubData());
    }
}
