<?php

namespace App\Services\Liturgia;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class NetBibleEnService
{
    private const NET_BIBLE_ENDPOINT = 'https://labs.bible.org/api/';

    /**
     * Tailwind pill (igual ao TS).
     */
    private const VERSE_PILL_CLASS =
        'mx-0.5 align-baseline rounded bg-amber-50 px-1 text-[0.75em] font-semibold text-amber-900/90 border border-amber-100';

    /**
     * Mapa de livros PT(abrev) -> EN (NET Bible).
     * Mantive as chaves como você usa no dataset (inclui "Jó").
     */
    private const BOOK_MAP = [
        'Gn' => 'Genesis',
        'Ex' => 'Exodus',
        'Lv' => 'Leviticus',
        'Nm' => 'Numbers',
        'Dt' => 'Deuteronomy',
        'Js' => 'Joshua',
        'Jz' => 'Judges',
        'Rt' => 'Ruth',
        '1Sm' => '1 Samuel',
        '2Sm' => '2 Samuel',
        '1Rs' => '1 Kings',
        '2Rs' => '2 Kings',
        '1Cr' => '1 Chronicles',
        '2Cr' => '2 Chronicles',
        'Esd' => 'Ezra',
        'Ne' => 'Nehemiah',
        'Tb' => 'Tobit',
        'Jt' => 'Judith',
        'Est' => 'Esther',

        'Jó' => 'Job',
        'Sl' => 'Psalm',
        'Pr' => 'Proverbs',
        'Ecl' => 'Ecclesiastes',
        'Ct' => 'Song of Solomon',
        'Is' => 'Isaiah',
        'Jr' => 'Jeremiah',
        'Lm' => 'Lamentations',
        'Ez' => 'Ezekiel',
        'Dn' => 'Daniel',
        'Os' => 'Hosea',
        'Jl' => 'Joel',
        'Am' => 'Amos',
        'Abd' => 'Obadiah',
        'Jn' => 'Jonah',
        'Mq' => 'Micah',
        'Na' => 'Nahum',
        'Hc' => 'Habakkuk',
        'Sf' => 'Zephaniah',
        'Ag' => 'Haggai',
        'Zc' => 'Zechariah',
        'Ml' => 'Malachi',

        'Mt' => 'Matthew',
        'Mc' => 'Mark',
        'Lc' => 'Luke',
        'Jo' => 'John',
        'At' => 'Acts',
        'Rm' => 'Romans',
        '1Cor' => '1 Corinthians',
        '2Cor' => '2 Corinthians',
        'Gl' => 'Galatians',
        'Ef' => 'Ephesians',
        'Fl' => 'Philippians',
        'Cl' => 'Colossians',
        '1Ts' => '1 Thessalonians',
        '2Ts' => '2 Thessalonians',
        '1Tm' => '1 Timothy',
        '2Tm' => '2 Timothy',
        'Tt' => 'Titus',
        'Fm' => 'Philemon',
        'Hb' => 'Hebrews',
        'Tg' => 'James',
        '1Pd' => '1 Peter',
        '2Pd' => '2 Peter',
        '1Jo' => '1 John',
        '2Jo' => '2 John',
        '3Jo' => '3 John',
        'Jd' => 'Jude',
        'Ap' => 'Revelation',
    ];

    private static function safeStr($v): string
    {
        return trim((string)($v ?? ''));
    }

    private static function normalizeDashes(string $s): string
    {
        // – — -> -
        return str_replace(["\u{2013}", "\u{2014}"], '-', $s);
    }

    private static function stripParenSuffix(string $s): string
    {
        // remove "(Forma Longa)" etc
        return trim(preg_replace('/\s*\([^)]*\)\s*/u', ' ', $s) ?? '');
    }

    /**
     * "Lc 24, 1-12" -> "Luke 24:1-12"
     * "Sl 103" -> "Psalm 103"
     * "Ex 15, 1-6. 17-18" -> "Exodus 15:1-6,17-18"
     */
    public function ptRefToNetPassage(string $ptRef): ?string
    {
        $raw = self::stripParenSuffix(self::normalizeDashes(self::safeStr($ptRef)));
        if ($raw === '') return null;

        // "Lc 24, 1-12"
        if (preg_match('/^([1-3]?\s?[A-Za-zÀ-ÿ]+)\s+(.+)$/u', $raw, $m)) {
            $bookKey = preg_replace('/\s+/u', '', $m[1] ?? '');
            $book = self::BOOK_MAP[$bookKey] ?? (self::BOOK_MAP[trim($m[1])] ?? null);
            if (!$book) return null;

            $rest = trim($m[2] ?? '');
            $rest = preg_replace('/\s*,\s*/u', ':', $rest);  // cap, verso -> cap:verso
            $rest = preg_replace('/\s*\.\s*/u', ',', $rest); // "1-6. 17-18" -> "1-6,17-18"
            $rest = preg_replace('/\s+/u', '', $rest);

            return trim($book . ' ' . $rest);
        }

        // "Sl 103"
        if (preg_match('/^([1-3]?\s?[A-Za-zÀ-ÿ]+)\s+(\d+)\s*$/u', $raw, $m2)) {
            $k1 = preg_replace('/\s+/u', '', $m2[1] ?? '');
            $book = self::BOOK_MAP[$k1] ?? (self::BOOK_MAP[trim($m2[1])] ?? null);
            if (!$book) return null;

            return trim($book . ' ' . ($m2[2] ?? ''));
        }

        return null;
    }

    /**
     * Junta versos e mantém numeração (cap:verso quando muda capítulo).
     */
    private function joinNetVerses(array $rows, bool $showChapterWhenChanges = true): string
    {
        $lastChapter = null;
        $parts = [];

        foreach ($rows as $r) {
            $chapter = self::safeStr($r['chapter'] ?? null);
            $verse   = self::safeStr($r['verse'] ?? null);
            $txt     = self::safeStr($r['text'] ?? null);
            if ($txt === '') continue;

            $label = '';
            if ($chapter !== '' && $verse !== '') {
                if ($showChapterWhenChanges && $lastChapter !== null && $chapter !== $lastChapter) {
                    $label = "{$chapter}:{$verse} ";
                } else {
                    $label = "{$verse} ";
                }
                $lastChapter = $chapter;
            }

            $parts[] = $label . $txt;
        }

        return trim(implode(' ', $parts));
    }

    /**
     * "3:16For" -> "3:16 For" e "16For" -> "16 For"
     */
    private function normalizeVerseSpacing(string $s): string
    {
        $s = preg_replace('/(\d{1,3}:\d{1,3})(?=\S)/u', '$1 ', $s) ?? $s;
        $s = preg_replace('/(^|\s)(\d{1,3})(?=\S)/u', '$1$2 ', $s) ?? $s;
        return trim($s);
    }

    /**
     * Converte números de versículo (e cap:verso) em <sup class="...">...</sup>
     * - escapa HTML
     * - suporta aspas/parênteses logo após número
     */
    private function formatVersesToSupHtmlEN(string $plain): string
    {
        $raw = self::safeStr($plain);
        if ($raw === '') return '';
        if (str_contains($raw, '<sup')) return $raw; // defensivo

        $s = e($this->normalizeVerseSpacing($raw));

        // após o número pode vir espaço + aspas/parênteses + letra
        $nextIsText = '(?=\s*["“”\'‘(\[]?[A-Za-zÀ-ÿ])';

        // capítulo:verso
        $s = preg_replace_callback(
            '/(^|[\s\.;:!\?—–-])(\d{1,3}:\d{1,3})' . $nextIsText . '/u',
            fn($m) => $m[1] . '<sup class="' . self::VERSE_PILL_CLASS . '">' . $m[2] . '</sup>',
            $s
        ) ?? $s;

        // só verso
        $s = preg_replace_callback(
            '/(^|[\s\.;:!\?—–-])(\d{1,3})' . $nextIsText . '/u',
            fn($m) => $m[1] . '<sup class="' . self::VERSE_PILL_CLASS . '">' . $m[2] . '</sup>',
            $s
        ) ?? $s;

        return $s;
    }

    /**
     * Texto EN "plain" com numeração consistente (cache 24h).
     */
    public function fetchNetBibleText(string $ptRef): ?string
    {
        $passage = $this->ptRefToNetPassage($ptRef);
        if (!$passage) return null;

        $cacheKey = 'netbible:text:' . md5($passage);

        return Cache::remember($cacheKey, now()->addDay(), function () use ($passage) {
            $res = Http::timeout(12)
                ->retry(2, 250)
                ->get(self::NET_BIBLE_ENDPOINT, [
                    'passage' => $passage,
                    'type' => 'json',
                    'formatting' => 'plain',
                ]);

            if (!$res->ok()) return null;

            $json = $res->json();
            if (!is_array($json) || count($json) === 0) return null;

            // normaliza para array de arrays
            $rows = [];
            foreach ($json as $r) {
                if (!is_array($r)) continue;
                $rows[] = [
                    'bookname' => $r['bookname'] ?? null,
                    'chapter'  => $r['chapter'] ?? null,
                    'verse'    => $r['verse'] ?? null,
                    'text'     => $r['text'] ?? null,
                ];
            }

            $joined = $this->joinNetVerses($rows, true);
            $joined = $joined ? $this->normalizeVerseSpacing($joined) : null;

            return $joined ? trim($joined) : null;
        });
    }

    /**
     * HTML pronto (EN-only) com <sup> para render direto no Blade.
     */
    public function fetchNetBibleHtml(string $ptRef): ?string
    {
        $passage = $this->ptRefToNetPassage($ptRef);
        if (!$passage) return null;

        $cacheKey = 'netbible:html:' . md5($passage);

        return Cache::remember($cacheKey, now()->addDay(), function () use ($ptRef) {
            $text = $this->fetchNetBibleText($ptRef);
            if (!$text) return null;
            return $this->formatVersesToSupHtmlEN($text);
        });
    }

    /**
     * Aplica EN em um "item de leitura" do seu payload:
     * usa referencia PT e sobrescreve textoHtml/texto.
     */
    public function applyToReadingItem(array $item): array
    {
        $ref = self::safeStr($item['referencia'] ?? null);
        if ($ref === '') return $item;

        $html = $this->fetchNetBibleHtml($ref);
        $text = $html ? null : $this->fetchNetBibleText($ref);

        if ($html) {
            $item['textoHtml'] = $html; // render direto
            $item['texto'] = strip_tags($html);
        } elseif ($text) {
            $item['texto'] = $text;
            $item['textoHtml'] = null; // deixa o seu renderizador fazer fallback
        }

        return $item;
    }
}
