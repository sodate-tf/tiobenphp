<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TioBenGeminiService
{
    public function ask(string $pergunta, array $history = [], string $lang = 'pt'): string
    {
        $lang = $lang === 'en' ? 'en' : 'pt';
        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            throw new \RuntimeException('GEMINI_API_KEY não configurada no .env');
        }

        $prompt = $this->buildPrompt($pergunta, $history, $lang);

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";

        $payload = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $prompt],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                // ✅ Aumenta para reduzir cortes
                'maxOutputTokens' => 2048,
            ],
        ];

        $res = Http::timeout(60)->post($url, $payload);

        if (!$res->ok()) {
            throw new \RuntimeException("Gemini HTTP {$res->status()}: " . $res->body());
        }

        $json = $res->json();

        // ✅ Concatena TODAS as parts (evita "corte" ao pegar só parts[0])
        $parts = $json['candidates'][0]['content']['parts'] ?? null;

        $text = '';
        if (is_array($parts)) {
            foreach ($parts as $p) {
                $chunk = $p['text'] ?? '';
                if (is_string($chunk) && $chunk !== '') {
                    $text .= $chunk;
                }
            }
        }

        $text = trim($text);

        if ($text === '') {
            // fallback extra (alguns retornos usam outputText, dependendo do SDK/versão)
            $fallback = $json['candidates'][0]['outputText'] ?? '';
            $fallback = is_string($fallback) ? trim($fallback) : '';
            if ($fallback !== '') return $fallback;

            throw new \RuntimeException('Resposta vazia do Gemini.');
        }

        return $text;
    }

    private function buildPrompt(string $pergunta, array $history, string $lang): string
    {
        $system = $this->buildSystemInstruction($lang);

        $history = array_slice($history ?? [], -5);

        $contextLines = [];
        foreach ($history as $msg) {
            $role = $msg['role'] ?? '';
            $content = trim((string)($msg['content'] ?? ''));

            if ($content === '' || ($role !== 'user' && $role !== 'assistant')) continue;

            $prefix = $lang === 'en'
                ? ($role === 'user' ? 'User:' : 'Uncle Ben:')
                : ($role === 'user' ? 'Pessoa:' : 'Tio Ben:');

            $contextLines[] = $prefix . ' ' . $content;
        }

        $contextBlock = implode("\n", $contextLines);

        $headerContext = '';
        if ($contextBlock !== '') {
            $headerContext = $lang === 'en'
                ? "CONVERSATION CONTEXT:\n{$contextBlock}\n"
                : "📖 CONTEXTO DA CONVERSA ATÉ AGORA:\n{$contextBlock}\n";
        }

        $headerQuestion = $lang === 'en'
            ? "CURRENT QUESTION:\n{$pergunta}"
            : "🎯 PERGUNTA ATUAL:\n{$pergunta}";

        return trim($system . "\n\n" . $headerContext . $headerQuestion);
    }

    private function buildSystemInstruction(string $lang): string
    {
        if ($lang === 'en') {
            return trim(<<<TXT
You are "Uncle Ben" (IA Tio Ben): a friendly Catholic mentor (age 20–30).
You answer ONLY from Catholic sources: Sacred Scripture, the Catechism of the Catholic Church,
official Magisterium documents, and Apostolic Tradition.

Style requirements:
- warm, pastoral, clear (no jargon)
- concise: 3–4 short paragraphs
- use a few fitting emojis (not excessive)
- end with "Next steps" suggesting 2–4 concrete study/prayer actions (bulleted)

If you are not sure based on those sources, say:
"I’m not sure yet — I’ll look into it and come back with a solid answer."

Sensitive topics (self-harm, abuse, violence, severe distress):
Respond gently, encourage seeking immediate help (local emergency services / trusted adult / priest / mental health professional).
Do NOT give clinical instructions; keep it pastoral and supportive.

Context rule:
- If the CURRENT question clearly depends on prior messages, continue naturally.
- If the CURRENT question is independent, ignore prior context.
- If the question is ambiguous, use context automatically.

Persona:
- speak in first person as Uncle Ben
- treat the user as someone you already know (friendly continuity)
- do not mention you are an AI unless asked
TXT);
        }

        return trim(<<<TXT
Você é o Tio Ben. Catequista jovem (20–30 anos). Responda única e exclusivamente
com base na fé Católica: Bíblia, Catecismo, documentos oficiais e Tradição da Igreja.

Você responde sempre de forma:
- simples
- acolhedora
- objetiva
- com 3 ou 4 parágrafos curtos
- usando alguns emojis
- e no final, sugere estudos (em lista)

Se não souber algo nessa base, diga:
"não sei ainda como responder isso e vou pesquisar".

Em temas delicados (suicídio, abuso, violência), oriente com carinho:
procure apoio de profissional de saúde, catequista, sacerdote ou pessoa de confiança.

⚠️ REGRA DE CONTEXTO:
- Se a PERGUNTA ATUAL depender claramente das perguntas anteriores, CONTINUE O ASSUNTO.
- Se a PERGUNTA ATUAL for independente, IGNORE o contexto anterior.
- Se a pergunta for ambígua, use o contexto automaticamente.

Aja como se já conhecesse a pessoa.
Fale sempre na primeira pessoa com ela.
Responda como um fluxo natural de conversa.
TXT);
    }
}
