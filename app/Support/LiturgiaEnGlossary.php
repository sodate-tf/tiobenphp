<?php

namespace App\Support;

class LiturgiaEnGlossary
{
    /**
     * Traduz textos curtos e “padronizados” da liturgia que vêm em PT,
     * usando glossário + regras simples.
     */
    public static function t(?string $s): string
    {
        $s = trim((string)$s);
        if ($s === '') return '';

        // Normalizações simples (acentos e espaços)
        $normalized = preg_replace('/\s+/u', ' ', $s) ?? $s;

        // Casos diretos (títulos/labels curtinhos)
        $direct = [
            // Tempo litúrgico / termos gerais
            'Tempo Comum' => 'Ordinary Time',
            'Semana do Tempo Comum' => 'Week in Ordinary Time',
            'Advento' => 'Advent',
            'Quaresma' => 'Lent',
            'Tempo da Quaresma' => 'Lent',
            'Páscoa' => 'Easter',
            'Tempo Pascal' => 'Easter Season',
            'Natal' => 'Christmas',
            'Tempo do Natal' => 'Christmas Season',

            // Cores
            'Verde' => 'Green',
            'Roxo' => 'Violet',
            'Vermelho' => 'Red',
            'Branco' => 'White',
            'Rosa' => 'Rose',
            'Preto' => 'Black',
            'Dourado' => 'Gold',
        ];

        if (isset($direct[$normalized])) {
            return $direct[$normalized];
        }

        return $normalized;
    }

    /**
     * Traduz "3ª feira da 6ª Semana do Tempo Comum" => "Tuesday of the 6th Week in Ordinary Time"
     * + alguns padrões próximos.
     */
    public static function translateCelebration(?string $celebration): string
    {
        $c = trim((string)$celebration);
        if ($c === '') return '';

        // Se já parece inglês, não mexe
        if (preg_match('/\b(Monday|Tuesday|Wednesday|Thursday|Friday|Saturday|Sunday)\b/i', $c)) {
            return $c;
        }

        $c = preg_replace('/\s+/u', ' ', $c) ?? $c;

        // "3ª feira ..." -> weekday
        $weekdayMap = [
            '2ª feira' => 'Monday',
            '3ª feira' => 'Tuesday',
            '4ª feira' => 'Wednesday',
            '5ª feira' => 'Thursday',
            '6ª feira' => 'Friday',
            'sábado'   => 'Saturday',
            'sabado'   => 'Saturday',
            'domingo'  => 'Sunday',
        ];

        // Captura:
        //  (weekday) da (Nª) Semana do (Tempo Comum|Advento|Quaresma|Tempo Pascal|Tempo do Natal)
        $re = '/^(?<weekday>2ª feira|3ª feira|4ª feira|5ª feira|6ª feira|sábado|sabado|domingo)\s+da\s+(?<n>\d{1,2})ª\s+Semana\s+do\s+(?<season>Tempo Comum|Advento|Quaresma|Tempo Pascal|Tempo do Natal)$/iu';

        if (preg_match($re, $c, $m)) {
            $weekdayPt = mb_strtolower($m['weekday']);
            $weekdayEn = $weekdayMap[$weekdayPt] ?? ucfirst($m['weekday']);

            $n = (int)$m['n'];
            $seasonEn = match (mb_strtolower($m['season'])) {
                'tempo comum' => 'Ordinary Time',
                'advento' => 'Advent',
                'quaresma' => 'Lent',
                'tempo pascal' => 'Easter Season',
                'tempo do natal' => 'Christmas Season',
                default => self::t($m['season']),
            };

            return "{$weekdayEn} of the " . self::ordinalEn($n) . " Week in {$seasonEn}";
        }

        // fallback: trocas pontuais dentro do texto (menos agressivo)
        $c = str_ireplace('Tempo Comum', 'Ordinary Time', $c);
        $c = str_ireplace('Quaresma', 'Lent', $c);
        $c = str_ireplace('Advento', 'Advent', $c);
        $c = str_ireplace('Tempo Pascal', 'Easter Season', $c);
        $c = str_ireplace('Tempo do Natal', 'Christmas Season', $c);

        // weekdays soltos
        foreach ($weekdayMap as $pt => $en) {
            $c = preg_replace('/\b' . preg_quote($pt, '/') . '\b/iu', $en, $c) ?? $c;
        }

        return $c;
    }

    public static function translateColor(?string $color): string
    {
        return self::t($color);
    }

    /**
     * Respostas/aclamações fixas
     */
    public static function acclamation(string $key): string
    {
        return match ($key) {
            'word_of_the_lord' => 'The word of the Lord.',
            'thanks_be_to_god' => 'Thanks be to God.',
            'word_of_the_gospel' => 'The Gospel of the Lord.',
            'praise_to_you_lord_jesus_christ' => 'Praise to you, Lord Jesus Christ.',
            default => $key,
        };
    }

    /**
     * Traduz subtítulos muito comuns (opcional, mas ajuda).
     * Ex.: "Proclamação do Evangelho de Jesus Cristo segundo Marcos"
     * => "A reading from the holy Gospel according to Mark"
     */
    public static function translateSubtitleIfPt(?string $subtitle, string $kind = 'reading'): string
    {
        $s = trim((string)$subtitle);
        if ($s === '') return '';

        // já está em inglês
        if (preg_match('/\b(according to|A reading from|Gospel)\b/i', $s)) return $s;

        // Gospel segundo X
        if ($kind === 'gospel') {
            $m = null;
            if (preg_match('/segundo\s+(Mateus|Marcos|Lucas|João|Joao)\b/iu', $s, $m)) {
                $ev = mb_strtolower($m[1]);
                $name = match ($ev) {
                    'mateus' => 'Matthew',
                    'marcos' => 'Mark',
                    'lucas' => 'Luke',
                    'joão', 'joao' => 'John',
                    default => ucfirst($m[1]),
                };
                return "A reading from the holy Gospel according to {$name}";
            }
            return "A reading from the holy Gospel";
        }

        // "Leitura da carta de são X"
        if (preg_match('/^Leitura\s+da\s+carta\s+de\s+s[aã]o\s+(.+)$/iu', $s, $m)) {
            $who = trim($m[1]);
            return "A reading from the Letter of Saint {$who}";
        }

        // "Leitura do livro de X"
        if (preg_match('/^Leitura\s+do\s+livro\s+de\s+(.+)$/iu', $s, $m)) {
            $book = trim($m[1]);
            return "A reading from the Book of {$book}";
        }

        return $s; // fallback
    }

    private static function ordinalEn(int $n): string
    {
        // 1st, 2nd, 3rd, 4th...
        $mod100 = $n % 100;
        if ($mod100 >= 11 && $mod100 <= 13) return "{$n}th";

        return match ($n % 10) {
            1 => "{$n}st",
            2 => "{$n}nd",
            3 => "{$n}rd",
            default => "{$n}th",
        };
    }
}
