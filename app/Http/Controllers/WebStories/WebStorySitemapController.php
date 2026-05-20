<?php

namespace App\Http\Controllers\WebStories;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Response;

class WebStorySitemapController extends Controller
{
    public function liturgia(): Response
    {
        return $this->yearlyDailySitemap('liturgia');
    }

    public function terco(): Response
    {
        return $this->yearlyDailySitemap('terco');
    }

    private function yearlyDailySitemap(string $kind): Response
    {
        $site = rtrim(config('app.url'), '/');
        $year = (int)now()->format('Y');

        $start = Carbon::create($year, 1, 1, 12, 0, 0, 'UTC');
        $end = Carbon::create($year, 12, 31, 12, 0, 0, 'UTC');

        $urls = [];

        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $iso = $d->format('Y-m-d');
            $slugDate = $d->format('d-m-Y'); // DD-MM-YYYY
            $loc = "{$site}/web-stories/{$kind}-{$slugDate}/";
            $urls[] = ['loc' => $loc, 'lastmod' => $iso];
        }

        $xml = $this->buildUrlset($urls);

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'Cache-Control' => 'public, s-maxage=3600, stale-while-revalidate=86400',
        ]);
    }

    private function xmlEscape(string $s): string
    {
        return htmlspecialchars($s ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    private function buildUrlset(array $urls): string
    {
        $body = [];
        foreach ($urls as $u) {
            $body[] = trim('
  <url>
    <loc>'.$this->xmlEscape($u['loc']).'</loc>
    '.(!empty($u['lastmod']) ? '<lastmod>'.$this->xmlEscape($u['lastmod']).'</lastmod>' : '').'
  </url>');
        }

        return '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
'.implode("\n", $body).'
</urlset>';
    }
}
