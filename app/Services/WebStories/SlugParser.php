<?php

namespace App\Services\WebStories;

class SlugParser
{
    public function parse(string $slug): ?array
    {
        // liturgia-26-01-2026  OU  terco-26-01-2026
        if (!preg_match('/^(liturgia|terco)-(\d{2})-(\d{2})-(\d{4})$/', $slug, $m)) {
            return null;
        }

        $kind = $m[1];
        $dd = $m[2];
        $mm = $m[3];
        $yyyy = $m[4];

        $isoDate = "{$yyyy}-{$mm}-{$dd}";
        if (!$this->isValidIsoDate($isoDate)) return null;

        return ['kind' => $kind, 'isoDate' => $isoDate];
    }

    private function isValidIsoDate(string $iso): bool
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $iso)) return false;
        [$y, $m, $d] = array_map('intval', explode('-', $iso));
        return checkdate($m, $d, $y);
    }
}
