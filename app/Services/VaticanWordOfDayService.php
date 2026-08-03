<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class VaticanWordOfDayService
{
    private const INDEX_URL = 'https://www.vaticannews.va/pt/palavra-do-dia.html';
    private const ANCHOR_TEXT = 'as palavras dos papas';

    public function forDate(Carbon $date): ?array
    {
        $cacheKey = 'mobile:vatican-word-of-day:v2:' . $date->copy()->timezone('America/Sao_Paulo')->format('Y-m-d');

        return Cache::remember(
            $cacheKey,
            now()->addHours(6),
            fn () => $this->fetchReflectionForDateWithMeta($date)['reflection']
        );
    }

    public function debugForDate(Carbon $date): array
    {
        $result = $this->fetchReflectionForDateWithMeta($date);

        return [
            'status' => $result['status'],
            'available' => $result['reflection'] !== null,
            'sourceUrl' => $result['sourceUrl'],
            'httpStatus' => $result['httpStatus'],
            'fetchedAt' => $result['fetchedAt'],
            'contentPreview' => $result['contentPreview'],
            'note' => $result['note'],
        ];
    }

    private function fetchReflectionForDateWithMeta(Carbon $date): array
    {
        $urls = $this->candidateUrlsForDate($date);
        $attemptNotes = [];

        foreach ($urls as $url) {
            $responseMeta = $this->fetchUrl($url);

            if ($responseMeta['response'] === null) {
                $attemptNotes[] = sprintf('%s: %s', $url, $responseMeta['note']);
                continue;
            }

            $response = $responseMeta['response'];
            if (!$response->successful()) {
                $attemptNotes[] = sprintf('%s: HTTP %s', $url, $response->status());
                continue;
            }

            $reflection = $this->extractReflection($response->body(), $url);
            if ($reflection) {
                return [
                    'status' => 'ok',
                    'reflection' => $reflection,
                    'sourceUrl' => $url,
                    'httpStatus' => $response->status(),
                    'fetchedAt' => now('America/Sao_Paulo')->toIso8601String(),
                    'contentPreview' => Str::limit($reflection['content'], 240),
                    'note' => 'ReflexÃ£o carregada com sucesso.',
                ];
            }

            $attemptNotes[] = sprintf('%s: HTML recebido, mas a seÃ§Ã£o nÃ£o foi encontrada.', $url);
        }

        return [
            'status' => 'not_found',
            'reflection' => null,
            'sourceUrl' => $urls[0],
            'httpStatus' => null,
            'fetchedAt' => now('America/Sao_Paulo')->toIso8601String(),
            'contentPreview' => null,
            'note' => implode(' | ', $attemptNotes),
        ];
    }

    /**
     * @return array{response: \Illuminate\Http\Client\Response|null, note: string|null}
     */
    private function fetchUrl(string $url): array
    {
        try {
            $response = Http::timeout(20)
                ->withHeaders([
                    'Accept-Language' => 'pt-BR,pt;q=0.9,en;q=0.8',
                    'User-Agent' => 'IA Tio Ben Mobile/1.0 (+https://www.iatioben.com.br)',
                ])
                ->get($url);

            return [
                'response' => $response,
                'note' => null,
            ];
        } catch (\Throwable $exception) {
            return [
                'response' => null,
                'note' => $exception->getMessage(),
            ];
        }
    }

    /**
     * @return list<string>
     */
    private function candidateUrlsForDate(Carbon $date): array
    {
        $path = sprintf('%04d/%02d/%02d.html', (int) $date->format('Y'), (int) $date->format('m'), (int) $date->format('d'));

        return [
            'https://www.vaticannews.va/pt/palavra-do-dia/' . $path,
            'https://www.vaticannews.va/content/vaticannews/pt/palavra-do-dia/' . $path,
            self::INDEX_URL,
        ];
    }

    private function extractReflection(string $html, string $sourceUrl): ?array
    {
        $blocks = $this->extractBlocksFromSectionHtml($html);

        if (count($blocks) === 0) {
            $blocks = $this->extractBlocksFromDom($html);
        }

        if (count($blocks) === 0) {
            $blocks = $this->extractBlocksFromPlainText($html);
        }

        $blocks = array_values(array_filter(array_map(
            fn (string $block) => $this->normalizeText($block),
            $blocks
        )));

        if (count($blocks) === 0) {
            return null;
        }

        return [
            'title' => 'ReflexÃ£o da Palavra',
            'label' => 'As palavras dos Papas',
            'content' => implode("\n\n", array_slice($blocks, 0, 3)),
            'sourceLabel' => 'Vatican News - Palavra do Dia',
            'sourceUrl' => $sourceUrl,
        ];
    }

    private function extractBlocksFromSectionHtml(string $html): array
    {
        if (!preg_match('/<section[^>]*>.*?<h2>\s*As palavras dos Papas\s*<\/h2>.*?<div class="section__content">(.*?)<\/div>/si', $html, $matches)) {
            return [];
        }

        preg_match_all('/<p>(.*?)<\/p>/si', $matches[1], $paragraphs);

        return array_values(array_filter(array_map(function (string $paragraph): string {
            $text = html_entity_decode(strip_tags($paragraph), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            return trim(preg_replace('/\s+/u', ' ', $text) ?? $text);
        }, $paragraphs[1] ?? [])));
    }

    private function extractBlocksFromDom(string $html): array
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $internalErrors = libxml_use_internal_errors(true);

        $loaded = $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
        libxml_clear_errors();
        libxml_use_internal_errors($internalErrors);

        if (!$loaded) {
            return [];
        }

        $xpath = new \DOMXPath($dom);
        $sections = $xpath->query("//section[.//h2[contains(translate(normalize-space(string(.)), 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz'), '" . self::ANCHOR_TEXT . "')]]");

        if (!$sections || $sections->length === 0) {
            return [];
        }

        $paragraphs = [];
        foreach ($xpath->query('.//p', $sections->item(0)) ?: [] as $paragraph) {
            $paragraphs[] = trim((string) $paragraph->textContent);
        }

        return $paragraphs;
    }

    private function extractBlocksFromPlainText(string $html): array
    {
        $text = Str::of(strip_tags($html))
            ->replace("\r", '')
            ->replace("\t", ' ')
            ->replace('&nbsp;', ' ')
            ->toString();

        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        $anchor = mb_stripos($text, 'As palavras dos Papas');
        if ($anchor === false) {
            return [];
        }

        $slice = trim(mb_substr($text, $anchor));
        if ($slice === '') {
            return [];
        }

        $slice = preg_replace('/^As palavras dos Papas\s*/u', '', $slice) ?? $slice;
        $parts = preg_split('/\s{2,}/u', $slice) ?: [];

        return array_values(array_filter(array_map('trim', $parts)));
    }

    private function normalizeText(string $text): string
    {
        $text = trim(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;
        $text = preg_replace('/^(As palavras dos Papas\s*)/iu', '', $text) ?? $text;

        return trim($text);
    }
}
