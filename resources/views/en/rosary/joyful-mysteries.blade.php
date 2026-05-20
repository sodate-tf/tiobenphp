{{-- resources/views/terco/en/joyful-mysteries.blade.php --}}
@extends('layouts.site')

@php
  $SITE_URL = 'https://www.iatioben.com.br';
  $PAGE_PATH = '/en/rosary/joyful-mysteries';
  $CANONICAL_URL = $SITE_URL.$PAGE_PATH;

  $ADS_CLIENT = 'ca-pub-8819996017476509';
  $ADS_SLOT_BODY_TOP = '3041346283';
  $ADS_SLOT_IN_ARTICLE = '2672028232';
  $ADS_SLOT_SIDEBAR_DESKTOP = '3041346283';
  $ADS_SLOT_SIDEBAR_MOBILE = '3041346283';

  $today = now()->toDateString();

  // IMPORTANTE: o aside espera setKey/currentSetKey e usa isso para destacar o ativo
  $setKey = 'gozosos';

  $mysteries = [
    [
      'id' => 'annunciation',
      'title' => '1st Joyful Mystery',
      'subtitle' => 'The Annunciation of the Lord',
      'passageRef' => 'Lk 1:26–38',
      'passageQuote' => '“Behold, I am the handmaid of the Lord; let it be done to me according to your word.”',
      'reflection' => [
        'The joy of the Gospel does not begin when everything is solved. It begins when God enters daily life and asks for room in the heart.',
        'Mary does not receive a map, but a call. Her first reaction is human: she is troubled, she asks, she ponders. Faith is not anesthesia; it is trust built in dialogue with God.',
        'Mary’s “let it be done” is not passivity. It is active surrender: she places her whole self at the service of the Lord’s plan. When life demands quick answers, this mystery reminds us that spiritual maturity begins with an interior decision: to trust.',
        'To pray this mystery is to place before God what you still do not understand — a waiting, a fear, a change — and ask for the grace to respond with humility and courage, without hardening the heart.',
      ],
      'points' => [
        'Question: what is God inviting me to welcome today?',
        'Fruit: humility and docility to God’s will.',
      ],
    ],
    [
      'id' => 'visitation',
      'title' => '2nd Joyful Mystery',
      'subtitle' => 'The Visitation of Mary to Elizabeth',
      'passageRef' => 'Lk 1:39–56',
      'passageQuote' => '“When Elizabeth heard Mary’s greeting, the child leaped in her womb.”',
      'reflection' => [
        'Faith that remains only an idea tends to stay still. True faith moves: it visits, serves, strengthens.',
        'Mary, newly called by God, does not close in on her own mystery. She goes to someone in need. This readiness is deeply evangelical: God gives gifts not to isolate us, but to help us love better.',
        'The Visitation also teaches that Christ’s presence in us transforms the atmosphere: where Mary arrives, there is joy, life leaps, blessing is recognized.',
        'To pray this mystery is to ask for an available heart: able to step outside itself, see real needs, and bring consolation without spectacle. Sometimes the greatest apostolate is a faithful, discreet visit.',
      ],
      'points' => [
        'Question: who needs my presence today — even if simple?',
        'Fruit: concrete charity and service.',
      ],
    ],
    [
      'id' => 'nativity',
      'title' => '3rd Joyful Mystery',
      'subtitle' => 'The Nativity of Jesus',
      'passageRef' => 'Lk 2:1–20',
      'passageQuote' => '“Today in the city of David a Savior has been born for you.”',
      'reflection' => [
        'God chooses to be born small. He enters not by imposition, but by closeness. The manger reveals a style: the Lord approaches our reality without demanding prior perfection.',
        'Bethlehem silently announces that God is not ashamed of human poverty: limitations, improvisations, hard nights. On the contrary, He makes His dwelling there.',
        'In this mystery, Christian joy is not euphoria — it is hope: knowing that even when conditions seem unfavorable, God is present and acting with tenderness.',
        'To pray the Nativity is to learn how to welcome Jesus rightly: with simplicity. If your heart is tired, start small — a short prayer, sincere silence, a new beginning.',
      ],
      'points' => [
        'Question: where do I need to welcome God with more simplicity?',
        'Fruit: detachment and love of humility.',
      ],
    ],
    [
      'id' => 'presentation',
      'title' => '4th Joyful Mystery',
      'subtitle' => 'The Presentation of the Lord in the Temple',
      'passageRef' => 'Lk 2:22–40 (cf. 2:22–35)',
      'passageQuote' => '“My eyes have seen your salvation.”',
      'reflection' => [
        'In the Temple, Mary and Joseph present Jesus to the Father. This gesture is living catechesis: everything we receive is a entrusted gift, not absolute possession.',
        'Simeon recognizes salvation where many would see only a baby. Faith sees beyond the obvious; it perceives God’s action even when it is small, hidden, silent.',
        'But there is also a prophecy of sorrow: the light that saves also contradicts the world. Christian joy matures when it learns to remain faithful without romanticizing the cross.',
        'To pray this mystery is to place on the altar what you love: family, projects, future — not to lose them, but to order them. When God is first, love becomes purer and freer.',
      ],
      'points' => [
        'Question: what do I need to present to God today, with trust?',
        'Fruit: purity of intention and surrender.',
      ],
    ],
    [
      'id' => 'finding',
      'title' => '5th Joyful Mystery',
      'subtitle' => 'The Finding of the Child Jesus in the Temple',
      'passageRef' => 'Lk 2:41–52',
      'passageQuote' => '“Did you not know that I must be in my Father’s house?”',
      'reflection' => [
        'This mystery touches a delicate human experience: seeking God when He seems to have withdrawn. Mary and Joseph know the anguish of not finding Jesus where they expected.',
        'The Gospel shows that searching is part of the path. There are seasons when faith is luminous; others, it is fidelity. That is where love is purified: when we keep seeking, even without consolations.',
        'When Jesus is found in the Temple, He reveals His identity and mission. It is not indifference to His parents’ pain; it is an invitation to look higher, not to reduce God to our schemes.',
        'To pray this mystery is to learn perseverance: return to the essential (the Father’s house), resume prayer with humility, seek safe direction. If today feels dry, do not conclude that God has left. Keep searching — with patience and firmness.',
      ],
      'points' => [
        'Question: where do I need to return to what is essential?',
        'Fruit: true wisdom and perseverance.',
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
    ['q' => 'On which days are the Joyful Mysteries prayed?', 'a' => 'Traditionally on Mondays and Saturdays.'],
    ['q' => 'What are the five Joyful Mysteries?', 'a' => 'Annunciation, Visitation, Nativity, Presentation, and the Finding of the Child Jesus in the Temple.'],
    ['q' => 'How can I meditate without praying on autopilot?', 'a' => 'Before each decade, announce the mystery, keep 10–20 seconds of silence, and pray the Hail Marys while holding the scene in your heart; if distracted, return calmly.'],
    ['q' => 'Can I pray the Joyful Mysteries on another day?', 'a' => 'Yes. The weekday assignment is a devotional guideline; you can choose according to your spiritual need.'],
  ];

  $articleSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $CANONICAL_URL],
    'headline' => 'Joyful Mysteries of the Rosary: Bible passages and reflections',
    'description' => 'Complete guide to the Joyful Mysteries with Scripture references, reflections for meditation, and practical guidance to pray with meaning.',
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
      ['@type' => 'Thing', 'name' => 'Joyful Mysteries'],
      ['@type' => 'Thing', 'name' => 'Rosary'],
      ['@type' => 'Thing', 'name' => 'Marian devotion'],
    ],
    'keywords' => [
      'joyful mysteries',
      'rosary mysteries',
      'monday saturday rosary',
      'how to meditate the rosary',
      'scripture for the rosary',
    ],
  ];

  $breadcrumbSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
      ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $SITE_URL.'/en'],
      ['@type' => 'ListItem', 'position' => 2, 'name' => 'Rosary', 'item' => $SITE_URL.'/en/rosary'],
      ['@type' => 'ListItem', 'position' => 3, 'name' => 'Joyful Mysteries', 'item' => $CANONICAL_URL],
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
    'name' => 'Joyful Mysteries of the Rosary',
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
    'name' => 'Joyful Mysteries of the Rosary: Bible passages and reflections',
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
    ['title' => 'Mary’s “Fiat”: meaning and spirituality (Annunciation)', 'slug' => 'mary-fiat-meaning'],
    ['title' => 'The Magnificat: joy born from gratitude (Visitation)', 'slug' => 'magnificat-explained'],
    ['title' => 'Christmas and simplicity: welcoming Jesus daily', 'slug' => 'christmas-and-simplicity'],
    ['title' => 'Simeon and Anna: waiting with fidelity (Presentation)', 'slug' => 'simeon-and-anna'],
    ['title' => 'When it feels like I lost God: what to do (Temple)', 'slug' => 'when-it-feels-like-i-lost-god'],
  ];

  // formato aceito pelo aside: pode ser {title, slug} OU {href, title, desc}
  // vou passar como {href, title, desc} para ficar explícito
  $blogLinksAside = array_map(function($x) {
    return [
      'href' => url('/en/blog/'.$x['slug']),
      'title' => $x['title'],
      'desc' => null,
    ];
  }, $blogLinks);
@endphp

@section('html_lang', 'en')
@section('title', 'Holy Rosary: Joyful Mysteries — Bible passages and reflections')
@section('meta_description', 'Pray and meditate on the Joyful Mysteries (Monday and Saturday) with Scripture passages and reflections to contemplate the infancy of Jesus with Mary.')
@section('canonical', $CANONICAL_URL)
@section('robots', 'index,follow')

@section('hreflang')
  <link rel="alternate" hreflang="en" href="{{ $CANONICAL_URL }}"/>
  <link rel="alternate" hreflang="pt-BR" href="{{ $SITE_URL }}/santo-terco/misterios-gozosos"/>
  <link rel="alternate" hreflang="x-default" href="{{ $SITE_URL }}/santo-terco/misterios-gozosos"/>
@endsection

@section('og_title', 'Holy Rosary: Joyful Mysteries')
@section('og_description', 'Monday and Saturday: contemplate the joy of the Gospel. Pray with Scripture and meditations for each decade.')
@section('og_url', $CANONICAL_URL)
@section('og_image', $SITE_URL.'/og/terco/misterios-gozosos.png?v=1')

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
      <span class="inline-flex items-center rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-900">Monday & Saturday</span>
    </div>

    <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl">Joyful Mysteries of the Rosary</h1>

    <p class="text-base leading-relaxed text-muted-foreground sm:text-lg">
      Here you’ll find the five Joyful Mysteries with <strong>Scripture passages</strong> and reflections to pray with meaning:
      Annunciation, Visitation, Nativity, Presentation, and the Finding in the Temple.
    </p>

    <section class="rounded-2xl border border-amber-200 bg-amber-50 p-4 shadow-sm sm:p-5" aria-label="Quick answer">
      <p class="text-xs font-semibold text-amber-900">Quick answer</p>
      <div class="mt-2 space-y-2 text-sm leading-relaxed text-foreground">
        <p>
          The <strong>Joyful Mysteries</strong> are traditionally prayed on <strong>Mondays</strong> and <strong>Saturdays</strong>.
          They teach Gospel joy that begins in the simple: welcoming, serving, and persevering.
        </p>
        <p class="text-muted-foreground">
          Practical tip: before each decade, announce the mystery and keep 10–20 seconds of silence; then pray each Hail Mary holding the scene in your heart.
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
      <nav aria-label="Joyful Mysteries summary" class="rounded-2xl border border-border bg-muted/40 p-4">
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
        <h2 id="what-are">What are the Joyful Mysteries?</h2>
        <p>
          The Joyful Mysteries place us at the beginning of the Gospel: Mary’s “yes”, charity that sets out on the road,
          the birth of the Savior, the offering in the Temple, and the persevering search when it feels like Jesus has “disappeared” from our horizon.
        </p>
        <p>
          They are especially fruitful for new beginnings, gratitude, and trust: joy that is not euphoria,
          but fidelity and God’s presence in daily life.
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

      {{-- (opcional) Espaço para mais seções abaixo --}}
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
