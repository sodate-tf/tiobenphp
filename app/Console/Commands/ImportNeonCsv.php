<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportNeonCsv extends Command
{
    protected $signature = 'import:neon-csv {path : Pasta onde estão os CSVs}';
    protected $description = 'Importa ai_settings, categories, posts, articles (CSV do Neon/Postgres) para MySQL';

    public function handle(): int
    {
        $dir = rtrim($this->argument('path'), DIRECTORY_SEPARATOR);

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $this->importAiSettings("$dir/ai_settings.csv");
        $this->importCategories("$dir/categories.csv");
        $this->importPosts("$dir/posts.csv");
        $this->importArticles("$dir/articles.csv");

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->info('Import finalizado.');
        return self::SUCCESS;
    }

    private function csvRows(string $file): \Generator
    {
        if (!file_exists($file)) {
            $this->warn("Arquivo não encontrado: $file");
            return;
        }

        $fh = fopen($file, 'r');
        if (!$fh) return;

        $header = fgetcsv($fh);
        if (!$header) return;

        while (($row = fgetcsv($fh)) !== false) {
            $assoc = [];
            foreach ($header as $i => $col) {
                $assoc[$col] = $row[$i] ?? null;
            }
            yield $assoc;
        }
        fclose($fh);
    }

    private function dt(?string $v): ?string
    {
        if (!$v) return null;

        $s = trim($v);

        // Normaliza ISO: troca T por espaço e remove Z
        $s = str_replace('T', ' ', $s);
        $s = str_replace('Z', '', $s);

        // Remove timezone no formato +00 / +00:00 / -03 etc
        $s = preg_replace('/([+-]\d{2})(:\d{2})?$/', '', $s);

        // Trunca microssegundos para 3 casas (datetime(3))
        // Ex: 2025-10-31 14:19:34.036296 -> 2025-10-31 14:19:34.036
        $s = preg_replace('/\.(\d{3})\d+/', '.$1', $s);

        // Se sobrar ponto sem milissegundos válidos, remove
        $s = preg_replace('/\.\s*$/', '', $s);

        return trim($s);
    }

    private function pgBoolToInt($v, int $default = 0): int
    {
        if ($v === null || $v === '') return $default;
        $v = strtolower(trim((string)$v));
        return in_array($v, ['1','t','true','yes','y'], true) ? 1 : 0;
    }

    private function pgTextArrayToJson(?string $pgArray): ?string
    {
        // Ex: "{a,b,c}" -> ["a","b","c"]
        if ($pgArray === null || $pgArray === '') return null;

        $s = trim($pgArray);

        if ($s === '{}') {
            return json_encode([], JSON_UNESCAPED_UNICODE);
        }

        if ($s[0] === '{' && substr($s, -1) === '}') {
            $inner = substr($s, 1, -1);
            if ($inner === '') return json_encode([], JSON_UNESCAPED_UNICODE);

            $parts = array_map('trim', explode(',', $inner));
            $parts = array_map(function ($v) {
                $v = trim($v, "\"");
                return $v === 'NULL' ? null : $v;
            }, $parts);

            $parts = array_values(array_filter($parts, fn($x) => $x !== null && $x !== ''));
            return json_encode($parts, JSON_UNESCAPED_UNICODE);
        }

        // se já vier JSON
        if ($s[0] === '[') return $s;

        return json_encode([$s], JSON_UNESCAPED_UNICODE);
    }

    private function importAiSettings(string $file): void
    {
        foreach ($this->csvRows($file) as $r) {
            DB::table('ai_settings')->updateOrInsert(
                ['id' => $r['id']],
                [
                    'agent_name' => $r['agent_name'] ?? 'Agente Tio Ben',
                    'ai_model' => $r['ai_model'] ?? 'gemini-1.5-flash',
                    'calendar_id' => $r['calendar_id'] ?: null,
                    'focus_keywords' => $r['focus_keywords'] ?: null,
                    'remote_post_url' => $r['remote_post_url'] ?: null,
                    'remote_post_api_key' => $r['remote_post_api_key'] ?: null,
                    'json_format_template' => $r['json_format_template'] ?: null,
                    'writer_instructions' => $r['writer_instructions'] ?: null,
                    'formatter_instructions' => $r['formatter_instructions'] ?: null,
                    'seo_instructions' => $r['seo_instructions'] ?: null,
                    'writer_files' => $r['writer_files'] ?: null,
                    'formatter_files' => $r['formatter_files'] ?: null,
                    'seo_files' => $r['seo_files'] ?: null,
                    'created_at' => $this->dt($r['created_at'] ?? null) ?? now(),
                    'updated_at' => $this->dt($r['updated_at'] ?? null) ?? now(),
                ]
            );
        }
        $this->info('ai_settings OK');
    }

    private function importCategories(string $file): void
    {
        foreach ($this->csvRows($file) as $r) {
            DB::table('categories')->updateOrInsert(
                ['id' => $r['id']],
                [
                    'name' => $r['name'],
                    'created_at' => $this->dt($r['created_at'] ?? null) ?? now(),
                    'updated_at' => $this->dt($r['updated_at'] ?? null) ?? now(),
                ]
            );
        }
        $this->info('categories OK');
    }

    private function importPosts(string $file): void
    {
        foreach ($this->csvRows($file) as $r) {
            DB::table('posts')->updateOrInsert(
                ['id' => $r['id']],
                [
                    'title' => $r['title'],
                    'slug' => $r['slug'],
                    'keywords' => $r['keywords'] ?: null,
                    'meta_description' => $r['meta_description'] ?: null,
                    'cover_image_url' => $r['cover_image_url'] ?: null,
                    'content' => $r['content'],
                    'category_id' => $r['category_id'] ?: null,
                    'is_active' => $this->pgBoolToInt($r['is_active'] ?? null, 1),
                    'publish_date' => $this->dt($r['publish_date']),
                    'expiry_date' => $this->dt($r['expiry_date'] ?? null),
                    'created_at' => $this->dt($r['created_at'] ?? null) ?? now(),
                    'updated_at' => $this->dt($r['updated_at'] ?? null) ?? now(),
                ]
            );
        }
        $this->info('posts OK');
    }

    private function importArticles(string $file): void
    {
        foreach ($this->csvRows($file) as $r) {
            DB::table('articles')->updateOrInsert(
                ['id' => (int)$r['id']],
                [
                    'generation_date' => $this->dt($r['generation_date']),
                    'title' => $r['title'],
                    'raw_content' => $r['raw_content'],
                    'formatted_content' => $r['formatted_content'],
                    'published' => $this->pgBoolToInt($r['published'] ?? null, 0),
                    'keywords' => $this->pgTextArrayToJson($r['keywords'] ?? null),
                    'meta_description' => $r['meta_description'] ?: null,
                ]
            );
        }
        $this->info('articles OK');
    }
}
