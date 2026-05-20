<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class OpsBackfillController extends Controller
{
    public function index()
    {
        return view('admin.ops.backfill-runner');
    }

    public function migrate(Request $request): RedirectResponse
    {
        @set_time_limit(300);
        Artisan::call('migrate', ['--force' => true]);

        return back()->with('success', 'Migration executada.')->with('command_output', Artisan::output());
    }

    public function backfillEnglish(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'limit' => ['nullable', 'integer', 'min:1', 'max:500'],
            'write' => ['nullable', 'boolean'],
        ]);

        $limit = (int) ($data['limit'] ?? 20);
        $write = (bool) ($data['write'] ?? false);

        $params = ['--limit' => $limit];
        if ($write) {
            $params['--write'] = true;
        }

        @set_time_limit(600);
        Artisan::call('posts:backfill-english', $params);

        $msg = $write
            ? "Backfill EN executado em modo WRITE (limit={$limit})."
            : "Backfill EN executado em modo DRY-RUN (limit={$limit}).";

        return back()->with('success', $msg)->with('command_output', Artisan::output());
    }

    public function backfillPairs(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'limit' => ['nullable', 'integer', 'min:1', 'max:5000'],
            'write' => ['nullable', 'boolean'],
        ]);

        $limit = (int) ($data['limit'] ?? 200);
        $write = (bool) ($data['write'] ?? false);

        $params = ['--limit' => $limit];
        if ($write) {
            $params['--write'] = true;
        }

        @set_time_limit(600);
        Artisan::call('posts:backfill-language-pairs', $params);

        $msg = $write
            ? "Pareamento PT/EN executado em modo WRITE (limit={$limit})."
            : "Pareamento PT/EN executado em modo DRY-RUN (limit={$limit}).";

        return back()->with('success', $msg)->with('command_output', Artisan::output());
    }
}

