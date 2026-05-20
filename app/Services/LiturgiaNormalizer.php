<?php

namespace App\Services;

class LiturgiaNormalizer
{
    private const VERSE_SUP_CLASS =
        'mx-0.5 align-baseline rounded bg-amber-50 px-1 text-[0.75em] font-semibold text-amber-900/90 border border-amber-100';

    public function normalize(array $raw, int $day, int $month, int $year): array
    {
        $dd = $this->pad2($day);
        $mm = $this->pad2($month);

        $dateSlug  = "{$dd}-{$mm}-{$year}";
        $dateISO   = "{$year}-{$mm}-{$dd}";
        $dateLabel = "{$dd}/{$mm}/{$year}";

        $weekday = $this->weekdayPT($day, $month, $year);

        $primeiraArr  = $this->mapTextoHtml(data_get($raw, 'leituras.primeiraLeitura', []));
        $segundaArr   = $this->mapTextoHtml(data_get($raw, 'leituras.segundaLeitura', []));
        $salmoArr     = $this->mapTextoHtml(data_get($raw, 'leituras.salmo', []));
        $evangelhoArr = $this->mapTextoHtml(data_get($raw, 'leituras.evangelho', []));
        $extrasArr    = $this->mapTextoHtml(data_get($raw, 'leituras.extras', []));

        $primeira0  = $primeiraArr[0] ?? null;
        $salmo0     = $salmoArr[0] ?? null;
        $segunda0   = $segundaArr[0] ?? null;
        $evangelho0 = $evangelhoArr[0] ?? null;

        $primeiraTexto  = $this->safeStr($primeira0['texto'] ?? '');
        $salmoTexto     = $this->safeStr($salmo0['texto'] ?? '');
        $segundaTexto   = $this->safeStr($segunda0['texto'] ?? '');
        $evangelhoTexto = $this->safeStr($evangelho0['texto'] ?? '');

        $antEntrada  = $this->safeStr(data_get($raw, 'antifonas.entrada', ''));
        $antComunhao = $this->safeStr(data_get($raw, 'antifonas.comunhao', ''));

        $coleta    = $this->safeStr(data_get($raw, 'oracoes.coleta', ''));
        $oferendas = $this->safeStr(data_get($raw, 'oracoes.oferendas', ''));
        $comunhao  = $this->safeStr(data_get($raw, 'oracoes.comunhao', ''));

        return [
            'dateSlug' => $dateSlug,
            'dateISO' => $dateISO,
            'dateLabel' => $dateLabel,
            'weekday' => $weekday,
            'season' => '',
            'celebration' => $this->safeStr($raw['liturgia'] ?? ''),
            'color' => $this->safeStr($raw['cor'] ?? ''),

            'primeiraRef' => $this->safeStr($primeira0['referencia'] ?? ''),
            'salmoRef' => $this->safeStr($salmo0['referencia'] ?? ''),
            'segundaRef' => $this->safeStr($segunda0['referencia'] ?? ''),
            'evangelhoRef' => $this->safeStr($evangelho0['referencia'] ?? ''),

            // plain
            'primeiraTexto' => $primeiraTexto,
            'salmoTexto' => $salmoTexto,
            'segundaTexto' => $segundaTexto,
            'evangelhoTexto' => $evangelhoTexto,

            'antEntrada' => $antEntrada,
            'antComunhao' => $antComunhao,

            // html
            'primeiraHtml' => $this->toReadableHtml($primeiraTexto),
            'salmoHtml' => $this->toReadableHtml($salmoTexto),
            'segundaHtml' => $this->toReadableHtml($segundaTexto),
            'evangelhoHtml' => $this->toReadableHtml($evangelhoTexto),

            'antEntradaHtml' => $this->toReadableHtml($antEntrada),
            'antComunhaoHtml' => $this->toReadableHtml($antComunhao),

            'evangelhoPreview' => $this->firstSentences($evangelhoTexto, 280),

            'leiturasFull' => [
                'primeiraLeitura' => $primeiraArr,
                'segundaLeitura' => $segundaArr,
                'salmo' => $salmoArr,
                'evangelho' => $evangelhoArr,
                'extras' => $extrasArr,
            ],

            'oracoesFull' => [
                'coleta' => $coleta,
                'coletaHtml' => $coleta ? $this->toReadableHtml($coleta) : '',
                'oferendas' => $oferendas,
                'oferendasHtml' => $oferendas ? $this->toReadableHtml($oferendas) : '',
                'comunhao' => $comunhao,
                'comunhaoHtml' => $comunhao ? $this->toReadableHtml($comunhao) : '',
                'extras' => is_array(data_get($raw, 'oracoes.extras')) ? data_get($raw, 'oracoes.extras') : [],
            ],

            'antifonasFull' => [
                'entrada' => $antEntrada,
                'entradaHtml' => $antEntrada ? $this->toReadableHtml($antEntrada) : '',
                'comunhao' => $antComunhao,
                'comunhaoHtml' => $antComunhao ? $this->toReadableHtml($antComunhao) : '',
            ],

            'raw' => $raw,
        ];
    }

    private function mapTextoHtml($arr): array
    {
        if (!is_array($arr)) return [];
        return array_map(function ($it) {
            $texto = $this->safeStr($it['texto'] ?? '');
            return array_merge($it, [
                'texto' => $texto,
                'textoHtml' => $texto ? $this->toReadableHtml($texto) : '',
            ]);
        }, $arr);
    }

    private function safeStr($v): string
    {
        if (is_string($v)) return trim($v);
        if ($v === null) return '';
        return trim((string)$v);
    }

    private function pad2(int $n): string
    {
        return str_pad((string)$n, 2, '0', STR_PAD_LEFT);
    }

    private function weekdayPT(int $dd, int $mm, int $yyyy): string
    {
        $dt = \DateTime::createFromFormat('Y-m-d H:i:s', sprintf('%04d-%02d-%02d 12:00:00', $yyyy, $mm, $dd));
        if (!$dt) return '';
        // tenta pt_BR; se não tiver locale instalado, cai para padrão
        try {
            $fmt = new \IntlDateFormatter('pt_BR', \IntlDateFormatter::FULL, \IntlDateFormatter::NONE, $dt->getTimezone(), null, 'EEEE');
            return $fmt->format($dt) ?: '';
        } catch (\Throwable $e) {
            return '';
        }
    }

    private function escapeHtml(string $s): string
    {
        return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    private function formatVersesToSup(string $text): string
    {
        $raw = $this->safeStr($text);
        if ($raw === '') return '';

        if (str_contains($raw, '<sup')) return $raw;

        $s = $this->escapeHtml($raw);
        $s = str_replace(["\r\n", "\r"], ["\n", "\n"], $s);

        // Heurística equivalente: (^|[space/pontuação])(\d{1,3})(?=[A-ZÁÉÍÓÚ...])
        $pattern = '/(^|[\s\.\;\:\!\?\—–\-])(\d{1,3})(?=[A-ZÁÉÍÓÚÂÊÔÃÕÀÇ])/u';

        return preg_replace_callback($pattern, function ($m) {
            $before = $m[1];
            $num = $m[2];
            return $before . '<sup class="'.self::VERSE_SUP_CLASS.'">'.$num.'</sup>';
        }, $s) ?? $s;
    }

    private function toReadableHtml(string $text): string
    {
        $raw = $this->safeStr($text);
        if ($raw === '') return '';

        $s = $this->formatVersesToSup($raw);
        $s = str_replace(["\r\n", "\r"], ["\n", "\n"], $s);
        $s = trim($s);

        $blocks = preg_split("/\n{2,}/", $s) ?: [];
        $blocks = array_values(array_filter(array_map('trim', $blocks)));

        $out = '';
        foreach ($blocks as $b) {
            $out .= '<p>' . str_replace("\n", '<br/>', $b) . '</p>';
        }
        return $out;
    }

    private function firstSentences(string $text, int $maxChars = 260): string
    {
        $t = preg_replace('/\s+/', ' ', trim($text)) ?? '';
        if ($t === '') return '';
        if (mb_strlen($t) <= $maxChars) return $t;
        $cut = mb_substr($t, 0, $maxChars);
        $cut = preg_replace('/[.,;:\s]+$/u', '', $cut) ?? $cut;
        return $cut.'…';
    }
}
