{{-- resources/views/terco/en/luminous-mysteries.blade.php --}}
@extends('layouts.site')

@php
  $SITE_URL = 'https://www.iatioben.com.br';
  $PAGE_PATH = '/en/rosary/luminous-mysteries';
  $CANONICAL_URL = $SITE_URL.$PAGE_PATH;

  $ADS_CLIENT = 'ca-pub-8819996017476509';
  $ADS_SLOT_BODY_TOP = '7474884427';
  $ADS_SLOT_IN_ARTICLE = '6161802751';
  $ADS_SLOT_SIDEBAR_DESKTOP = '8534838745';
  $ADS_SLOT_SIDEBAR_MOBILE = '1573844576';

  $today = now()->toDateString();

  // IMPORTANTE: o aside espera setKey/currentSetKey e usa isso para destacar o ativo
  $setKey = 'luminosos';

  $mysteries = [
    [
      'id' => 'baptism',
      'title' => '1st Luminous Mystery',
      'subtitle' => 'The Baptism of Jesus in the Jordan',
      'passageRef' => 'Mt 3:13–17',
      'passageQuote' => '“This is my beloved Son, with whom I am well pleased.”',
      'reflection' => [
        'At the Jordan, Jesus enters the waters not out of personal need, but to unite Himself to us. He sanctifies the path and reveals our core identity: beloved sonship in the Father.',
        'This mystery heals a deep root of anxiety: living to “prove our worth.” In God, identity comes before performance.',
        'Baptism is both belonging and mission: to be a child, and therefore to live as one. Grace is not only comfort; it is transformation.',
        'Praying here is asking the heart to return to the essential: “I am loved” — and, from that place, to choose the good with steadiness.',
      ],
      'points' => [
        'Question: where do I draw my identity from today?',
        'Fruit: fidelity to baptism and life as God’s child.',
      ],
    ],
    [
      'id' => 'cana',
      'title' => '2nd Luminous Mystery',
      'subtitle' => 'The Wedding at Cana',
      'passageRef' => 'Jn 2:1–11',
      'passageQuote' => '“Do whatever he tells you.”',
      'reflection' => [
        'Cana shows that God cares about daily life: concrete joy, what is missing at the table, quiet crises.',
        'Mary notices the need before everyone else; her sentence is a whole spiritual path: do what Jesus says — even when we do not understand everything.',
        'Jesus’ first sign turns water into wine: God does not merely “patch” things; He elevates them. Grace is not the minimum; it is ordered abundance.',
        'To pray Cana is to entrust our lacks to God: when the “wine” has run out in some area, ask for the Lord’s intervention and the docility to obey.',
      ],
      'points' => [
        'Question: where has the “wine” run out in my life?',
        'Fruit: trust and obedience.',
      ],
    ],
    [
      'id' => 'kingdom',
      'title' => '3rd Luminous Mystery',
      'subtitle' => 'The Proclamation of the Kingdom and the Call to Conversion',
      'passageRef' => 'Mk 1:14–15',
      'passageQuote' => '“Repent, and believe in the Gospel.”',
      'reflection' => [
        'Here the Gospel is direct: God is near. Conversion is not only “stopping wrong”; it is changing direction, reordering the heart, and choosing truth.',
        'The Kingdom is not a slogan; it is God’s presence transforming relationships, choices, priorities, and habits.',
        'To believe the Gospel is to trust that good is possible — and that grace is stronger than repeated falls.',
        'To pray this mystery is to make a simple examination: what needs to change today, concretely? Small sustained conversions matter more than big promises.',
      ],
      'points' => [
        'Question: what concrete conversion is God asking of me today?',
        'Fruit: conversion and love of truth.',
      ],
    ],
    [
      'id' => 'transfiguration',
      'title' => '4th Luminous Mystery',
      'subtitle' => 'The Transfiguration of the Lord',
      'passageRef' => 'Lk 9:28–36',
      'passageQuote' => '“This is my Son, my Chosen; listen to him!”',
      'reflection' => [
        'The Transfiguration is light given before the cross. God strengthens the disciples so they do not give up when night comes.',
        'Christ reveals His glory, but also teaches: we do not build our home on spiritual experiences. We must come down the mountain and live fidelity in the valley.',
        'Listening to Jesus is more than hearing; it is taking His word as criterion. True prayer reorients us.',
        'To pray this mystery is to ask for light to pass through the night: the memory of God’s presence when faith feels dim.',
      ],
      'points' => [
        'Question: which word of Jesus do I need to obey more decisively?',
        'Fruit: desire for holiness and attentive listening.',
      ],
    ],
    [
      'id' => 'eucharist',
      'title' => '5th Luminous Mystery',
      'subtitle' => 'The Institution of the Eucharist',
      'passageRef' => 'Lk 22:14–20 (cf. 1 Cor 11:23–26)',
      'passageQuote' => '“This is my body, which is given for you.”',
      'reflection' => [
        'The Eucharist is the heart of Christianity: God gives Himself as food. It is not metaphor — it is real presence and communion.',
        'By instituting it, Jesus teaches the style of love: to be broken bread, a life poured out, concrete service.',
        'This mystery also heals individualism: faith becomes body, community, adoration, and mission.',
        'Praying here is desiring to live eucharistically: receive, give thanks, adore, and give oneself. Ask that each Communion forms in us a heart more like Christ’s.',
      ],
      'points' => [
        'Question: has my life been “bread shared” or only self-preservation?',
        'Fruit: love for the Eucharist and charity.',
      ],
    ],
  ];

  $splitPoint = function (?string $raw): string {
    if (!$raw) return '';
    $pos = mb_strpos($raw, ':');
    if ($pos === false) return trim($raw);
    return trim(mb_substr($raw, $pos + 1));
  };

  $faq = [
    ['q' => 'On which day are the Luminous Mysteries prayed?', 'a' => 'Traditionally on Thursdays.'],
    ['q' => 'What are the five Luminous Mysteries?', 'a' => 'Baptism in the Jordan, Wedding at Cana, Proclamation of the Kingdom, Transfiguration, and Institution of the Eucharist.'],
    ['q' => 'How do I meditate on the Luminous Mysteries?', 'a' => 'Announce the mystery, keep a brief silence, and pray each Hail Mary contemplating Christ’s light in public life: identity, obedience, conversion, listening, and communion.'],
    ['q' => 'Can I pray them on another day?', 'a' => 'Yes. The weekday assignment is a devotional tradition.'],
  ];

  $articleSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $CANONICAL_URL],
    'headline' => 'Luminous Mysteries of the Rosary: Bible passages and reflections',
    'description' => 'Complete guide to the Luminous Mysteries with Scripture references, reflections, and practical guidance to pray with meaning.',
    'inLanguage' => 'en',
    'author' => ['@type' => 'Organization', 'name' => 'IA Tio Ben', 'url' => $SITE_URL],
    'publisher' => [
      '@type' => 'Organization',
      'name' => 'IA Tio Ben',
      'url' => $SITE_URL,
      'logo' => ['@type' => 'ImageObject', 'url' => $SITE_URL.'/images/tio-ben-transparente.webp'],
    ],
    'datePublished' => $today,
    'dateModified' => $today,
    'image' => $SITE_URL.'/images/santo-do-dia-ia-tio-ben.png',
    'about' => [
      ['@type' => 'Thing', 'name' => 'Luminous Mysteries'],
      ['@type' => 'Thing', 'name' => 'Public life of Jesus'],
      ['@type' => 'Thing', 'name' => 'Rosary'],
    ],
    'keywords' => ['luminous mysteries', 'thursday rosary', 'mysteries of light', 'how to meditate the rosary', 'eucharist'],
  ];

  $breadcrumbSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
      ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $SITE_URL.'/en'],
      ['@type' => 'ListItem', 'position' => 2, 'name' => 'Rosary', 'item' => $SITE_URL.'/en/rosary'],
      ['@type' => 'ListItem', 'position' => 3, 'name' => 'Luminous Mysteries', 'item' => $CANONICAL_URL],
    ],
  ];

  $faqSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'inLanguage' => 'en',
    'mainEntity' => array_map(fn($x) => [
      '@type' => 'Question',
      'name' => $x['q'],
      'acceptedAnswer' => ['@type' => 'Answer', 'text' => $x['a']],
    ], $faq),
  ];

  $itemListSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'ItemList',
    'name' => 'Luminous Mysteries of the Rosary',
    'itemListElement' => array_map(function($m, $idx) use ($CANONICAL_URL) {
      return [
        '@type' => 'ListItem',
        'position' => $idx + 1,
        'name' => $m['title'].' — '.$m['subtitle'],
        'url' => $CANONICAL_URL.'#'.$m['id'],
        'description' => $m['passageRef'].'.',
      ];
    }, $mysteries, array_keys($mysteries)),
  ];

  $webPageSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    '@id' => $CANONICAL_URL,
    'url' => $CANONICAL_URL,
    'name' => 'Luminous Mysteries of the Rosary: Bible passages and reflections',
    'inLanguage' => 'en',
    'isPartOf' => ['@type' => 'WebSite', 'name' => 'IA Tio Ben', 'url' => $SITE_URL],
    'mainEntity' => [$itemListSchema, $faqSchema],
  ];

  $readingProse =
    'prose prose-amber max-w-none '.
    'prose-p:leading-[1.9] prose-li:leading-[1.85] '.
    'prose-p:my-4 prose-li:my-2 '.
    'prose-h2:mt-10 prose-h2:mb-4 prose-h3:mt-8 prose-h3:mb-3 '.
    'text-[17px] sm:text-[18px] lg:text-[18.5px] '.
    'text-foreground break-words hyphens-auto';

  $blogLinks = [
    ['title' => 'Beloved sonship: healing anxiety (Baptism)', 'slug' => 'beloved-sonship-healing-anxiety'],
    ['title' => 'Cana: when the wine runs out—what then?', 'slug' => 'when-the-wine-runs-out-cana'],
    ['title' => 'Concrete conversion: changing little by little', 'slug' => 'concrete-conversion-little-by-little'],
    ['title' => 'Transfiguration: light to pass through the night', 'slug' => 'transfiguration-light-in-the-night'],
    ['title' => 'Eucharist: why it’s the heart of faith', 'slug' => 'eucharist-heart-of-faith'],
  ];

  // formato aceito pelo aside: {href, title, desc}
  $blogLinksAside = array_map(function($x) {
    return [
      'href' => url('/en/blog/'.$x['slug']),
      'title' => $x['title'],
      'desc' => null,
    ];
  }, $blogLinks);
@endphp

@section('html_lang', 'en')
@section('title', 'Holy Rosary: Luminous Mysteries — Bible passages and reflections')
@section('meta_description', 'Pray and meditate on the Luminous Mysteries (Thursday) with Scripture passages and reflections to contemplate the public life of Jesus and His light.')
@section('canonical', $CANONICAL_URL)
@section('robots', 'index,follow')

@section('hreflang')
  <link rel="alternate" hreflang="en" href="{{ $CANONICAL_URL }}"/>
  <link rel="alternate" hreflang="pt-BR" href="{{ $SITE_URL }}/santo-terco/misterios-luminosos"/>
  <link rel="alternate" hreflang="x-default" href="{{ $SITE_URL }}/santo-terco/misterios-luminosos"/>
@endsection

@section('og_title', 'Holy Rosary: Luminous Mysteries')
@section('og_description', 'Thursday: contemplate Christ’s light. Pray with Scripture and meditations for each decade.')
@section('og_url', $CANONICAL_URL)
@section('og_image', $SITE_URL.'/og/terco/misterios-luminosos.png?v=1')

@push('head')
  <script type="application/ld+json">{!! json_encode($webPageSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
  <script type="application/ld+json">{!! json_encode($articleSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
  <script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
  <script type="application/ld+json">{!! json_encode($faqSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
  <script type="application/ld+json">{!! json_encode($itemListSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>

  {{-- Adsense loader (1x por página) --}}
  <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ $ADS_CLIENT }}" crossorigin="anonymous"></script>

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
<article class="mx-auto w-full max-w-6xl px-3 pb-16 pt-6 sm:px-6 lg:px-8 text-foreground mt-10" itemscope itemtype="https://schema.org/Article">
  <div id="top" class="h-0 w-0 scroll-mt-24"></div>

  <header class="space-y-4 mt-3">
    <div class="flex flex-wrap items-center gap-2">
      <span class="inline-flex items-center rounded-full border border-border bg-muted px-3 py-1 text-xs font-semibold text-foreground">Rosary</span>
      <span class="inline-flex items-center rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-900">Thursday</span>
    </div>

    <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl">Luminous Mysteries of the Rosary</h1>

    <p class="text-base leading-relaxed text-muted-foreground sm:text-lg">
      The Luminous Mysteries (Mysteries of Light) contemplate Jesus’ public life: identity, signs, conversion, listening, and the Eucharist.
      Here you’ll find <strong>Scripture passages</strong> and reflections to meditate with clarity.
    </p>

    <section class="rounded-2xl border border-amber-200 bg-amber-50 p-4 shadow-sm sm:p-5" aria-label="Quick answer">
      <p class="text-xs font-semibold text-amber-900">Quick answer</p>
      <div class="mt-2 space-y-2 text-sm leading-relaxed text-foreground">
        <p>
          The <strong>Luminous Mysteries</strong> are traditionally prayed on <strong>Thursdays</strong>.
          They help you contemplate Christ’s light in daily life: beloved identity, obedience, conversion, listening, and communion.
        </p>
        <p class="text-muted-foreground">
          Practical tip: before each decade, choose a guiding word (identity, trust, conversion, listening, communion).
        </p>
      </div>

      <div class="mt-4 flex flex-wrap gap-2">
        <a href="{{ url('/en/rosary') }}"
           class="inline-flex items-center justify-center rounded-xl bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500">
          Pray now (guided)
        </a>
        <a href="{{ url('/en/rosary/how-to-pray-the-rosary') }}"
           class="inline-flex items-center justify-center rounded-xl border border-amber-200 bg-white px-4 py-2 text-sm font-semibold text-amber-900 shadow-sm hover:bg-amber-100 focus:outline-none focus:ring-2 focus:ring-amber-500">
          How to pray the Rosary
        </a>
      </div>
    </section>
  </header>

  {{-- GRID: conteúdo + aside (padrão) --}}
  <div class="rosary-grid gap-6 items-start mt-6">
    {{-- MAIN --}}
    <section class="min-w-0 rounded-2xl border border-border bg-card p-4 shadow-sm sm:p-6 lg:p-8">
      <nav aria-label="Luminous Mysteries summary" class="rounded-2xl border border-border bg-muted/40 p-4">
        <p class="text-sm font-semibold text-foreground">Summary</p>
        <div class="mt-3 grid gap-2 sm:grid-cols-2">
          @foreach($mysteries as $m)
            <a href="#{{ $m['id'] }}"
               class="rounded-xl border border-border bg-card px-3 py-2 text-sm text-muted-foreground shadow-sm transition hover:bg-muted hover:text-foreground">
              {{ $m['title'] }}: {{ $m['subtitle'] }}
            </a>
          @endforeach
        </div>
      </nav>

      {{-- Adsense top --}}
      <div class="mt-4 rounded-2xl border border-border bg-card p-3 shadow-sm">
        <p class="px-1 pb-2 text-[11px] font-semibold text-muted-foreground">Advertisement</p>
        <div class="w-full overflow-hidden rounded-xl bg-muted/30" style="min-height:160px">
          <ins class="adsbygoogle"
               style="display:block;min-height:160px"
               data-ad-client="{{ $ADS_CLIENT }}"
               data-ad-slot="{{ $ADS_SLOT_BODY_TOP }}"
               data-ad-format="auto"
               data-full-width-responsive="true"></ins>
        </div>
        @push('scripts')
          <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
        @endpush
      </div>

      <div class="{{ $readingProse }} mt-7">
        <h2 id="what-are">What are the Luminous Mysteries?</h2>
        <p>
          They contemplate Christ’s light in public life: the Father reveals the Son, Jesus works signs, calls to conversion,
          transfigures the heart through listening, and gives Himself as food in the Eucharist.
        </p>
      </div>

      <div class="mt-8 space-y-7">
        @foreach($mysteries as $idx => $m)
          @php
            $question = $splitPoint($m['points'][0] ?? '');
            $fruit = $splitPoint($m['points'][1] ?? '');
            $anchor = $m['reflection'][1] ?? null;
          @endphp

          <div class="space-y-4 mt-3">
            <section id="{{ $m['id'] }}" class="scroll-mt-24 rounded-2xl border border-amber-200 bg-amber-50/40 p-4 shadow-sm sm:p-6">
              <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                <div>
                  <p class="text-xs font-semibold text-amber-900">{{ $m['title'] }}</p>
                  <h3 class="mt-1 text-2xl font-bold text-foreground">{{ $m['subtitle'] }}</h3>
                </div>
                <span class="inline-flex w-fit items-center rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-900">
                  {{ $m['passageRef'] }}
                </span>
              </div>

              <div class="mt-5 rounded-2xl border border-amber-200 bg-white p-5">
                <p class="text-xs font-semibold text-amber-900">Scripture</p>
                <p class="mt-3 text-[15px] leading-relaxed text-foreground sm:text-[16px]">“{{ $m['passageQuote'] }}”</p>
                <p class="mt-2 text-xs text-muted-foreground">{{ $m['passageRef'] }}</p>
              </div>

              <div class="mt-6">
                <p class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Meditation</p>

                <div class="mt-3 max-w-[65ch]">
                  <div class="space-y-4 text-[15px] leading-relaxed text-foreground sm:text-[16px]">
                    @foreach($m['reflection'] as $p)
                      <p class="m-0">{{ $p }}</p>
                    @endforeach
                  </div>

                  @if($anchor)
                    <div class="my-5 rounded-xl border border-amber-200 bg-white/70 px-4 py-3 text-sm italic text-amber-900">
                      {{ $anchor }}
                    </div>
                  @endif

                  <div class="mt-5 space-y-3">
                    <div class="rounded-xl border border-amber-200 bg-white px-4 py-3">
                      <p class="text-xs font-semibold uppercase tracking-wide text-amber-800">Question to ponder</p>
                      <p class="mt-2 text-sm leading-relaxed text-foreground">{{ $question ?: '—' }}</p>
                    </div>

                    <div class="rounded-xl border border-border bg-muted/40 px-4 py-3">
                      <p class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Spiritual fruit</p>
                      <p class="mt-1 text-sm text-foreground">{{ $fruit ?: '—' }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <div class="mt-6 flex flex-wrap gap-2">
                <a href="{{ url('/en/rosary') }}"
                   class="inline-flex items-center justify-center rounded-xl bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500">
                  Pray now (mystery of the day)
                </a>
                <a href="#top"
                   class="inline-flex items-center justify-center rounded-xl border border-border bg-card px-4 py-2 text-sm font-semibold text-foreground shadow-sm hover:bg-muted focus:outline-none focus:ring-2 focus:ring-amber-500">
                  Back to top
                </a>
              </div>
            </section>

            @if($idx === 2)
              <div class="pt-1 rounded-2xl border border-border bg-card p-3 shadow-sm">
                <p class="px-1 pb-2 text-[11px] font-semibold text-muted-foreground">Advertisement</p>
                <div class="w-full overflow-hidden rounded-xl bg-muted/30 py-2">
                  <ins class="adsbygoogle"
                       style="display:block;text-align:center"
                       data-ad-layout="in-article"
                       data-ad-format="fluid"
                       data-ad-client="{{ $ADS_CLIENT }}"
                       data-ad-slot="{{ $ADS_SLOT_IN_ARTICLE }}"></ins>
                </div>
                @push('scripts')
                  <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
                @endpush
              </div>
            @endif
          </div>
        @endforeach
      </div>
    </section>

    {{-- MOBILE: Aside colapsável --}}
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
            'setKey' => $setKey,

            // ads
            'adsSlotDesktop300x250' => $ADS_SLOT_SIDEBAR_DESKTOP,
            'adsSlotDesktop' => $ADS_SLOT_SIDEBAR_DESKTOP,
            'adsSlotMobile' => $ADS_SLOT_SIDEBAR_MOBILE,

            // blog links
            'blogLinks' => $blogLinksAside,
          ])
        </div>
      </div>
    </div>

    {{-- ASIDE Desktop --}}
    <aside class="rosary-aside-desktop hidden lg:block min-w-0">
      <div class="sticky top-20">
        @include('terco.partials.aside', [
          'lang' => 'en',
          'variant' => 'desktop',
          'setKey' => $setKey,

          // ads
          'adsSlotDesktop300x250' => $ADS_SLOT_SIDEBAR_DESKTOP,
          'adsSlotDesktop' => $ADS_SLOT_SIDEBAR_DESKTOP,
          'adsSlotMobile' => $ADS_SLOT_SIDEBAR_MOBILE,

          // blog links
          'blogLinks' => $blogLinksAside,
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
