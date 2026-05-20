{{-- resources/views/terco/en/glorious-mysteries.blade.php --}}
@extends('layouts.site')

@php
  $SITE_URL = 'https://www.iatioben.com.br';
  $PAGE_PATH = '/en/rosary/glorious-mysteries';
  $CANONICAL_URL = $SITE_URL.$PAGE_PATH;

  // Adsense
  $ADS_CLIENT = 'ca-pub-8819996017476509';
  $ADS_SLOT_BODY_TOP = '7474884427';
  $ADS_SLOT_IN_ARTICLE = '6161802751';
  $ADS_SLOT_SIDEBAR_DESKTOP = '8534838745';
  $ADS_SLOT_SIDEBAR_MOBILE = '1573844576';

  $today = now()->toDateString();

  // IMPORTANTE: o aside espera setKey/currentSetKey e usa isso para destacar o ativo
  $setKey = 'gloriosos';

  $mysteries = [
    [
      'id' => 'resurrection',
      'title' => '1st Glorious Mystery',
      'subtitle' => 'The Resurrection of Jesus',
      'passageRef' => 'Lk 24:1–12 (cf. Jn 20:1–18)',
      'passageQuote' => '“Why do you seek the living among the dead?”',
      'reflection' => [
        'The Resurrection is not merely a “happy ending”: it is the beginning of a new creation. God does not return Jesus to the past; He opens the future.',
        'The empty tomb teaches that Christian hope is not optimism: it is certainty grounded in God’s action, even when reality seems sealed.',
        'For us, rising begins within: when grace reopens doors closed by sorrow, when forgiveness breaks cycles, when the heart dares to believe again.',
        'To pray this mystery is to ask God to raise in us what has died through discouragement, fear, or sin. The Lord does not only comfort — He re-creates.',
      ],
      'points' => [
        'Question: where do I need to hope again?',
        'Fruit: living faith and spiritual joy.',
      ],
    ],
    [
      'id' => 'ascension',
      'title' => '2nd Glorious Mystery',
      'subtitle' => 'The Ascension of the Lord',
      'passageRef' => 'Acts 1:6–11',
      'passageQuote' => '“This Jesus… will come in the same way as you saw him go.”',
      'reflection' => [
        'The Ascension is not absence: it is presence in another mode. Jesus goes to the Father and, at the same time, opens heaven’s way for us.',
        'The disciples are told not to keep staring upward indefinitely. Faith is not escape from the world, but mission within it — with a heart lifted to God.',
        'The Ascension heals a common spiritual vice: wanting to hold God on our terms. Christ trains us to trust, to walk, and to witness.',
        'To pray this mystery is to realign priorities: feet on the ground and heart on high — without losing hope of the definitive encounter.',
      ],
      'points' => [
        'Question: what distracts me from today’s concrete mission?',
        'Fruit: hope and desire for heaven.',
      ],
    ],
    [
      'id' => 'pentecost',
      'title' => '3rd Glorious Mystery',
      'subtitle' => 'The Descent of the Holy Spirit',
      'passageRef' => 'Acts 2:1–13',
      'passageQuote' => '“They were all filled with the Holy Spirit.”',
      'reflection' => [
        'Pentecost heals fear. The Spirit does not only change circumstances; He changes persons — giving courage, clarity, steadfastness, and inner fire.',
        'The same group that once hid now proclaims. Faith is not temperament; it is grace received and answered.',
        'The Spirit also unifies: where there is Babel, He creates communion. Where there is confusion, He grants discernment.',
        'To pray this mystery is to ask for concrete gifts: wisdom to decide, fortitude to persevere, charity to love, and humility to obey God.',
      ],
      'points' => [
        'Question: which gift do I need to ask for insistently today?',
        'Fruit: apostolic zeal and courage.',
      ],
    ],
    [
      'id' => 'assumption',
      'title' => '4th Glorious Mystery',
      'subtitle' => 'The Assumption of Mary',
      'passageRef' => 'Rev 12:1 (Church tradition)',
      'passageQuote' => '“A great sign appeared in heaven: a woman clothed with the sun.”',
      'reflection' => [
        'The Assumption reveals the promised destiny: God does not save “halfway.” He desires the whole person — and the whole of life transfigured.',
        'In Mary, the Church contemplates what she hopes for: not escape from the body, but grace’s victory over death.',
        'This mystery feeds a concrete hope: history does not end in wear and tear. In God, fidelity has a future.',
        'To pray the Assumption is to see life with an eternal horizon — and to live the present with greater purity, courage, and trust.',
      ],
      'points' => [
        'Question: am I living with an eternal horizon or only reacting to the immediate?',
        'Fruit: hope and purity.',
      ],
    ],
    [
      'id' => 'coronation',
      'title' => '5th Glorious Mystery',
      'subtitle' => 'The Coronation of Mary in Heaven',
      'passageRef' => 'Rev 12:1 (symbol) / tradition',
      'passageQuote' => '“A woman… and a crown of twelve stars.”',
      'reflection' => [
        'Mary’s Coronation is not “competition” for greatness: it is the exaltation of humility. God crowns those who learn to serve.',
        'Mary is an image of the glorified Church: what God begins in faith, He brings to fullness. Salvation history has a coronation.',
        'This mystery purifies desire: not seeking human glory, but God’s glory — which is to love and remain faithful.',
        'To pray this mystery is to ask that our life end well: with fidelity, perseverance, and love to the end.',
      ],
      'points' => [
        'Question: what “glory” have I been chasing — and what does God want to purify in me?',
        'Fruit: filial trust and perseverance.',
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
    ['q' => 'On which days are the Glorious Mysteries prayed?', 'a' => 'Traditionally on Wednesdays and Sundays.'],
    ['q' => 'What are the five Glorious Mysteries?', 'a' => 'Resurrection, Ascension, Pentecost, Assumption of Mary, and Coronation of Mary.'],
    ['q' => 'How should I meditate on the Glorious Mysteries?', 'a' => 'Announce the mystery, pause briefly, and pray each Hail Mary as an act of hope. Contemplate God’s victory and ask for the grace to live with an eternal horizon.'],
    ['q' => 'Can I pray them on another day?', 'a' => 'Yes. The weekday assignment is a devotional tradition.'],
  ];

  $articleSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $CANONICAL_URL],
    'headline' => 'Glorious Mysteries of the Rosary: Bible passages and reflections',
    'description' => 'Complete guide to the Glorious Mysteries with Scripture references, reflections, and practical guidance to pray with meaning.',
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
      ['@type' => 'Thing', 'name' => 'Glorious Mysteries'],
      ['@type' => 'Thing', 'name' => 'Resurrection'],
      ['@type' => 'Thing', 'name' => 'Pentecost'],
      ['@type' => 'Thing', 'name' => 'Rosary'],
    ],
    'keywords' => [
      'glorious mysteries',
      'rosary wednesday sunday',
      'resurrection',
      'pentecost',
      'how to meditate on the rosary',
    ],
  ];

  $breadcrumbSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
      ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $SITE_URL.'/en'],
      ['@type' => 'ListItem', 'position' => 2, 'name' => 'Rosary', 'item' => $SITE_URL.'/en/rosary'],
      ['@type' => 'ListItem', 'position' => 3, 'name' => 'Glorious Mysteries', 'item' => $CANONICAL_URL],
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
    'name' => 'Glorious Mysteries of the Rosary',
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
    'name' => 'Glorious Mysteries of the Rosary: Bible passages and reflections',
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
    ['title' => 'What does it mean to “rise” within?', 'slug' => 'rise-within-meaning', 'desc' => null],
    ['title' => 'Ascension: absent God or present in a new way?', 'slug' => 'ascension-presence-new-way', 'desc' => null],
    ['title' => 'Gifts of the Holy Spirit: how to ask and live them', 'slug' => 'gifts-holy-spirit-how-to-ask', 'desc' => null],
    ['title' => 'Assumption of Mary: what the Church teaches', 'slug' => 'assumption-of-mary-explained', 'desc' => null],
    ['title' => 'Coronation of Mary: meaning and spirituality', 'slug' => 'coronation-of-mary-meaning', 'desc' => null],
  ];

  // ===== Params do Aside (no formato do seu partial) =====
  $asideBlogLinks = array_map(function ($x) {
    return [
      'href'  => url('/en/blog/'.$x['slug']),
      'title' => $x['title'],
      'desc'  => $x['desc'] ?? null,
    ];
  }, $blogLinks);

  // Aqui é uma página de set mesmo -> mantém $setKey
  $asideSetKey = $setKey;

  $jsonFlags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
@endphp

@section('html_lang', 'en')
@section('title', 'Holy Rosary: Glorious Mysteries — Bible passages and reflections')
@section('meta_description', 'Pray and meditate on the Glorious Mysteries (Wednesday and Sunday) with Scripture passages and reflections to contemplate the Resurrection and the glory of Heaven.')
@section('canonical', $CANONICAL_URL)
@section('robots', 'index,follow')

@section('hreflang')
  <link rel="alternate" hreflang="en" href="{{ $CANONICAL_URL }}"/>
  <link rel="alternate" hreflang="pt-BR" href="{{ $SITE_URL }}/santo-terco/misterios-gloriosos"/>
  <link rel="alternate" hreflang="x-default" href="{{ $SITE_URL }}/santo-terco/misterios-gloriosos"/>
@endsection

@section('og_title', 'Holy Rosary: Glorious Mysteries')
@section('og_description', 'Wednesday and Sunday: contemplate the Resurrection and Christian hope. Pray with Scripture and meditations for each decade.')
@section('og_url', $CANONICAL_URL)
@section('og_image', $SITE_URL.'/og/terco/misterios-gloriosos.png?v=1')

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

  <script type="application/ld+json">{!! json_encode($webPageSchema, $jsonFlags) !!}</script>
  <script type="application/ld+json">{!! json_encode($articleSchema, $jsonFlags) !!}</script>
  <script type="application/ld+json">{!! json_encode($breadcrumbSchema, $jsonFlags) !!}</script>
  <script type="application/ld+json">{!! json_encode($faqSchema, $jsonFlags) !!}</script>
  <script type="application/ld+json">{!! json_encode($itemListSchema, $jsonFlags) !!}</script>

  {{-- Adsense loader (1x por página) --}}
  <script>
    window.addEventListener('load', function() {
      setTimeout(function(){
        var s = document.createElement('script');
        s.src = "https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=...";
        s.async = true;
        document.head.appendChild(s);
      }, 2500);
    });
    </script>
@endpush

@section('content')
<article
  class="mx-auto w-full max-w-6xl px-3 pb-16 pt-6 sm:px-6 lg:px-8 text-foreground"
  style="margin-top:10px"
  itemscope
  itemtype="https://schema.org/Article"
>
  <div id="top" class="h-0 w-0 scroll-mt-24"></div>

  <header class="space-y-4 mt-3">
    <div class="flex flex-wrap items-center gap-2">
      <span class="inline-flex items-center rounded-full border border-border bg-muted px-3 py-1 text-xs font-semibold text-foreground">Rosary</span>
      <span class="inline-flex items-center rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-900">Wednesday & Sunday</span>
    </div>

    <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl">Glorious Mysteries of the Rosary</h1>

    <p class="text-base leading-relaxed text-muted-foreground sm:text-lg">
      The Glorious Mysteries contemplate God’s victory: Resurrection, Ascension, Pentecost, Assumption, and the Coronation of Mary.
      Here you’ll find <strong>Scripture passages</strong> and reflections to pray with hope.
    </p>

    <section class="rounded-2xl border border-amber-200 bg-amber-50 p-4 shadow-sm sm:p-5" aria-label="Quick answer">
      <p class="text-xs font-semibold text-amber-900">Quick answer</p>
      <div class="mt-2 space-y-2 text-sm leading-relaxed text-foreground">
        <p>
          The <strong>Glorious Mysteries</strong> are traditionally prayed on <strong>Wednesdays</strong> and <strong>Sundays</strong>.
          They strengthen hope and remind us that history ends in God.
        </p>
        <p class="text-muted-foreground">
          Practical tip: pray each decade as an act of concrete hope — “Lord, make all things new.”
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

  {{-- GRID: conteúdo + aside (igual modelo) --}}
  <div class="rosary-grid gap-6 items-start mt-6">
    {{-- MAIN --}}
    <section class="min-w-0 rounded-2xl border border-border bg-card p-4 shadow-sm sm:p-6 lg:p-8">
      <nav aria-label="Glorious Mysteries summary" class="rounded-2xl border border-border bg-muted/40 p-4">
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
        <h2 id="what-are">What are the Glorious Mysteries?</h2>
        <p>
          They help us contemplate that the final word is not sin or death, but life in God. The Resurrection opens the future; the Ascension orients mission;
          Pentecost ignites the heart; and in Mary we see the promised destiny.
        </p>
        <p>
          Praying these mysteries forms hope: God acts in history and, at the same time, works quietly within us.
          What seems closed can be reopened by grace.
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

      <div class="{{ $readingProse }} mt-12">
        <h2 id="how-to-pray">How to pray the Glorious Mysteries (practically)</h2>
        <p>
          Announce the mystery, pray one Our Father, ten Hail Marys, and the Glory Be. While praying, ask for the grace of concrete hope:
          God’s victory is not theory — it becomes perseverance and charity.
        </p>

        <h3 id="how-to-meditate">How to meditate with hope</h3>
        <ul>
          <li>Before each decade, repeat interiorly: “Lord, renew my hope.”</li>
          <li>Pray each Hail Mary connecting the scene to your day (Resurrection to begin again, Pentecost for courage, etc.).</li>
          <li>At the end, give thanks for a sign of life God has already given you (even a small one).</li>
        </ul>
      </div>
    </section>

    {{-- MOBILE: Aside colapsável (igual modelo) --}}
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
