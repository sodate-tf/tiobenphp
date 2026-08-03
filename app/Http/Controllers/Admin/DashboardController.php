<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MobileBetaTester;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('admin.dashboard', [
            'mobileBetaTesterCount' => MobileBetaTester::count(),
            'mobileBetaPendingCount' => MobileBetaTester::where('link_sent', false)->count(),
        ]);
    }
}
