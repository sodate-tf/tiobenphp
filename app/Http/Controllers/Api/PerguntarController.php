<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TioBenGeminiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PerguntarController extends Controller
{
    public function store(Request $request, TioBenGeminiService $service): JsonResponse
    {
        try {
            $data = $request->validate([
                'pergunta' => ['required', 'string', 'min:1', 'max:4000'],
                'lang' => ['nullable', 'in:pt,en'],
                'history' => ['nullable', 'array'],
                'history.*.role' => ['required_with:history', 'in:user,assistant'],
                'history.*.content' => ['required_with:history', 'string', 'max:4000'],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        }

        $pergunta = trim((string) $data['pergunta']);
        $referer = (string) $request->headers->get('referer', '');
        $accept = (string) $request->headers->get('accept-language', '');
        $lang = (string) ($data['lang'] ?? $this->detectLang($referer, $accept));

        $history = is_array($data['history'] ?? null) ? $data['history'] : [];
        $history = array_slice($history, -5);

        try {
            return response()->json([
                'resposta' => $service->ask($pergunta, $history, $lang),
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'error' => $lang === 'en'
                    ? 'Sorry, I could not get a response right now.'
                    : 'Desculpe, nao consegui obter resposta agora.',
            ], 500);
        }
    }

    private function detectLang(string $referer, string $acceptLanguage): string
    {
        $ref = strtolower($referer);

        if (preg_match('~(^|/)en(/|$)~', $ref) || str_contains($ref, 'lang=en')) {
            return 'en';
        }

        $acc = strtolower($acceptLanguage);
        if (str_starts_with($acc, 'en') || str_contains($acc, ',en')) {
            return 'en';
        }

        return 'pt';
    }
}
