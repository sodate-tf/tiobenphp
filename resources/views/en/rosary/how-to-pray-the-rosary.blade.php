{{-- resources/views/rosary/en/how-to-pray-the-rosary.blade.php --}}
@extends('layouts.site')

@php
  $SITE_URL = 'https://www.iatioben.com.br';
  $PAGE_PATH = '/en/rosary/how-to-pray-the-rosary';
  $CANONICAL_URL = $SITE_URL.$PAGE_PATH;

  // Adsense
  $ADS_CLIENT = 'ca-pub-8819996017476509';
  $ADS_SLOT_BODY_TOP = '7474884427';
  $ADS_SLOT_IN_ARTICLE = '6161802751';
  $ADS_SLOT_SIDEBAR_DESKTOP = '8534838745';
  $ADS_SLOT_SIDEBAR_MOBILE = '1573844576';
  $ADS_SLOT_IN_ARTICLE_2 = '5469336488';

  $OG_IMAGE = $SITE_URL.'/og/terco.png?v=1';

  $mysteryLinks = [
    ['title' => 'Joyful Mysteries',   'tipo' => 'misterios-gozosos',   'desc' => 'The childhood of Jesus contemplated with Mary.',               'day' => 'Monday & Saturday'],
    ['title' => 'Sorrowful Mysteries','tipo' => 'misterios-dolorosos', 'desc' => 'The Passion and the Cross: faith that remains.',              'day' => 'Tuesday & Friday'],
    ['title' => 'Glorious Mysteries', 'tipo' => 'misterios-gloriosos', 'desc' => 'Resurrection and hope that does not disappoint.',            'day' => 'Wednesday & Sunday'],
    ['title' => 'Luminous Mysteries', 'tipo' => 'misterios-luminosos', 'desc' => 'Jesus’ public life: light for the path.',                    'day' => 'Thursday'],
  ];

  // Se você ainda não tem /en/blog, mantenha /blog por enquanto.
  $recommendedBlogLinks = [
    ['title' => 'Rosary step-by-step (very detailed)', 'slug' => 'terco-passo-a-passo', 'desc' => 'A slow, clear guide for beginners.'],
    ['title' => 'Difference between Rosary and Chaplet', 'slug' => 'diferenca-terco-e-rosario', 'desc' => 'Simple definitions and practical usage.'],
    ['title' => 'Which mysteries to pray each day?', 'slug' => 'misterios-do-terco-por-dia-da-semana', 'desc' => 'Weekly table + spiritual explanation.'],
    ['title' => 'How to meditate (without autopilot)', 'slug' => 'como-meditar-os-misterios-do-terco', 'desc' => 'Small practices to pray with attention.'],
    ['title' => 'Rosary for beginners', 'slug' => 'terco-para-iniciantes', 'desc' => 'An easy starting point if you are returning to prayer.'],
    ['title' => 'Praying alone: is it valid? How to do it?', 'slug' => 'rezar-o-terco-sozinho', 'desc' => 'A pastoral guide for daily life.'],
  ];

  $meta = [
    'html_lang' => 'en',
    'title' => 'How to pray the Rosary (step-by-step) | IA Tio Ben',
    'description' => 'Learn how to pray the Catholic Rosary with meaning: order of prayers, mysteries, daily schedule, and FAQs. Start today.',
    'canonical' => $CANONICAL_URL,
    'robots' => 'index,follow',
    'hreflangs' => [
      'pt-BR' => $SITE_URL.'/santo-terco/como-rezar-o-terco',
      'en' => $CANONICAL_URL,
      'x-default' => $SITE_URL.'/santo-terco/como-rezar-o-terco',
    ],
    'og_title' => 'How to pray the Rosary (step-by-step)',
    'og_description' => 'A practical guide: quick sequence, step-by-step, mysteries, daily schedule, and FAQs.',
    'og_url' => $CANONICAL_URL,
    'og_image' => $OG_IMAGE,
  ];

  $date = now()->toDateString();

  $faq = [
    ['q' => 'Do I have to pray the Rosary out loud?', 'a' => 'No. You can pray out loud, silently, or mentally. What matters is attention and faith.'],
    ['q' => 'Can I pray without rosary beads?', 'a' => 'Yes. You can follow the text and keep the sequence calmly. The value is in praying with a present heart.'],
    ['q' => 'What is the difference between the Rosary and a chaplet?', 'a' => 'The Rosary is the full devotion with mysteries. The common daily form is five decades (often called a chaplet).'],
    ['q' => 'Which mysteries are prayed on each day?', 'a' => 'Mon/Sat: Joyful. Tue/Fri: Sorrowful. Wed/Sun: Glorious. Thu: Luminous.'],
    ['q' => 'Is praying online valid?', 'a' => 'Yes. Digital resources can help with consistency and attention. Keep a prayerful spirit and meditate on the mysteries.'],
    ['q' => 'Do I need to pray it all at once?', 'a' => 'No. If needed, pray in parts (one mystery at a time). Perseverance matters more than speed.'],
  ];

  $articleSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $CANONICAL_URL],
    'headline' => 'How to pray the Rosary: complete guide (with mysteries)',
    'description' => 'A practical Catholic guide to pray the Rosary with meaning: quick sequence, step-by-step, mysteries, weekly schedule, and FAQs.',
    'inLanguage' => 'en',
    'author' => ['@type' => 'Organization', 'name' => 'IA Tio Ben', 'url' => $SITE_URL],
    'publisher' => [
      '@type' => 'Organization',
      'name' => 'IA Tio Ben',
      'url' => $SITE_URL,
      'logo' => ['@type' => 'ImageObject', 'url' => $SITE_URL.'/images/tio-ben-transparente.png'],
    ],
    'datePublished' => $date,
    'dateModified' => $date,
    'image' => $SITE_URL.'/images/santo-do-dia-ia-tio-ben.png',
    'about' => [
      ['@type' => 'Thing', 'name' => 'Rosary'],
      ['@type' => 'Thing', 'name' => 'Prayer'],
      ['@type' => 'Thing', 'name' => 'Marian devotion'],
      ['@type' => 'Thing', 'name' => 'Mysteries of the Rosary'],
    ],
    'keywords' => [
      'how to pray the rosary',
      'catholic rosary',
      'rosary mysteries',
      'rosary schedule',
      'pray the rosary online',
    ],
  ];

  $breadcrumbSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
      ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $SITE_URL.'/en'],
      ['@type' => 'ListItem', 'position' => 2, 'name' => 'Holy Rosary', 'item' => $SITE_URL.'/en/santo-terco'],
      ['@type' => 'ListItem', 'position' => 3, 'name' => 'How to pray the Rosary', 'item' => $CANONICAL_URL],
    ],
  ];

  $faqSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'inLanguage' => 'en',
    'mainEntity' => array_map(function ($x) {
      return [
        '@type' => 'Question',
        'name' => $x['q'],
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $x['a']],
      ];
    }, $faq),
  ];

  $howToSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'HowTo',
    'name' => 'How to pray the Catholic Rosary (step-by-step)',
    'description' => 'A simple step-by-step method to pray the Rosary calmly while meditating on the mysteries of Jesus’ life.',
    'inLanguage' => 'en',
    'estimatedCost' => ['@type' => 'MonetaryAmount', 'currency' => 'USD', 'value' => '0'],
    'supply' => [['@type' => 'HowToSupply', 'name' => 'Rosary beads (optional)']],
    'tool' => [['@type' => 'HowToTool', 'name' => 'Text guide (optional)']],
    'step' => [
      ['@type' => 'HowToStep', 'position' => 1, 'name' => 'Sign of the Cross', 'text' => 'Begin with the Sign of the Cross and offer your intention.'],
      ['@type' => 'HowToStep', 'position' => 2, 'name' => 'Apostles’ Creed', 'text' => 'Pray the Creed, professing your faith.'],
      ['@type' => 'HowToStep', 'position' => 3, 'name' => 'Our Father', 'text' => 'Pray one Our Father.'],
      ['@type' => 'HowToStep', 'position' => 4, 'name' => 'Three Hail Marys', 'text' => 'Pray three Hail Marys (for faith, hope, and charity).'],
      ['@type' => 'HowToStep', 'position' => 5, 'name' => 'Glory Be', 'text' => 'Pray the Glory Be.'],
      ['@type' => 'HowToStep', 'position' => 6, 'name' => 'Announce the mystery', 'text' => 'Announce the first mystery and pause briefly to meditate.'],
      ['@type' => 'HowToStep', 'position' => 7, 'name' => 'Our Father (decade)', 'text' => 'Pray one Our Father before the decade.'],
      ['@type' => 'HowToStep', 'position' => 8, 'name' => 'Ten Hail Marys', 'text' => 'Pray ten Hail Marys while contemplating the mystery.'],
      ['@type' => 'HowToStep', 'position' => 9, 'name' => 'Glory Be', 'text' => 'Pray the Glory Be at the end of the decade.'],
      ['@type' => 'HowToStep', 'position' => 10, 'name' => 'Repeat for five mysteries', 'text' => 'Repeat until you complete the five mysteries of the day.'],
      ['@type' => 'HowToStep', 'position' => 11, 'name' => 'Hail Holy Queen + closing', 'text' => 'Finish with the Hail Holy Queen and a closing prayer.'],
    ],
  ];

  $itemListSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'ItemList',
    'name' => 'Main navigation: how to pray the Rosary and mysteries',
    'itemListElement' => array_merge(
      [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Quick answer', 'url' => $CANONICAL_URL.'#quick-answer'],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Step-by-step', 'url' => $CANONICAL_URL.'#how-to'],
        ['@type' => 'ListItem', 'position' => 3, 'name' => 'Mysteries', 'url' => $CANONICAL_URL.'#mysteries'],
        ['@type' => 'ListItem', 'position' => 4, 'name' => 'Weekly schedule', 'url' => $CANONICAL_URL.'#schedule'],
        ['@type' => 'ListItem', 'position' => 5, 'name' => 'FAQs', 'url' => $CANONICAL_URL.'#faq'],
      ],
      array_map(function ($m, $i) use ($SITE_URL) {
        return [
          '@type' => 'ListItem',
          'position' => 6 + $i,
          'name' => $m['title'],
          'url' => $SITE_URL.'/en/santo-terco/'.$m['tipo'],
        ];
      }, $mysteryLinks, array_keys($mysteryLinks))
    ),
  ];

  $webPageSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    '@id' => $CANONICAL_URL,
    'url' => $CANONICAL_URL,
    'name' => 'How to pray the Rosary: complete guide (with mysteries)',
    'inLanguage' => 'en',
    'isPartOf' => ['@type' => 'WebSite', 'name' => 'IA Tio Ben', 'url' => $SITE_URL],
    'primaryImageOfPage' => ['@type' => 'ImageObject', 'url' => $SITE_URL.'/images/santo-do-dia-ia-tio-ben.png'],
    'mainEntity' => [$howToSchema, $faqSchema],
  ];

  $jsonFlags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;

  // ===== Params do Aside (mesmo padrão do PT) =====
  $asideBlogLinks = array_map(function ($x) {
    return [
      'href'  => url('/blog/'.$x['slug']),
      'title' => $x['title'],
      'desc'  => $x['desc'],
    ];
  }, $recommendedBlogLinks);

  // Página "how to" não é um set -> fixa um padrão pra visual
  $asideSetKey = 'luminosos';
@endphp

@section('html_lang', $meta['html_lang'])
@section('title', $meta['title'])
@section('meta_description', $meta['description'])
@section('canonical', $meta['canonical'])
@section('robots', $meta['robots'])

@section('hreflang')
  @foreach($meta['hreflangs'] as $lang => $url)
    <link rel="alternate" hreflang="{{ $lang }}" href="{{ $url }}"/>
  @endforeach
@endsection

@section('og_title', $meta['og_title'])
@section('og_description', $meta['og_description'])
@section('og_url', $meta['og_url'])
@section('og_image', $meta['og_image'])

@push('head')
  <style>
    /* ===== Layout grid (fallback sem Tailwind lg:) ===== */
    .rosary-grid { display: block; }
    .rosary-aside-desktop { display: none; }
    .rosary-aside-mobile { display: block; }

    @media (min-width: 1024px) {
      .rosary-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 320px;
        gap: 24px;
        align-items: start;
      }
      .rosary-aside-desktop { display: block; }
      .rosary-aside-mobile { display: none; }
    }
  </style>
@endpush

@section('content')
  <script
    async
    src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ $ADS_CLIENT }}"
    crossorigin="anonymous"></script>

  <script type="application/ld+json">{!! json_encode($webPageSchema, $jsonFlags) !!}</script>
  <script type="application/ld+json">{!! json_encode($articleSchema, $jsonFlags) !!}</script>
  <script type="application/ld+json">{!! json_encode($breadcrumbSchema, $jsonFlags) !!}</script>
  <script type="application/ld+json">{!! json_encode($faqSchema, $jsonFlags) !!}</script>
  <script type="application/ld+json">{!! json_encode($howToSchema, $jsonFlags) !!}</script>
  <script type="application/ld+json">{!! json_encode($itemListSchema, $jsonFlags) !!}</script>

  <article class="post-santo mx-auto w-full max-w-6xl px-3 pb-16 pt-6 sm:px-6 lg:px-8 text-foreground">
    <header class="space-y-4 mb-6">
      <div class="inline-flex items-center gap-2 rounded-full border border-border bg-muted px-3 py-1 text-xs font-semibold text-foreground">
        IA Tio Ben • Holy Rosary Hub
      </div>

      <h1 class="font-reading text-3xl font-extrabold tracking-tight sm:text-4xl">
        How to pray the Rosary: a complete guide to pray with meaning
      </h1>

      <p class="font-reading text-base leading-relaxed text-muted-foreground sm:text-lg">
        If you want a clear explanation and a practical way to start today, this guide is for you:
        quick sequence, step-by-step, mysteries, weekly schedule, and common questions — in one place.
      </p>

      <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        <a href="{{ url('/en/santo-terco') }}"
          class="group inline-flex items-center justify-center gap-2 rounded-xl bg-amber-600 px-4 py-3 text-sm font-semibold text-white shadow-sm ring-1 ring-amber-700/20 transition hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500">
          Pray now (guided)
          <span class="text-white/90 transition group-hover:translate-x-0.5">→</span>
        </a>

        <a href="#quick-answer"
          class="inline-flex items-center justify-center gap-2 rounded-xl border border-border bg-card px-4 py-3 text-sm font-semibold shadow-sm transition hover:bg-muted focus:outline-none focus:ring-2 focus:ring-amber-500">
          Quick answer (60s)
        </a>

        <a href="#mysteries"
          class="inline-flex items-center justify-center gap-2 rounded-xl border border-border bg-card px-4 py-3 text-sm font-semibold shadow-sm transition hover:bg-muted focus:outline-none focus:ring-2 focus:ring-amber-500 sm:col-span-2 lg:col-span-1">
          See the mysteries
        </a>
      </div>
    </header>

    {{-- GRID: main + aside (mesmo padrão do PT e do page.blade) --}}
    <div class="rosary-grid gap-6 items-start">

      {{-- MAIN --}}
      <section class="min-w-0 rounded-2xl border border-border bg-card p-4 shadow-sm sm:p-6 lg:p-8">
        {{-- Top ad --}}
        <div class="rounded-2xl border border-border bg-card p-3 shadow-sm mb-6">
          <p class="px-1 pb-2 text-[11px] font-semibold text-muted-foreground">Advertisement</p>
          <div class="flex justify-center">
            <ins class="adsbygoogle"
              style="display:block; min-height:140px"
              data-ad-client="{{ $ADS_CLIENT }}"
              data-ad-slot="{{ $ADS_SLOT_BODY_TOP }}"
              data-ad-format="auto"
              data-full-width-responsive="true"></ins>
          </div>
          <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
        </div>

        {{-- Quick answer --}}
        <section id="quick-answer" class="rounded-2xl border border-amber-200 bg-amber-50 p-4 sm:p-5 mb-6" aria-label="Quick answer">
          <h2 class="text-base font-semibold text-foreground"><strong>Quick answer: how to pray the Rosary</strong></h2>
          <p class="mt-1 text-sm text-muted-foreground">If you just want the sequence, follow this:</p>

          <ol class="mt-3 list-decimal space-y-1 pl-5 text-sm text-foreground">
            <li>Sign of the Cross</li>
            <li>Apostles’ Creed</li>
            <li>Our Father</li>
            <li>3 Hail Marys</li>
            <li>Glory Be</li>
            <li>Announce 1 mystery + brief meditation (10–20s)</li>
            <li>Our Father + 10 Hail Marys + Glory Be (1 decade)</li>
            <li>Repeat until 5 mysteries are completed</li>
            <li>Hail Holy Queen + closing prayer</li>
          </ol>

          <div class="mt-4 grid gap-3 sm:grid-cols-2">
            <a href="{{ url('/en/santo-terco') }}"
              class="inline-flex items-center justify-center rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500">
              Pray now (guided)
            </a>
            <a href="#how-to"
              class="inline-flex items-center justify-center rounded-xl border border-amber-200 bg-card px-4 py-2.5 text-sm font-semibold text-amber-900 shadow-sm transition hover:bg-amber-100 focus:outline-none focus:ring-2 focus:ring-amber-500">
              Full step-by-step
            </a>
          </div>
        </section>

        <div class="prose prose-amber max-w-none font-reading leading-relaxed
                    prose-p:my-4 prose-li:my-1
                    prose-h2:mt-10 prose-h2:mb-4 prose-h2:font-extrabold prose-h2:text-2xl prose-h2:text-foreground
                    prose-h3:mt-8 prose-h3:mb-3 prose-h3:font-bold prose-h3:text-xl prose-h3:text-foreground">

          <h2 id="what-is"><strong>What is the Catholic Rosary?</strong></h2>
          <p>
            The Rosary is a Marian prayer that leads us to Jesus. While praying the Our Father, Hail Mary, and Glory Be,
            we meditate on the <strong>mysteries of Christ’s life</strong>.
          </p>

          <h2><strong>Why pray the Rosary?</strong></h2>
          <ul>
            <li>To quiet the heart and refocus on God</li>
            <li>To meditate on the Gospel with calm</li>
            <li>To entrust intentions for yourself and your family</li>
            <li>To grow in perseverance</li>
            <li>To find peace amid daily battles</li>
          </ul>

          <h2><strong>Rosary vs. chaplet: what’s the difference?</strong></h2>
          <p>
            The <strong>Rosary</strong> refers to the devotion with mysteries. The most common daily form is
            <strong> five decades</strong> (often called a chaplet in some places).
          </p>

          <h2 id="how-to"><strong>How to pray the Rosary (step-by-step)</strong></h2>
          <p>
            The Rosary is not a race. It’s a path. If possible, start with a simple intention (someone you love, gratitude, a need).
          </p>

          <h3><strong>Quick checklist (so you don’t get lost)</strong></h3>
          <ol>
            <li>Sign of the Cross</li>
            <li>Apostles’ Creed</li>
            <li>Our Father</li>
            <li>Three Hail Marys</li>
            <li>Glory Be</li>
            <li>Announce the 1st mystery + meditate</li>
            <li>Our Father</li>
            <li>Ten Hail Marys</li>
            <li>Glory Be</li>
            <li>Repeat until five mysteries</li>
            <li>Hail Holy Queen + closing prayer</li>
          </ol>

          {{-- In-article ad --}}
          <div class="not-prose rounded-2xl border border-border bg-card p-3 shadow-sm mb-6">
            <p class="px-1 pb-2 text-[11px] font-semibold text-muted-foreground">Advertisement</p>
            <div class="flex justify-center">
              <ins class="adsbygoogle"
                style="display:block; min-height:160px"
                data-ad-client="{{ $ADS_CLIENT }}"
                data-ad-slot="{{ $ADS_SLOT_IN_ARTICLE }}"
                data-ad-format="auto"
                data-full-width-responsive="true"></ins>
            </div>
            <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
          </div>

          <h2 id="mysteries"><strong>Mysteries of the Rosary</strong></h2>
          <p>Choose the mysteries for the day and pray calmly.</p>
        </div>

        <div class="mt-6 grid gap-3 sm:grid-cols-2 mb-6">
          @foreach($mysteryLinks as $m)
            <a href="{{ url('/en/santo-terco/'.$m['tipo']) }}"
              class="rounded-2xl border border-amber-200 bg-card p-4 shadow-sm transition hover:bg-amber-50 focus:outline-none focus:ring-2 focus:ring-amber-500">
              <div class="flex items-start justify-between gap-3">
                <div>
                  <p class="text-sm font-semibold text-foreground">{{ $m['title'] }}</p>
                  <p class="mt-1 text-sm text-muted-foreground">{{ $m['desc'] }}</p>
                </div>
                <span class="shrink-0 rounded-full border border-amber-200 bg-amber-50 px-2 py-1 text-xs font-semibold text-amber-900">
                  {{ $m['day'] }}
                </span>
              </div>
              <p class="mt-3 text-xs font-semibold text-amber-900">Open page →</p>
            </a>
          @endforeach
        </div>

        {{-- Mobile ad (quando sidebar some) --}}
        <div class="lg:hidden rounded-2xl border border-border bg-card p-3 shadow-sm mb-6">
          <p class="px-1 pb-2 text-[11px] font-semibold text-muted-foreground">Advertisement</p>
          <div class="flex justify-center">
            <ins class="adsbygoogle"
              style="display:block; min-height:100px; width:100%"
              data-ad-client="{{ $ADS_CLIENT }}"
              data-ad-slot="{{ $ADS_SLOT_SIDEBAR_MOBILE }}"
              data-ad-format="auto"
              data-full-width-responsive="true"></ins>
          </div>
          <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
        </div>

        <div class="prose prose-amber max-w-none font-reading leading-relaxed mb-4
                    prose-h2:font-extrabold prose-h2:text-2xl prose-h2:text-foreground">
          <h2 id="schedule"><strong>Which mysteries to pray each day?</strong></h2>
        </div>

        <div class="overflow-hidden rounded-2xl border border-amber-200 bg-card mb-6">
          <div class="grid grid-cols-1 divide-y divide-amber-100 sm:grid-cols-2 sm:divide-x sm:divide-y-0">
            @php
              $days = [
                ['Monday', 'Joyful Mysteries'],
                ['Tuesday', 'Sorrowful Mysteries'],
                ['Wednesday', 'Glorious Mysteries'],
                ['Thursday', 'Luminous Mysteries'],
                ['Friday', 'Sorrowful Mysteries'],
                ['Saturday', 'Joyful Mysteries'],
                ['Sunday', 'Glorious Mysteries'],
              ];
            @endphp
            @foreach($days as $idx => $row)
              <div class="p-4 {{ $idx === 6 ? 'sm:col-span-2' : '' }}">
                <p class="text-sm font-semibold text-foreground">{{ $row[0] }}</p>
                <p class="text-sm text-muted-foreground">{{ $row[1] }}</p>
              </div>
            @endforeach
          </div>
        </div>

        <div class="prose prose-amber max-w-none font-reading leading-relaxed mb-4
                    prose-h2:font-extrabold prose-h2:text-2xl prose-h2:text-foreground">
          <h2 id="faq"><strong>FAQs</strong></h2>
        </div>

        {{-- Ad before FAQ --}}
        <div class="rounded-2xl border border-border bg-card p-3 shadow-sm mb-6">
          <p class="px-1 pb-2 text-[11px] font-semibold text-muted-foreground">Advertisement</p>
          <div class="flex justify-center">
            <ins class="adsbygoogle"
              style="display:block; min-height:140px"
              data-ad-client="{{ $ADS_CLIENT }}"
              data-ad-slot="{{ $ADS_SLOT_IN_ARTICLE_2 }}"
              data-ad-format="auto"
              data-full-width-responsive="true"></ins>
          </div>
          <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
        </div>

        <div class="space-y-3 mb-6">
          @foreach([
            ['Do I have to pray out loud?', 'No. You may pray out loud or silently. Attention and faith matter most.'],
            ['Can I pray without beads?', 'Yes. Follow the text and keep the sequence calmly.'],
            ['Rosary vs. chaplet?', 'The daily form is five decades, meditating on the mysteries.'],
            ['Do I need to pray it all at once?', 'No. Pray in parts if needed. Perseverance matters.'],
            ['Is praying online valid?', 'Yes. Digital resources can help with consistency and attention.'],
          ] as $qa)
            <details class="rounded-xl border border-amber-200 bg-card p-4">
              <summary class="cursor-pointer text-sm font-semibold text-foreground">{{ $qa[0] }}</summary>
              <p class="mt-3 text-sm text-muted-foreground">{{ $qa[1] }}</p>
            </details>
          @endforeach
        </div>

        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 sm:p-6">
          <div class="space-y-3">
            <p class="text-base font-semibold text-amber-900"><strong>Ready to take one step today?</strong></p>
            <p class="text-sm leading-relaxed text-amber-900/90">
              If you feel tired or anxious, start with just one mystery. Slowly. God is already listening.
            </p>
            <div class="grid gap-3 sm:grid-cols-2">
              <a href="{{ url('/en/santo-terco') }}"
                class="inline-flex items-center justify-center rounded-xl bg-amber-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500">
                Pray now (guided)
              </a>
              <a href="{{ url('/blog/rezar-o-terco-sozinho') }}"
                class="inline-flex items-center justify-center rounded-xl border border-amber-200 bg-card px-4 py-3 text-sm font-semibold text-amber-900 shadow-sm transition hover:bg-amber-100 focus:outline-none focus:ring-2 focus:ring-amber-500">
                Praying alone (blog)
              </a>
            </div>
          </div>
        </div>
      </section>

      {{-- MOBILE: Aside colapsável (mesmo padrão do terço) --}}
      <div class="rosary-aside-mobile mt-6 lg:hidden">
        <div class="rounded-2xl bg-white/90 p-4 shadow-sm ring-1 ring-slate-200/60">
          <div class="flex items-center justify-between">
            <p class="text-xs font-semibold tracking-wide text-amber-700 uppercase">Quick access</p>
            <button type="button" id="rosary-aside-toggle"
              class="text-xs font-semibold text-slate-600 hover:text-slate-900">
              Show
            </button>
          </div>

          <div id="rosary-aside-mobile-panel" class="mt-3 hidden">
            @include('terco.partials.aside', [
              'lang' => 'en',
              'variant' => 'mobile',
              'setKey' => $asideSetKey,
              'adsSlotDesktop300x250' => $ADS_SLOT_SIDEBAR_DESKTOP,
              'adsSlotMobile' => $ADS_SLOT_SIDEBAR_MOBILE,
              'blogLinks' => $asideBlogLinks,
            ])
          </div>
        </div>
      </div>

      {{-- ASIDE Desktop (fixo à direita) --}}
      <aside class="rosary-aside-desktop hidden lg:block min-w-0">
        <div class="sticky top-20">
          @include('terco.partials.aside', [
            'lang' => 'en',
            'variant' => 'desktop',
            'setKey' => $asideSetKey,
            'adsSlotDesktop300x250' => $ADS_SLOT_SIDEBAR_DESKTOP,
            'adsSlotMobile' => $ADS_SLOT_SIDEBAR_MOBILE,
            'blogLinks' => $asideBlogLinks,
          ])
        </div>
      </aside>

    </div>
  </article>
@endsection

@push('scripts')
<script>
(function(){
  const toggle = document.getElementById('rosary-aside-toggle');
  const panel = document.getElementById('rosary-aside-mobile-panel');
  if(!toggle || !panel) return;

  toggle.addEventListener('click', ()=>{
    const open = !panel.classList.contains('hidden');
    panel.classList.toggle('hidden', open);
    toggle.textContent = open ? 'Show' : 'Hide';
  });
})();
</script>
@endpush
