<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TioBenGeminiService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class PerguntarController extends Controller
{
    public function store(Request $request, TioBenGeminiService $service): JsonResponse
    {
        try {
            $data = $request->validate([
                'pergunta' => ['required', 'string', 'min:1', 'max:4000'],
                'history'  => ['nullable', 'array'],
                'history.*.role' => ['required_with:history', 'in:user,assistant'],
                'history.*.content' => ['required_with:history', 'string', 'max:4000'],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        }

        $pergunta = trim($data['pergunta']);

        // ✅ idioma automático
        $referer = (string) $request->headers->get('referer', '');
        $accept  = (string) $request->headers->get('accept-language', '');
        $lang    = $this->detectLang($referer, $accept);

        // ✅ só as últimas 5 mensagens
        $history = is_array($data['history'] ?? null) ? $data['history'] : [];
        $history = array_slice($history, -5);

        try {
            $resposta = $service->ask($pergunta, $history, $lang);

            return response()->json([
                'resposta' => $resposta,
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'error' => $lang === 'en'
                    ? 'Sorry, I could not get a response right now.'
                    : 'Desculpe, não consegui obter resposta agora.',
            ], 500);
        }
    }

    private function detectLang(string $referer, string $acceptLanguage): string
    {
        // 1) Referer: mais fiel ao idioma da página que chamou a API
        $ref = strtolower($referer);

        // cobre: /en, /en/, /en/qualquer-coisa e query ?lang=en
        if (preg_match('~(^|/)en(/|$)~', $ref) || str_contains($ref, 'lang=en')) {
            return 'en';
        }

        // 2) Fallback: Accept-Language
        $acc = strtolower($acceptLanguage);

        // começa com en ou tem en na lista
        if (str_starts_with($acc, 'en') || str_contains($acc, ',en')) {
            return 'en';
        }

        return 'pt';
    }
}
