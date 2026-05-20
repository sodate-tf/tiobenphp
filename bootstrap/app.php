<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->alias([
            'locale'    => \App\Http\Middleware\SetLocale::class,
            'is_admin'  => \App\Http\Middleware\IsAdmin::class,
            'remote.key' => \App\Http\Middleware\RequireRemotePostApiKey::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // você pode customizar exceptions aqui se quiser
    })
    ->create();
