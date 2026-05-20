<?php

namespace App\Support;

class Seo
{
    public static function canonical(string $path): string
    {
        return rtrim(config('app.url'), '/') . '/' . ltrim($path, '/');
    }

    public static function hreflangs(string $ptPath, string $enPath): array
    {
        return [
            'pt-BR' => self::canonical($ptPath),
            'en' => self::canonical($enPath),
            'x-default' => self::canonical($ptPath),
        ];
    }

    public static function ogImage(string $path): string
    {
        return self::canonical($path);
    }
}
