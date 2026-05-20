{{-- resources/views/terco/en/sorrowful-mysteries.blade.php --}}
@extends('layouts.site')

@php
  $SITE_URL = 'https://www.iatioben.com.br';
  $PAGE_PATH = '/en/holy-rosary/sorrowful-mysteries';
  $CANONICAL_URL = $SITE_URL.$PAGE_PATH;

  $ADS_CLIENT = 'ca-pub-8819996017476509';
  $ADS_SLOT_BODY_TOP = '7474884427';
  $ADS_SLOT_IN_ARTICLE = '6161802751';
  $ADS_SLOT_SIDEBAR_DESKTOP = '8534838745';
  $ADS_SLOT_SIDEBAR_MOBILE = '1573844576';

  $today = now()->toDateString();

  // IMPORTANTE: setKey usado pelo aside para destacar o conjunto ativo
  $setKey = 'dolorosos';

  $mysteries = [
    [
      'id' => 'agony',
      'title' => '1st Sorrowful Mystery',
      'subtitle' => 'The Agony of Jesus in the Garden',
      'passageRef' => 'Mt 26:36–46 (cf. Lk 22:39–46)',
      'passageQuote' => '“My Father, if it is possible, let this cup pass from me… yet not as I will, but as you will.”',
      'reflection' => [
        'This mystery brings us to a decisive point: when God’s will and our will seem to collide. Jesus does not pretend to be calm; He prays from real pain.',
        'The Garden shows that prayer does not remove the inner struggle — it transforms struggle into surrender. Instead of escaping, Jesus remains; instead of hardening, He opens Himself to the Father.',
        'There are moments when the heart wants shortcuts: numbing anxiety, avoiding confrontation, postponing decisions. Here we learn the opposite: name our fear before God and choose fidelity.',
        'To pray this mystery is to place your “cup” before the Lord — what you cannot control, what weighs on you, what requires courage — and ask for the grace to pass through it without losing God.',
      ],
      'points' => [
        'Question to ponder: what “cup” have I been avoiding facing with God?',
        'Traditional spiritual fruit: conformity to God’s will and watchfulness.',
      ],
    ],
    [
      'id' => 'scourging',
      'title' => '2nd Sorrowful Mystery',
      'subtitle' => 'The Scourging of Jesus',
      'passageRef' => 'Jn 19:1 (cf. Is 53:5)',
      'passageQuote' => '“Then Pilate took Jesus and had him scourged.”',
      'reflection' => [
        'The Scourging confronts us with injustice and with violence born of fear and cowardice. Jesus suffers without striking back, showing that love’s strength does not depend on aggression.',
        'To contemplate this mystery is to recognize that evil tries to disfigure us — and that Christ takes that disfigurement upon Himself to restore our dignity.',
        'It also invites us to review what “scourges” us inside: old guilt, addictions, impulses, harsh words, habits that wound. God does not humiliate us; He heals.',
        'Praying here is asking for purification: that the Lord undo in us what harms, and train us in a firm meekness that resists without becoming cruel.',
      ],
      'points' => [
        'Question to ponder: what wounds do I need to surrender so God may heal them in truth?',
        'Traditional spiritual fruit: purity and mortification of the senses.',
      ],
    ],
    [
      'id' => 'crown',
      'title' => '3rd Sorrowful Mystery',
      'subtitle' => 'The Crowning with Thorns',
      'passageRef' => 'Mt 27:27–31',
      'passageQuote' => '“They put a crown of thorns on him… and mocked him.”',
      'reflection' => [
        'The pain here is not only physical: it is humiliation — an assault on identity. The world tries to ridicule truth, diminish holiness, and caricature faith.',
        'Jesus accepts an “upside-down” crown to teach that God’s kingship does not rely on applause. Christ’s dignity does not depend on others’ opinions.',
        'This mystery touches our insecurities: the need for approval, fear of appearing weak, the desire to control our image. Holiness matures when we stop living as hostages to validation.',
        'To pray the Crowning is to ask for inner freedom: that no mockery — external or internal — can steal what God says about you.',
      ],
      'points' => [
        'Question to ponder: where do I seek validation at the cost of the Gospel’s truth?',
        'Traditional spiritual fruit: courage and contempt for vanity.',
      ],
    ],
    [
      'id' => 'cross',
      'title' => '4th Sorrowful Mystery',
      'subtitle' => 'Jesus Carries the Cross',
      'passageRef' => 'Lk 23:26–32',
      'passageQuote' => '“If anyone would come after me, let him deny himself, take up his cross, and follow me.”',
      'reflection' => [
        'Carrying the cross is not romanticizing suffering. It is learning that faithful love has weight — and that weight, when carried with Christ, gains meaning.',
        'Jesus falls, rises, and continues. The Gospel does not hide weakness; it shows that perseverance is stronger than perfection.',
        'Simon of Cyrene appears as unexpected grace: God allows concrete help. The cross was not made to be carried in solitary pride.',
        'To pray this mystery is to ask: what fidelity must I sustain today? And also: whom can I help carry a portion of the weight?',
      ],
      'points' => [
        'Question to ponder: which “cross” today can be lived as love — not as revolt?',
        'Traditional spiritual fruit: patience in trials.',
      ],
    ],
    [
      'id' => 'crucifixion',
      'title' => '5th Sorrowful Mystery',
      'subtitle' => 'The Crucifixion and Death of Jesus',
      'passageRef' => 'Jn 19:17–30 (cf. Lk 23:33–46)',
      'passageQuote' => '“It is finished.”',
      'reflection' => [
        'The Cross is where love goes to the end. Jesus does not merely suffer: He gives Himself. He transforms received violence into offering and opens a path of reconciliation.',
        '“It is finished” is not defeat; it is fullness — love brought to completion, even when outwardly it looks like failure.',
        'To contemplate the Crucifixion is to measure life by another standard: not control, not immediate success, but fidelity to the good.',
        'To pray this mystery is to let the Cross illuminate your losses and pains: God does not waste tears when they are placed in His hands. The Cross is passage.',
      ],
      'points' => [
        'Question to ponder: what do I need to surrender to God so He may transform it into life?',
        'Traditional spiritual fruit: love for Jesus and a spirit of sacrifice.',
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
    ['q' => 'On which days are the Sorrowful Mysteries prayed?', 'a' => 'Traditionally, the Sorrowful Mysteries are prayed on Tuesdays and Fridays.'],
    ['q' => 'What are the five Sorrowful Mysteries?', 'a' => 'Agony in the Garden, Scourging at the Pillar, Crowning with Thorns, Carrying of the Cross, Crucifixion and Death.'],
    ['q' => 'How can I meditate on them without losing hope?', 'a' => 'Meditate on Christ’s faithful love: announce the mystery, pause briefly, and pray each Hail Mary as an act of trust and surrender.'],
    ['q' => 'Can I pray the Sorrowful Mysteries on another day?', 'a' => 'Yes. The weekday assignment is a devotional tradition; you may pray according to your spiritual need.'],
  ];

  $articleSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $CANONICAL_URL],
    'headline' => 'Sorrowful Mysteries of the Rosary: Bible passages and reflections',
    'description' => 'Complete guide to the Sorrowful Mysteries with Scripture references, reflections for meditation, and practical guidance to pray with meaning.',
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
      ['@type' => 'Thing', 'name' => 'Sorrowful Mysteries'],
      ['@type' => 'Thing', 'name' => 'Passion of Christ'],
      ['@type' => 'Thing', 'name' => 'Rosary'],
    ],
    'keywords' => [
      'sorrowful mysteries',
      'rosary tuesday friday',
      'passion of christ',
      'how to meditate on the rosary',
      'bible passages rosary',
    ],
  ];

  $breadcrumbSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
      ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $SITE_URL.'/en'],
      ['@type' => 'ListItem', 'position' => 2, 'name' => 'Holy Rosary', 'item' => $SITE_URL.'/en/holy-rosary'],
      ['@type' => 'ListItem', 'position' => 3, 'name' => 'Sorrowful Mysteries', 'item' => $CANONICAL_URL],
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
    'name' => 'Sorrowful Mysteries of the Rosary',
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
    'name' => 'Sorrowful Mysteries of the Rosary: Bible passages and reflections',
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
    ['title' => 'How to face anxiety in prayer (Garden)', 'slug' => 'how-to-face-anxiety-in-prayer'],
    ['title' => 'Wounds and inner healing: what the Scourging teaches', 'slug' => 'scourging-and-inner-healing'],
    ['title' => 'Inner freedom vs. vanity (thorns)', 'slug' => 'crown-of-thorns-and-vanity'],
    ['title' => 'How to carry the cross without hardening', 'slug' => 'carry-the-cross-without-hardening'],
    ['title' => 'The meaning of “It is finished”', 'slug' => 'it-is-finished-meaning'],
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
@section('title', 'Holy Rosary: Sorrowful Mysteries — Bible passages and reflections')
@section('meta_description', 'Pray and meditate on the Sorrowful Mysteries (Tuesday and Friday) with Scripture passages and reflections to live the Passion of Christ with meaning.')
@section('canonical', $CANONICAL_URL)
@section('robots', 'index,follow')

@section('hreflang')
  <link rel="alternate" hreflang="en" href="{{ $CANONICAL_URL }}"/>
  <link rel="alternate" hreflang="pt-BR" href="{{ $SITE_URL }}/santo-terco/misterios-dolorosos"/>
  <link rel="alternate" hreflang="x-default" href="{{ $SITE_URL }}/santo-terco/misterios-dolorosos"/>
@endsection

@section('og_title', 'Holy Rosary: Sorrowful Mysteries')
@section('og_description', 'Tuesday and Friday: go deeper into the Passion of Christ. Pray with Scripture and meditations for each decade.')
@section('og_url', $CANONICAL_URL)
@section('og_image', $SITE_URL.'/og/terco/misterios-dolorosos.png?v=1')

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
      <span class="inline-flex items-center rounded-full border border-border bg-muted px-3 py-1 text-xs font-semibold text-foreground">
        Holy Rosary
      </span>
      <span class="inline-flex items-center rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-900">
        Tuesday & Friday
      </span>
    </div>

    <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl">
      Sorrowful Mysteries of the Rosary
    </h1>

    <p class="text-base leading-relaxed text-muted-foreground sm:text-lg">
      The Sorrowful Mysteries contemplate the Lord’s Passion. Here you’ll find <strong>Scripture passages</strong> and reflections to meditate on each decade with depth and hope.
    </p>

    <section class="rounded-2xl border border-amber-200 bg-amber-50 p-4 shadow-sm sm:p-5" aria-label="Quick answer">
      <p class="text-xs font-semibold text-amber-900">Quick answer</p>
      <div class="mt-2 space-y-2 text-sm leading-relaxed text-foreground">
        <p>
          The <strong>Sorrowful Mysteries</strong> are traditionally prayed on <strong>Tuesdays</strong> and <strong>Fridays</strong>.
          They help you pray the Cross without despair by contemplating Christ’s faithful love.
        </p>
        <p class="text-muted-foreground">
          Practical tip: announce the mystery and pray each Hail Mary as an act of trust and surrender.
        </p>
      </div>

      <div class="mt-4 flex flex-wrap gap-2">
        <a href="{{ url('/en/holy-rosary') }}"
           class="inline-flex items-center justify-center rounded-xl bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500">
          Pray now (guided)
        </a>
        <a href="{{ url('/en/holy-rosary/how-to-pray') }}"
           class="inline-flex items-center justify-center rounded-xl border border-amber-200 bg-white px-4 py-2 text-sm font-semibold text-amber-900 shadow-sm hover:bg-amber-100 focus:outline-none focus:ring-2 focus:ring-amber-500">
          How to pray the Rosary
        </a>
      </div>
    </section>
  </header>

  <div class="rosary-grid gap-6 items-start mt-6">
    {{-- MAIN --}}
    <section class="min-w-0 rounded-2xl border border-border bg-card p-4 shadow-sm sm:p-6 lg:p-8">
      <nav aria-label="Sorrowful Mysteries summary" class="rounded-2xl border border-border bg-muted/40 p-4">
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
        <h2 id="what-are">What are the Sorrowful Mysteries?</h2>
        <p>
          They place us before the way of the Cross — not as a distant story, but as a school of faithful love. By contemplating the Passion,
          we learn that God does not abandon humanity in pain — He enters it from within.
        </p>
        <p>
          Praying these mysteries forms the heart for compassion: to look at suffering without hardening, and to respond with fidelity and charity.
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
                <p class="mt-3 text-[15px] leading-relaxed text-foreground sm:text-[16px]">
                  “{{ $m['passageQuote'] }}”
                </p>
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
                <a href="{{ url('/en/holy-rosary') }}"
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

      {{-- FAQ (render opcional; JSON-LD já existe) --}}
      <div class="{{ $readingProse }} mt-12">
        <h2 id="faq">Frequently asked questions</h2>
        <div class="mt-4 space-y-3">
          @foreach($faq as $qa)
            <details class="rounded-xl border border-amber-200 bg-card p-4">
              <summary class="cursor-pointer text-sm font-semibold text-foreground">{{ $qa['q'] }}</summary>
              <p class="mt-3 text-sm text-muted-foreground">{{ $qa['a'] }}</p>
            </details>
          @endforeach
        </div>
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
