<?php

namespace App\Support;

use Illuminate\Support\Str;

class BlogCatalog
{
    /**
     * Editorias públicas do blog.
     *
     * Observação: o banco antigo usa categories.id em UUID e categories.name.
     * O slug público PT/EN fica centralizado aqui para não depender de posts.category_slug.
     */
    private const ITEMS = [
        'liturgia' => [
            'category_id' => '388b79e9-4a3a-4f9a-968c-2737cff74cce',
            'category_name' => 'Liturgia',
            'pt_slug' => 'liturgia',
            'en_slug' => 'liturgy',
            'pt_label' => 'Liturgia',
            'en_label' => 'Liturgy',
            'pt_description' => 'Evangelho do dia, leituras da Missa e reflexões para rezar com a Igreja.',
            'en_description' => 'Daily Mass readings, Gospel reflections and Catholic prayer for everyday life.',
            'accentText' => 'text-amber-900',
            'accentBg' => 'bg-amber-50',
            'accentBorder' => 'border-amber-200',
            'accentUnderline' => 'bg-amber-500',
        ],
        'santos' => [
            'category_id' => 'ba7adc02-de35-4405-b3f3-7391947d6281',
            'category_name' => 'Santos',
            'pt_slug' => 'santos',
            'en_slug' => 'saints',
            'pt_label' => 'Santos',
            'en_label' => 'Saints',
            'pt_description' => 'Histórias, exemplos e ensinamentos dos santos para a vida cristã real.',
            'en_description' => 'Stories, examples and teachings of the saints for practical Christian life.',
            'accentText' => 'text-rose-900',
            'accentBg' => 'bg-rose-50',
            'accentBorder' => 'border-rose-200',
            'accentUnderline' => 'bg-rose-500',
        ],
        'terco' => [
            'category_id' => 'da37f657-b94e-485f-be57-468815d712bd',
            'category_name' => 'Terço',
            'pt_slug' => 'terco',
            'en_slug' => 'rosary',
            'pt_label' => 'Terço',
            'en_label' => 'Rosary',
            'pt_description' => 'Guias, mistérios e meditações para rezar o Santo Terço com constância.',
            'en_description' => 'Guides, mysteries and meditations to pray the Holy Rosary consistently.',
            'accentText' => 'text-emerald-900',
            'accentBg' => 'bg-emerald-50',
            'accentBorder' => 'border-emerald-200',
            'accentUnderline' => 'bg-emerald-500',
        ],
        'homilia' => [
            'category_id' => '7e0577dc-36c8-4f9d-b476-97af9dc1a077',
            'category_name' => 'Homilia',
            'pt_slug' => 'homilia',
            'en_slug' => 'homily',
            'pt_label' => 'Homilia',
            'en_label' => 'Homily',
            'pt_description' => 'Reflexões sobre o Evangelho, a liturgia e a caminhada cristã.',
            'en_description' => 'Reflections on the Gospel, the liturgy and the Christian journey.',
            'accentText' => 'text-indigo-900',
            'accentBg' => 'bg-indigo-50',
            'accentBorder' => 'border-indigo-200',
            'accentUnderline' => 'bg-indigo-500',
        ],
        'cotidiano' => [
            'category_id' => 'ae6cd3b7-dbc9-4df2-bf7c-c372d6b7fe5a',
            'category_name' => 'Cotidiano',
            'pt_slug' => 'cotidiano',
            'en_slug' => 'christian-life',
            'pt_label' => 'Vida Cristã',
            'en_label' => 'Christian Life',
            'pt_description' => 'Fé católica aplicada à rotina, família, trabalho, oração e decisões concretas.',
            'en_description' => 'Catholic faith applied to routine, family, work, prayer and concrete decisions.',
            'accentText' => 'text-sky-900',
            'accentBg' => 'bg-sky-50',
            'accentBorder' => 'border-sky-200',
            'accentUnderline' => 'bg-sky-500',
        ],
        'noticias' => [
            'category_id' => '81f79787-be83-420f-b214-6b243da21cda',
            'category_name' => 'Notícias',
            'pt_slug' => 'noticias',
            'en_slug' => 'news',
            'pt_label' => 'Notícias',
            'en_label' => 'News',
            'pt_description' => 'Atualizações, acontecimentos e temas católicos em linguagem simples.',
            'en_description' => 'Updates, events and Catholic topics in plain language.',
            'accentText' => 'text-slate-900',
            'accentBg' => 'bg-slate-50',
            'accentBorder' => 'border-slate-200',
            'accentUnderline' => 'bg-slate-500',
        ],
        'financas' => [
            'category_id' => '9742686a-f8c7-4664-802a-f5241de857f0',
            'category_name' => 'Cristão Católico e Finanças',
            'pt_slug' => 'cristao-catolico-e-financas',
            'en_slug' => 'catholic-christian-and-finances',
            'pt_label' => 'Cristão Católico e Finanças',
            'en_label' => 'Catholic Christian and Finances',
            'pt_description' => 'Catecismo, Doutrina Social, santos e papas aplicados a orçamento, dívidas, investimento ético e generosidade.',
            'en_description' => 'Catechism, Catholic social teaching, saints and popes applied to money, debt, ethical investing and generosity.',
            'accentText' => 'text-amber-950',
            'accentBg' => 'bg-amber-50',
            'accentBorder' => 'border-amber-200',
            'accentUnderline' => 'bg-amber-600',
            'hub_path' => '/cristao-catolico-e-financas',
            'is_hub' => true,
        ],
    ];

    public static function all(): array
    {
        return self::ITEMS;
    }

    public static function portalSections(): array
    {
        return array_filter(self::ITEMS, fn (array $item) => empty($item['is_hub']));
    }

    public static function routeSlug(array $item, string $lang = 'pt'): string
    {
        return $lang === 'en' ? $item['en_slug'] : $item['pt_slug'];
    }

    public static function label(array $item, string $lang = 'pt'): string
    {
        return $lang === 'en' ? $item['en_label'] : $item['pt_label'];
    }

    public static function description(array $item, string $lang = 'pt'): string
    {
        return $lang === 'en' ? $item['en_description'] : $item['pt_description'];
    }

    public static function findByRouteSlug(?string $slug, string $lang = 'pt'): ?array
    {
        $slug = Str::slug((string) $slug);
        if ($slug === '') {
            return null;
        }

        foreach (self::ITEMS as $key => $item) {
            if (
                $slug === $key ||
                $slug === $item['pt_slug'] ||
                $slug === $item['en_slug'] ||
                $slug === Str::slug($item['category_name'])
            ) {
                $item['key'] = $key;
                $item['route_slug'] = self::routeSlug($item, $lang);
                $item['label'] = self::label($item, $lang);
                $item['description'] = self::description($item, $lang);

                return $item;
            }
        }

        return null;
    }

    public static function decorate(array $item, string $lang = 'pt'): array
    {
        $key = $item['key'] ?? array_search($item, self::ITEMS, true) ?: Str::slug($item['category_name'] ?? 'blog');

        return array_merge(self::defaultTheme($lang), $item, [
            'key' => $key,
            'route_slug' => self::routeSlug($item, $lang),
            'label' => self::label($item, $lang),
            'description' => self::description($item, $lang),
        ]);
    }

    public static function defaultTheme(string $lang = 'pt'): array
    {
        return [
            'key' => 'blog',
            'category_id' => null,
            'category_name' => $lang === 'en' ? 'Blog' : 'Blog',
            'pt_slug' => 'blog',
            'en_slug' => 'blog',
            'route_slug' => 'blog',
            'label' => $lang === 'en' ? 'Blog' : 'Blog',
            'description' => $lang === 'en'
                ? 'Catholic articles, reflections and practical spirituality.'
                : 'Artigos católicos, reflexões e espiritualidade prática.',
            'accentText' => 'text-slate-900',
            'accentBg' => 'bg-slate-50',
            'accentBorder' => 'border-slate-200',
            'accentUnderline' => 'bg-slate-500',
        ];
    }
}
