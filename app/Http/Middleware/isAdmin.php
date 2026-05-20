<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Ajuste esta regra conforme seu banco:
        // - se você tiver coluna is_admin (tinyint)
        // - ou role (string)
        // - ou email allowlist
        if (!$user || !((bool) ($user->is_admin ?? false))) {
            abort(403);
        }

        return $next($request);
    }
}
