<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RosaryController extends Controller
{
    private string $siteUrl = 'https://www.iatioben.com.br';

    // =====================
    // HUB (orações guiadas)
    // =====================

    public function hubPt(Request $request)
    {
        $lang = 'pt-BR';

        $setKey = $this->setFromWeekdaySP();
        $canonical = $this->siteUrl . "/santo-terco";

        $meta = $this->buildMetaPtHub($setKey, $canonical);

        return view('terco.page', [
            'lang' => $lang,
            'meta' => $meta,
            'initial' => [
                'lang' => 'pt',
                'route' => 'pt',
                'canonical' => $canonical,
                'setKey' => $setKey,
                'slug' => null,
            ],
        ]);
    }

    public function hubEn(Request $request)
    {
        $lang = 'en';

        $setKey = $this->setFromWeekdaySP();
        $canonical = $this->siteUrl . "/en/rosary";

        $meta = $this->buildMetaEnHub($setKey, $canonical);

        return view('terco.page', [
            'lang' => $lang,
            'meta' => $meta,
            'initial' => [
                'lang' => 'en',
                'route' => 'en',
                'canonical' => $canonical,
                'setKey' => $setKey,
                'slug' => null,
            ],
        ]);
    }

    // =====================
    // CONTENT (mistérios + reflexões)
    // =====================

    public function contentPt(Request $request)
    {
        $set = (string) $request->route('set');
        $setKey = $this->normalizeSetKeyStrict($set);

        // view dedicada por set
        $view = match ($setKey) {
            'gozosos'   => 'terco.gozosos',
            'dolorosos' => 'terco.dolorosos',
            'gloriosos' => 'terco.gloriosos',
            'luminosos' => 'terco.luminosos',
        };

        return view($view);
    }

    public function contentEn(Request $request)
{
    $set = (string) $request->route('set'); // gozosos|dolorosos|gloriosos|luminosos

    $view = match ($set) {
        'gozosos'   => 'en.rosary.joyful-mysteries',
        'dolorosos' => 'en.rosary.sorrowful-mysteries',
        'gloriosos' => 'en.rosary.glorious-mysteries',
        'luminosos' => 'en.rosary.luminous-mysteries',
        default     => abort(404),
    };

    abort_unless(view()->exists($view), 404, "View [$view] not found.");
    return view($view);
}

    // =====================
    // Helpers
    // =====================

    private function weekdayInSaoPaulo(): int
    {
        // 0=domingo ... 6=sábado
        return Carbon::now('America/Sao_Paulo')->dayOfWeek;
    }

    private function setFromWeekdaySP(): string
    {
        $dow = $this->weekdayInSaoPaulo();
        if ($dow === 1 || $dow === 6) return 'gozosos';
        if ($dow === 2 || $dow === 5) return 'dolorosos';
        if ($dow === 3 || $dow === 0) return 'gloriosos';
        return 'luminosos';
    }

    private function normalizeSetKeyStrict(string $set): string
    {
        return match ($set) {
            'gozosos', 'dolorosos', 'gloriosos', 'luminosos' => $set,
            default => abort(404),
        };
    }

    private function ogFromSet(string $set): string
    {
        return "{$this->siteUrl}/og/terco/misterios-{$set}.png?v=1";
    }

    private function labelPt(string $set): string
    {
        return match ($set) {
            'gozosos' => 'Mistérios Gozosos',
            'dolorosos' => 'Mistérios Dolorosos',
            'gloriosos' => 'Mistérios Gloriosos',
            'luminosos' => 'Mistérios Luminosos',
            default => 'Mistérios do Rosário',
        };
    }

    private function daysLinePt(string $set): string
    {
        return match ($set) {
            'gozosos' => 'Segunda e Sábado',
            'dolorosos' => 'Terça e Sexta',
            'gloriosos' => 'Quarta e Domingo',
            'luminosos' => 'Quinta-feira',
            default => 'Hoje',
        };
    }

    // =====================
    // META HUB (orações)
    // =====================

    private function buildMetaPtHub(string $set, string $canonical): array
    {
        $label = $this->labelPt($set);
        $days = $this->daysLinePt($set);
        $og = $this->ogFromSet($set);

        $title = "Santo Terço de Hoje: {$label} (passo a passo) | IA Tio Ben";
        $description = "{$days}: reze o Santo Terço no celular com orações completas, reflexões bíblicas em cada dezena e progresso guiado. Clique e comece agora.";

        return [
            'html_lang' => 'pt-BR',
            'title' => $title,
            'description' => $description,
            'canonical' => $canonical,
            'robots' => 'index,follow',
            'og' => [
                'title' => $title,
                'description' => $description,
                'url' => $canonical,
                'image' => $og,
                'locale' => 'pt_BR',
                'alt' => "Santo Terço de Hoje — {$label}",
            ],
            'twitter' => [
                'card' => 'summary_large_image',
                'title' => $title,
                'description' => $description,
                'image' => $og,
            ],
            'hreflangs' => [
                'pt-BR' => $this->siteUrl . '/santo-terco',
                'en' => $this->siteUrl . '/en/rosary',
                'x-default' => $this->siteUrl . '/santo-terco',
            ],
            'set' => $set,
        ];
    }

    private function buildMetaEnHub(string $set, string $canonical): array
    {
        $og = $this->ogFromSet($set);

        $title = "Rosary for Today: guided step-by-step prayer (with Scripture) | IA Tio Ben";
        $description =
            "Pray the Rosary on your phone with a clear step-by-step flow, the mysteries for today, " .
            "Scripture-based meditation for each decade, and a progress tracker designed for mobile.";

        return [
            'html_lang' => 'en',
            'title' => $title,
            'description' => $description,
            'canonical' => $canonical,
            'robots' => 'index,follow',
            'og' => [
                'title' => $title,
                'description' => $description,
                'url' => $canonical,
                'image' => $og,
                'locale' => 'en_US',
                'alt' => "Rosary for Today — guided prayer",
            ],
            'twitter' => [
                'card' => 'summary_large_image',
                'title' => $title,
                'description' => $description,
                'image' => $og,
            ],
            'hreflangs' => [
                'pt-BR' => $this->siteUrl . '/santo-terco',
                'en' => $this->siteUrl . '/en/rosary',
                'x-default' => $this->siteUrl . '/santo-terco',
            ],
            'set' => $set,
        ];
    }
}
