<?php

namespace App\Support;

class LiturgiaDate
{
    public static function parseSlug(string $slug): ?array
    {
        $parsed = self::parseSlugWithFormat($slug);

        return $parsed
            ? [$parsed['day'], $parsed['month'], $parsed['year']]
            : null;
    }

    public static function parseSlugWithFormat(string $slug): ?array
    {
        $slug = trim($slug);

        if (!preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $slug, $m)) {
            return null;
        }

        $a = (int) $m[1];
        $b = (int) $m[2];
        $year = (int) $m[3];

        // 1) tenta formato brasileiro/canônico: dd-mm-yyyy
        if (checkdate($b, $a, $year)) {
            return [
                'day' => $a,
                'month' => $b,
                'year' => $year,
                'input_format' => 'br',
            ];
        }

        // 2) tenta formato americano apenas quando o BR não é válido
        if (checkdate($a, $b, $year)) {
            return [
                'day' => $b,
                'month' => $a,
                'year' => $year,
                'input_format' => 'us',
            ];
        }

        return null;
    }

    public static function normalizeDaySlug(string $slug): ?array
    {
        $slug = trim($slug);
        $slug = str_replace('/', '-', $slug);

        if ($slug === '') {
            return null;
        }

        // ISO: yyyy-mm-dd
        if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $slug, $m)) {
            $year = (int) $m[1];
            $month = (int) $m[2];
            $day = (int) $m[3];

            if (!checkdate($month, $day, $year)) {
                return null;
            }

            return [
                'day' => $day,
                'month' => $month,
                'year' => $year,
                'input_format' => 'iso',
                'slug' => self::slugFrom($day, $month, $year),
            ];
        }

        // dd-mm-yyyy, d-m-yyyy, mm-dd-yyyy em casos não ambíguos
        if (preg_match('/^(\d{1,2})-(\d{1,2})-(\d{4})$/', $slug, $m)) {
            $a = (int) $m[1];
            $b = (int) $m[2];
            $year = (int) $m[3];

            // Mantém compatibilidade com o padrão atual: dd-mm-yyyy.
            // Em datas ambíguas, ex: 05-07-2026, preserva como 05/07/2026.
            if (checkdate($b, $a, $year)) {
                return [
                    'day' => $a,
                    'month' => $b,
                    'year' => $year,
                    'input_format' => 'br',
                    'slug' => self::slugFrom($a, $b, $year),
                ];
            }

            // Aceita padrão americano apenas quando o BR for impossível.
            // Ex: 12-31-2026 -> 31-12-2026.
            if (checkdate($a, $b, $year)) {
                return [
                    'day' => $b,
                    'month' => $a,
                    'year' => $year,
                    'input_format' => 'us',
                    'slug' => self::slugFrom($b, $a, $year),
                ];
            }

            return null;
        }

        return null;
    }

    public static function slugFrom(int $day, int $month, int $year): string
    {
        return sprintf('%02d-%02d-%04d', $day, $month, $year);
    }
}