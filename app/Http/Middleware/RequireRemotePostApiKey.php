<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RequireRemotePostApiKey
{
    public function handle(Request $request, Closure $next)
    {
        // Cliente (Apps Script)
        $clientKey = (string) $request->header('x-api-key');

        // Servidor (preferência: config -> env -> banco)
        $serverKey = (string) config('services.remote_post_api_key', '');

        if ($serverKey === '') {
            $serverKey = (string) env('REMOTE_POST_API_KEY', '');
        }

        // fallback opcional (se você usa ai_settings)
        if ($serverKey === '') {
            try {
                $serverKey = (string) DB::table('ai_settings')->value('remote_post_api_key');
            } catch (\Throwable $e) {
                // ignora
            }
        }

        if ($serverKey === '') {
            return response()->json([
                'success' => false,
                'message' => 'Servidor mal configurado. Falta REMOTE_POST_API_KEY.',
            ], 500);
        }

        if ($clientKey === '' || !hash_equals($serverKey, $clientKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso não autorizado. Chave de API inválida.',
            ], 401);
        }

        return $next($request);
    }
}