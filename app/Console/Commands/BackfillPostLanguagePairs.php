<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class BackfillPostLanguagePairs extends Command
{
    protected $signature = 'posts:backfill-language-pairs
        {--write : Persiste alterações (sem essa flag roda em dry-run)}
        {--limit=0 : Limite de posts PT analisados}
        {--window-days=5 : Janela de dias para comparar publish_date}';

    protected $description = 'Pareia posts PT<->EN antigos e unifica uuid para hreflang consistente';

    public function handle(): int
    {
        $write = (bool) $this->option('write');
        $limit = max(0, (int) $this->option('limit'));
        $windowDays = max(1, (int) $this->option('window-days'));

        $this->info($write ? 'Modo WRITE: alterações serão salvas.' : 'Modo DRY-RUN: nenhuma alteração será salva.');

        $ptQuery = Post::query()
            ->where('lang', 'pt')
            ->where('is_active', true)
            ->orderByDesc('publish_date')
            ->orderByDesc('id');

        if ($limit > 0) {
            $ptQuery->limit($limit);
        }

        /** @var Collection<int, Post> $ptPosts */
        $ptPosts = $ptQuery->get();

        /** @var Collection<int, Post> $enPosts */
        $enPosts = Post::query()
            ->where('lang', 'en')
            ->where('is_active', true)
            ->get();

        $updated = 0;
        $alreadyPaired = 0;
        $notFound = 0;
        $ambiguous = 0;
        $usedEnIds = [];

        foreach ($ptPosts as $pt) {
            $ptUuid = (string) ($pt->uuid ?? '');
            if ($ptUuid !== '') {
                $sameUuid = $enPosts->first(fn (Post $en) => (string) $en->uuid === $ptUuid);
                if ($sameUuid) {
                    $usedEnIds[(int) $sameUuid->id] = true;
                }
            }
        }

        foreach ($ptPosts as $pt) {
            $ptUuid = (string) ($pt->uuid ?? '');
            if ($ptUuid !== '') {
                $sameUuid = $enPosts->first(fn (Post $en) => (string) $en->uuid === $ptUuid);
                if ($sameUuid) {
                    $alreadyPaired++;
                    continue;
                }
            }

            $candidate = $this->pickBestEnCandidate($pt, $enPosts, $windowDays, $usedEnIds);
            if (!$candidate) {
                $notFound++;
                continue;
            }

            $ptUuidToUse = $ptUuid !== '' ? $ptUuid : (string) Str::uuid();
            $enUuid = (string) ($candidate->uuid ?? '');

            if ($enUuid !== '' && $enUuid !== $ptUuidToUse) {
                $conflictPt = Post::query()
                    ->where('lang', 'pt')
                    ->where('uuid', $enUuid)
                    ->where('id', '!=', $pt->id)
                    ->first();

                if ($conflictPt) {
                    $ambiguous++;
                    $this->warn("Conflito de uuid EN detectado: PT {$pt->id} x EN {$candidate->id} (uuid já ligado ao PT {$conflictPt->id}).");
                    continue;
                }
            }

            $this->line("Pareado PT {$pt->id} ({$pt->slug}) <-> EN {$candidate->id} ({$candidate->slug}) | uuid {$ptUuidToUse}");

            if ($write) {
                if ($ptUuid === '') {
                    $pt->uuid = $ptUuidToUse;
                    $pt->save();
                }
                if ((string) $candidate->uuid !== $ptUuidToUse) {
                    $candidate->uuid = $ptUuidToUse;
                    $candidate->save();
                }
            }

            $usedEnIds[(int) $candidate->id] = true;
            $updated++;
        }

        $this->newLine();
        $this->info('Resumo:');
        $this->line("- PT analisados: {$ptPosts->count()}");
        $this->line("- Já pareados: {$alreadyPaired}");
        $this->line("- Novos pareamentos: {$updated}");
        $this->line("- Sem candidato: {$notFound}");
        $this->line("- Ambíguos/conflito: {$ambiguous}");

        if (!$write) {
            $this->comment('Para efetivar, execute novamente com --write');
        }

        return self::SUCCESS;
    }

    private function pickBestEnCandidate(Post $pt, Collection $enPosts, int $windowDays, array $usedEnIds): ?Post
    {
        $ptSlug = (string) ($pt->slug ?? '');
        $ptTitle = (string) ($pt->title ?? '');
        $ptDate = $pt->publish_date;

        $ptSlugNorm = $this->normalizeSlug($ptSlug);
        $ptTitleSlug = $this->normalizeSlug(Str::slug($ptTitle));

        $scored = [];

        foreach ($enPosts as $en) {
            if (isset($usedEnIds[(int) $en->id])) {
                continue;
            }

            $score = 0;
            $reasons = [];

            $enSlug = (string) ($en->slug ?? '');
            $enTitle = (string) ($en->title ?? '');
            $enDate = $en->publish_date;

            $enSlugNorm = $this->normalizeSlug($enSlug);
            $enTitleSlug = $this->normalizeSlug(Str::slug($enTitle));

            if ($ptSlug !== '' && $enSlug === $ptSlug) {
                $score += 100;
                $reasons[] = 'slug-exato';
            }

            if ($ptSlugNorm !== '' && $enSlugNorm === $ptSlugNorm) {
                $score += 80;
                $reasons[] = 'slug-normalizado';
            }

            if ($ptTitleSlug !== '' && $enTitleSlug === $ptTitleSlug) {
                $score += 50;
                $reasons[] = 'titulo-slug';
            }

            $titleOverlap = $this->tokenOverlapScore($ptTitle, $enTitle);
            if ($titleOverlap > 0) {
                $score += $titleOverlap;
                $reasons[] = "titulo-overlap-{$titleOverlap}";
            }

            if ($ptDate && $enDate) {
                $days = abs($ptDate->diffInDays($enDate));
                if ($days === 0) {
                    $score += 45;
                    $reasons[] = 'mesma-data';
                } elseif ($days <= $windowDays) {
                    $score += max(0, 30 - ($days * 4));
                    $reasons[] = "janela-{$days}d";
                }
            }

            if ($pt->created_at && $en->created_at) {
                $minutes = abs($pt->created_at->diffInMinutes($en->created_at));
                if ($minutes <= 60) {
                    $score += 40;
                    $reasons[] = "created-{$minutes}m";
                } elseif ($minutes <= 360) {
                    $score += 25;
                    $reasons[] = "created-{$minutes}m";
                } elseif ($minutes <= 720) {
                    $score += 12;
                    $reasons[] = "created-{$minutes}m";
                }
            }

            if ($score > 0) {
                $scored[] = ['post' => $en, 'score' => $score, 'reasons' => implode(',', $reasons)];
            }
        }

        if (empty($scored)) {
            return null;
        }

        usort($scored, fn ($a, $b) => $b['score'] <=> $a['score']);

        $best = $scored[0];
        $second = $scored[1] ?? null;

        if (($best['score'] ?? 0) < 60) {
            return null;
        }

        if ($second && (($best['score'] - $second['score']) <= 5)) {
            return null;
        }

        return $best['post'];
    }

    private function tokenOverlapScore(string $a, string $b): int
    {
        $stop = [
            'the', 'and', 'for', 'with', 'from', 'that', 'this', 'de', 'da', 'do', 'das', 'dos',
            'para', 'com', 'sem', 'uma', 'um', 'em', 'no', 'na', 'of', 'to', 'in', 'on',
        ];

        $ta = array_values(array_filter(preg_split('/[^a-z0-9]+/i', Str::lower($a)) ?: [], fn ($t) => strlen($t) >= 4 && !in_array($t, $stop, true)));
        $tb = array_values(array_filter(preg_split('/[^a-z0-9]+/i', Str::lower($b)) ?: [], fn ($t) => strlen($t) >= 4 && !in_array($t, $stop, true)));

        if (empty($ta) || empty($tb)) {
            return 0;
        }

        $inter = array_intersect($ta, $tb);
        $count = count(array_unique($inter));

        if ($count >= 4) return 25;
        if ($count === 3) return 18;
        if ($count === 2) return 10;
        if ($count === 1) return 4;
        return 0;
    }

    private function normalizeSlug(string $slug): string
    {
        $s = Str::slug($slug);
        $s = preg_replace('/-en$/', '', $s) ?? $s;
        $s = preg_replace('/-pt$/', '', $s) ?? $s;
        return trim($s);
    }
}
