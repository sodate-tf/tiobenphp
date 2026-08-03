<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class OpsBackfillController extends Controller
{
    /**
     * Comandos permitidos para execucao via browser.
     * Mantemos whitelist para evitar execucoes perigosas.
     */
    private const ALLOWED_ARTISAN_COMMANDS = [
        'migrate',
        'migrate:status',
        'migrate:rollback',
        'migrate:fresh',
        'optimize:clear',
        'cache:clear',
        'config:clear',
        'route:clear',
        'view:clear',
        'queue:restart',
    ];

    public function index()
    {
        return view('admin.ops.backfill-runner');
    }

    public function artisanRunner()
    {
        return view('admin.ops.artisan-runner', [
            'allowedCommands' => self::ALLOWED_ARTISAN_COMMANDS,
        ]);
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

    public function runArtisanCommand(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'command' => ['required', 'string', 'max:255'],
        ]);

        $raw = trim((string) $data['command']);
        if ($raw === '') {
            return back()->with('error', 'Informe um comando Artisan.');
        }

        $parts = preg_split('/\s+/', $raw) ?: [];
        $signature = (string) array_shift($parts);

        if (!in_array($signature, self::ALLOWED_ARTISAN_COMMANDS, true)) {
            return back()->with('error', "Comando nao permitido: {$signature}");
        }

        $params = [];
        foreach ($parts as $part) {
            if (!str_starts_with($part, '--')) {
                return back()->with('error', "Parametro invalido: {$part}. Use apenas flags --opcao ou --opcao=valor.");
            }

            $flag = substr($part, 2);
            if ($flag === '') {
                return back()->with('error', 'Flag vazia detectada no comando.');
            }

            if (str_contains($flag, '=')) {
                [$key, $value] = explode('=', $flag, 2);
                if ($key === '') {
                    return back()->with('error', "Flag invalida: {$part}");
                }
                $params["--{$key}"] = $value;
            } else {
                $params["--{$flag}"] = true;
            }
        }

        // forca --force em comandos de migrate para ambiente sem interacao
        if ($signature === 'migrate' || $signature === 'migrate:rollback' || $signature === 'migrate:fresh') {
            $params['--force'] = true;
        }

        @set_time_limit(600);
        Artisan::call($signature, $params);

        $rendered = $signature;
        foreach ($params as $k => $v) {
            $rendered .= $v === true ? " {$k}" : " {$k}={$v}";
        }

        return back()
            ->with('success', "Comando executado: {$rendered}")
            ->with('command_output', Artisan::output())
            ->with('last_command', $raw);
    }
}
