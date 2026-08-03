<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TioBenGeminiService
{
    public function ask(string $pergunta, array $history = [], string $lang = 'pt'): string
    {
        $lang = $lang === 'en' ? 'en' : 'pt';
        $apiKey = trim((string) env('OPENAI_API_KEY', ''));

        if ($apiKey === '') {
            throw new \RuntimeException('OPENAI_API_KEY nao configurada no .env.');
        }

        $prompt = $this->buildPrompt($pergunta, $history, $lang);
        $model = trim((string) env('OPENAI_MODEL_CHAT', env('OPENAI_MODEL', 'gpt-4.1-mini')));
        $timeout = (int) env('OPENAI_TIMEOUT', 120);
        $baseUri = rtrim((string) config('services.openai.base_uri', 'https://api.openai.com'), '/');

        $res = Http::timeout($timeout)
            ->withToken($apiKey)
            ->acceptJson()
            ->asJson()
            ->post($baseUri . '/v1/responses', [
                'model' => $model,
                'input' => [
                    [
                        'role' => 'user',
                        'content' => [
                            ['type' => 'input_text', 'text' => $prompt],
                        ],
                    ],
                ],
                'text' => [
                    'format' => ['type' => 'text'],
                ],
                'max_output_tokens' => 2048,
            ]);

        if (!$res->ok()) {
            throw new \RuntimeException("OpenAI HTTP {$res->status()}: " . $res->body());
        }

        $text = $this->extractOpenAiText($res->json() ?: []);

        if ($text === '') {
            throw new \RuntimeException('Resposta vazia da OpenAI.');
        }

        return $text;
    }

    private function buildPrompt(string $pergunta, array $history, string $lang): string
    {
        $system = $this->buildSystemInstruction($lang);
        $history = array_slice($history, -5);

        $contextLines = [];
        foreach ($history as $msg) {
            $role = $msg['role'] ?? '';
            $content = trim((string) ($msg['content'] ?? ''));

            if ($content === '' || ($role !== 'user' && $role !== 'assistant')) {
                continue;
            }

            $prefix = $lang === 'en'
                ? ($role === 'user' ? 'User:' : 'Uncle Ben:')
                : ($role === 'user' ? 'Pessoa:' : 'Tio Ben:');

            $contextLines[] = $prefix . ' ' . $content;
        }

        $contextBlock = implode("\n", $contextLines);
        $headerContext = $contextBlock !== ''
            ? ($lang === 'en'
                ? "CONVERSATION CONTEXT:\n{$contextBlock}\n"
                : "CONTEXTO DA CONVERSA ATE AGORA:\n{$contextBlock}\n")
            : '';

        $headerQuestion = $lang === 'en'
            ? "CURRENT QUESTION:\n{$pergunta}"
            : "PERGUNTA ATUAL:\n{$pergunta}";

        return trim($system . "\n\n" . $headerContext . $headerQuestion);
    }

    private function buildSystemInstruction(string $lang): string
    {
        if ($lang === 'en') {
            return trim(<<<TXT
You are "Uncle Ben" (IA Tio Ben): a friendly Catholic mentor.
Answer only from Catholic sources: Sacred Scripture, the Catechism of the Catholic Church, official Magisterium documents, and Apostolic Tradition.

Style:
- warm, pastoral, clear, and concise
- 3 to 4 short paragraphs
- use a few fitting emojis, without excess
- end with "Next steps" and 2 to 4 concrete study or prayer actions

If you are not sure based on those sources, say:
"I'm not sure yet. I'll look into it and come back with a solid answer."

Sensitive topics such as self-harm, abuse, violence, or severe distress:
respond gently and encourage immediate help from local emergency services, a trusted adult, a priest, or a mental health professional. Do not give clinical instructions.

Context rule:
- If the current question clearly depends on prior messages, continue naturally.
- If the current question is independent, ignore prior context.
- If it is ambiguous, use the context.

Speak in first person as Uncle Ben. Do not say you are an AI unless asked.
TXT);
        }

        return trim(<<<TXT
Voce e o Tio Ben, um mentor catolico jovem, acolhedor e fiel a Igreja.
Responda unica e exclusivamente com base na fe catolica: Biblia, Catecismo da Igreja Catolica, documentos oficiais do Magisterio e Tradicao da Igreja.

Estilo:
- simples, acolhedor, objetivo e pastoral
- 3 ou 4 paragrafos curtos
- use alguns emojis, sem exagero
- no final, sugira estudos ou passos praticos em lista

Se nao souber responder com seguranca nessa base, diga:
"Nao sei ainda como responder isso e vou pesquisar."

Em temas delicados como suicidio, abuso, violencia ou sofrimento intenso:
oriente com carinho a procurar ajuda imediata de servicos de emergencia, profissional de saude, sacerdote, catequista ou pessoa de confianca. Nao de instrucoes clinicas.

Regra de contexto:
- Se a pergunta atual depender claramente das mensagens anteriores, continue o assunto.
- Se a pergunta atual for independente, ignore o contexto anterior.
- Se a pergunta for ambigua, use o contexto automaticamente.

Aja como se ja conhecesse a pessoa. Fale sempre na primeira pessoa como Tio Ben.
TXT);
    }

    private function extractOpenAiText(array $json): string
    {
        $text = '';

        if (isset($json['output_text']) && is_string($json['output_text'])) {
            $text .= $json['output_text'];
        }

        foreach (($json['output'] ?? []) as $item) {
            if (($item['type'] ?? '') !== 'message') {
                continue;
            }

            foreach (($item['content'] ?? []) as $chunk) {
                if (($chunk['type'] ?? '') === 'output_text') {
                    $text .= (string) ($chunk['text'] ?? '');
                }
            }
        }

        return trim($text);
    }
}
