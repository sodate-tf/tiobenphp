{{-- resources/views/terco/partials/aside.blade.php --}}
@php
  /**
   * Correções principais:
   * - EN base correto: /en/rosary (sem duplicar /en)
   * - rotas EN corretas: /en/rosary/{slug} e /en/rosary/how-to-pray-the-rosary
   * - padronização dos slots de ads: aceita adsSlotDesktop / adsSlotMobile (novos) e mantém compat com adsSlotDesktop300x250
   * - blogLinks: aceita array com {title, slug} (padrão do Next) ou {href, title, desc}
   * - definição de setKey: aceita setKey/currentSetKey e também slug (pt/en) e normaliza em gozosos/dolorosos/gloriosos/luminosos
   */

  $lang = $lang ?? (request()->is('en') || request()->is('en/*') ? 'en' : 'pt');
  $variant = $variant ?? 'desktop';
  $isEn = $lang === 'en';

  // ---- Normaliza chave do conjunto atual (gozosos/dolorosos/gloriosos/luminosos) ----
  $rawKey = $currentSetKey ?? ($setKey ?? null);

  // aceita slug "misterios-..." (PT) e "...-mysteries" (EN)
  $slugToKey = [
    // PT
    'misterios-gozosos'   => 'gozosos',
    'misterios-dolorosos' => 'dolorosos',
    'misterios-gloriosos' => 'gloriosos',
    'misterios-luminosos' => 'luminosos',
    // EN
    'joyful-mysteries'    => 'gozosos',
    'sorrowful-mysteries' => 'dolorosos',
    'glorious-mysteries'  => 'gloriosos',
    'luminous-mysteries'  => 'luminosos',
  ];

  if (is_string($rawKey) && isset($slugToKey[$rawKey])) {
    $rawKey = $slugToKey[$rawKey];
  }

  $setKey = in_array($rawKey, ['gozosos','dolorosos','gloriosos','luminosos'], true)
    ? $rawKey
    : 'luminosos';

  // ---- Ads slots (compat) ----
  // Preferência: $adsSlotDesktop e $adsSlotMobile
  // Compat legado: $adsSlotDesktop300x250 (desktop)
  $adsSlotDesktop = $adsSlotDesktop ?? ($adsSlotDesktop300x250 ?? null);
  $adsSlotMobile  = $adsSlotMobile ?? null;

  // ---- Links base ----
  $blogBase = $isEn ? '/en/blog' : '/blog';
  $hubHref  = $isEn ? '/en/rosary' : '/santo-terco';
  $howHref  = $isEn ? '/en/rosary/how-to-pray-the-rosary' : '/santo-terco/como-rezar-o-terco';

  // ---- Textos ----
  $t = $isEn ? [
    'quick' => 'Quick guide',
    'menu' => 'Rosary menu',
    'choose' => 'Pick a mystery set to pray and meditate.',
    'blog' => 'Go deeper',
    'blogHint' => 'Articles about prayer and faith.',
    'prayNow' => 'Pray now',
    'learn' => 'Learn step-by-step',
    'open' => 'Open',
    'viewAll' => 'View all',
    'what' => 'What they are',
    'how' => 'How to pray',
    'ad' => 'Advertisement',
  ] : [
    'quick' => 'Guia rápido',
    'menu' => 'Menu do Terço',
    'choose' => 'Escolha um mistério para rezar e meditar.',
    'blog' => 'Aprofundar no blog',
    'blogHint' => 'Conteúdos sobre oração e fé.',
    'prayNow' => 'Rezar agora',
    'learn' => 'Aprender o passo a passo',
    'open' => 'Abrir',
    'viewAll' => 'Ver todos',
    'what' => 'O que são',
    'how' => 'Como rezar',
    'ad' => 'Publicidade',
  ];

  // ---- Design tokens ----
  $panel  = "rounded-3xl bg-white/75 shadow-sm backdrop-blur-sm p-4 sm:p-5 border border-white/40";
  $kicker = "text-[11px] font-bold uppercase tracking-wide text-amber-700";
  $subtle = "text-sm text-slate-600";
  $title  = "text-[15px] font-extrabold text-slate-900 leading-snug";

  // ---- Tema por mistério ----
  $theme = [
    'gozosos'   => ['dot' => 'bg-rose-500/80',     'chip' => 'bg-rose-50 border-rose-200 text-rose-900'],
    'dolorosos' => ['dot' => 'bg-[#7a1f2b]/80',   'chip' => 'bg-[#fbf2f3] border-[#e7c7cc] text-[#5a151f]'],
    'gloriosos' => ['dot' => 'bg-blue-600/80',    'chip' => 'bg-blue-50 border-blue-200 text-blue-900'],
    'luminosos' => ['dot' => 'bg-sky-500/80',     'chip' => 'bg-sky-50 border-sky-200 text-sky-900'],
  ];

  $chipClass = $theme[$setKey]['chip'] ?? 'bg-amber-50 border-amber-200 text-amber-900';

  // ---- Guia rápido ----
  $guide = $isEn
    ? [
        'gozosos'   => ['label' => 'Mon & Sat', 'text' => 'Joyful Mysteries. Pray with gratitude and trust as you begin the journey.'],
        'dolorosos' => ['label' => 'Tue & Fri', 'text' => 'Sorrowful Mysteries. Unite your sufferings to Christ’s Passion with hope.'],
        'gloriosos' => ['label' => 'Wed & Sun', 'text' => 'Glorious Mysteries. Contemplate Resurrection, Heaven, and God’s victory.'],
        'luminosos' => ['label' => 'Thu',       'text' => 'Luminous Mysteries. Meditate on Christ’s light in His public ministry.'],
      ][$setKey]
    : [
        'gozosos'   => ['label' => 'Seg e Sáb', 'text' => 'Mistérios Gozosos. Reze com alegria, gratidão e confiança no início da caminhada.'],
        'dolorosos' => ['label' => 'Ter e Sex', 'text' => 'Mistérios Dolorosos. Una suas dores à Paixão de Cristo com esperança e fidelidade.'],
        'gloriosos' => ['label' => 'Qua e Dom', 'text' => 'Mistérios Gloriosos. Contemple a Ressurreição, o Céu e a vitória de Deus.'],
        'luminosos' => ['label' => 'Qui',       'text' => 'Mistérios Luminosos. Medite a luz de Cristo no anúncio do Reino e nos sacramentos.'],
      ][$setKey];

  // ---- Menu links ----
  $menuLinks = $isEn ? [
    ['href' => '/en/rosary/joyful-mysteries',     'label' => 'Joyful Mysteries',     'key' => 'gozosos'],
    ['href' => '/en/rosary/sorrowful-mysteries',  'label' => 'Sorrowful Mysteries',  'key' => 'dolorosos'],
    ['href' => '/en/rosary/glorious-mysteries',   'label' => 'Glorious Mysteries',   'key' => 'gloriosos'],
    ['href' => '/en/rosary/luminous-mysteries',   'label' => 'Luminous Mysteries',   'key' => 'luminosos'],
  ] : [
    ['href' => '/santo-terco/misterios-gozosos',   'label' => 'Mistérios Gozosos',   'key' => 'gozosos'],
    ['href' => '/santo-terco/misterios-dolorosos', 'label' => 'Mistérios Dolorosos', 'key' => 'dolorosos'],
    ['href' => '/santo-terco/misterios-gloriosos', 'label' => 'Mistérios Gloriosos', 'key' => 'gloriosos'],
    ['href' => '/santo-terco/misterios-luminosos', 'label' => 'Mistérios Luminosos', 'key' => 'luminosos'],
  ];

  // ---- Blog links: aceita [{title, slug}] ou [{href, title, desc}] ----
  $blogLinks = (isset($blogLinks) && is_array($blogLinks)) ? $blogLinks : [];

  $normalizedBlogLinks = [];
  foreach ($blogLinks as $item) {
    if (!is_array($item)) continue;

    $titleTxt = $item['title'] ?? '';
    $descTxt  = $item['desc'] ?? ($item['description'] ?? null);

    // Se vier {slug}, monta href em /blog/{slug} ou /en/blog/{slug}
    if (!empty($item['slug']) && empty($item['href'])) {
      $href = rtrim($blogBase, '/') . '/' . ltrim($item['slug'], '/');
    } else {
      $href = $item['href'] ?? '#';
    }

    $normalizedBlogLinks[] = [
      'href'  => $href,
      'title' => $titleTxt,
      'desc'  => $descTxt,
    ];
  }

  // fallback
  $defaultBlogLinks = $isEn ? [
    ['href' => "{$blogBase}/how-to-pray-the-rosary", 'title' => 'How to pray the Rosary', 'desc' => 'A practical step-by-step guide.'],
    ['href' => "{$blogBase}/rosary-mysteries",       'title' => 'Rosary mysteries',      'desc' => 'How to meditate each set.'],
    ['href' => "{$blogBase}/rosary-prayers",         'title' => 'Rosary prayers',        'desc' => 'Our Father, Hail Mary, Glory, and closing.'],
    ['href' => "{$blogBase}/promises-of-the-rosary", 'title' => 'Promises of the Rosary','desc' => 'Meaning, devotion, and perseverance.'],
  ] : [
    ['href' => "{$blogBase}/como-rezar-o-terco",     'title' => 'Como rezar o Terço',     'desc' => 'Passo a passo simples para criar constância.'],
    ['href' => "{$blogBase}/misterios-do-terco",     'title' => 'Os mistérios do Santo Terço', 'desc' => 'Entenda cada conjunto e como meditar.'],
    ['href' => "{$blogBase}/oracoes-do-terco",       'title' => 'Orações do Terço',       'desc' => 'Ave-Maria, Pai-Nosso, Glória e finais.'],
    ['href' => "{$blogBase}/promessas-do-terco",     'title' => 'Promessas do Santo Terço','desc' => 'Sentido espiritual e perseverança na oração.'],
  ];

  $effectiveBlogLinks = count($normalizedBlogLinks)
    ? array_slice($normalizedBlogLinks, 0, 4)
    : array_slice($defaultBlogLinks, 0, 4);

  // destaque do item ativo
  $activeLinkClass = "ring-2 ring-amber-200 bg-white";
@endphp

<div class="space-y-6">
  {{-- Guia rápido --}}
  <div class="{{ $panel }}">
    <div class="flex items-center justify-between gap-3">
      <p class="{{ $kicker }}">{{ $t['quick'] }}</p>
      <span class="inline-flex items-center rounded-full border px-3 py-1 text-[11px] font-semibold {{ $chipClass }}">
        {{ $guide['label'] ?? '' }}
      </span>
    </div>

    <p class="mt-3 text-xs text-slate-600">{{ $guide['text'] ?? '' }}</p>

    <div class="mt-4 grid gap-2">
      <a href="#o-que-sao" class="rounded-xl border border-white/40 bg-white/70 px-3 py-2 text-sm text-slate-600 shadow-sm transition hover:bg-white hover:text-slate-900">
        {{ $t['what'] }}
      </a>
      <a href="#como-rezar" class="rounded-xl border border-white/40 bg-white/70 px-3 py-2 text-sm text-slate-600 shadow-sm transition hover:bg-white hover:text-slate-900">
        {{ $t['how'] }}
      </a>
      <a href="#faq" class="rounded-xl border border-white/40 bg-white/70 px-3 py-2 text-sm text-slate-600 shadow-sm transition hover:bg-white hover:text-slate-900">
        FAQ
      </a>
    </div>

    <div class="mt-4 grid gap-2">
      <a href="{{ $hubHref }}" class="inline-flex items-center justify-center rounded-xl bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-amber-700">
        {{ $t['prayNow'] }}
      </a>
      <a href="{{ $howHref }}" class="inline-flex items-center justify-center rounded-xl border border-amber-200 bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-900 shadow-sm hover:bg-amber-100">
        {{ $t['learn'] }}
      </a>
    </div>
  </div>

  {{-- Menu do Terço --}}
  <div class="{{ $panel }}">
    <p class="{{ $kicker }}">{{ $t['menu'] }}</p>
    <p class="mt-2 text-xs text-slate-600">{{ $t['choose'] }}</p>

    <div class="mt-4 space-y-2">
      @foreach($menuLinks as $x)
        @php
          $isActive = ($x['key'] ?? null) === $setKey;
          $dotThis = "mt-1.5 h-2 w-2 rounded-full " . ($theme[$x['key']]['dot'] ?? 'bg-amber-500/80');
        @endphp

        <a
          href="{{ $x['href'] }}"
          class="group flex items-start gap-3 rounded-2xl bg-white/70 px-4 py-3 hover:bg-white transition {{ $isActive ? $activeLinkClass : '' }}"
          aria-current="{{ $isActive ? 'page' : 'false' }}"
        >
          <span class="{{ $dotThis }}"></span>
          <div class="min-w-0">
            <p class="{{ $title }}">{{ $x['label'] }}</p>
            <p class="mt-1 {{ $subtle }}">{{ $t['open'] }}</p>
          </div>
        </a>
      @endforeach
    </div>
  </div>

  {{-- Ads --}}
  @if($variant === 'desktop' && !empty($adsSlotDesktop))
    @if(view()->exists('ads.sidebar-desktop-300x250'))
      @include('ads.sidebar-desktop-300x250', ['slot' => $adsSlotDesktop])
    @elseif(view()->exists('ads.sidebar-desktop'))
      @include('ads.sidebar-desktop', ['slot' => $adsSlotDesktop])
    @else
      {{-- sem ads --}}
    @endif
  @endif

  @if($variant !== 'desktop' && !empty($adsSlotMobile))
    @if(view()->exists('ads.sidebar-mobile'))
      @include('ads.sidebar-mobile', ['slot' => $adsSlotMobile])
    @elseif(view()->exists('ads.sidebar-mobile-320x100'))
      @include('ads.sidebar-mobile-320x100', ['slot' => $adsSlotMobile])
    @else
      {{-- sem ads --}}
    @endif
  @endif

  {{-- Blog --}}
  <div class="{{ $panel }}">
    <p class="{{ $kicker }}">{{ $t['blog'] }}</p>
    <p class="mt-2 text-xs text-slate-600">{{ $t['blogHint'] }}</p>

    <div class="mt-4 space-y-2">
      @foreach($effectiveBlogLinks as $p)
        <a href="{{ $p['href'] ?? '#' }}" class="group flex items-start gap-3 rounded-2xl bg-white/70 px-4 py-3 hover:bg-white transition">
          <span class="mt-1.5 h-2 w-2 rounded-full bg-slate-300"></span>
          <div class="min-w-0">
            <p class="{{ $title }}">{{ $p['title'] ?? '' }}</p>
            @if(!empty($p['desc']))
              <p class="mt-1 {{ $subtle }}">{{ $p['desc'] }}</p>
            @endif
          </div>
        </a>
      @endforeach

      <a href="{{ $blogBase }}" class="group flex items-start gap-3 rounded-2xl bg-white/70 px-4 py-3 hover:bg-white transition">
        <span class="mt-1.5 h-2 w-2 rounded-full bg-amber-400/90"></span>
        <div class="min-w-0">
          <p class="{{ $title }}">{{ $t['viewAll'] }}</p>
          <p class="mt-1 {{ $subtle }}">{{ $t['blogHint'] }}</p>
        </div>
      </a>
    </div>
  </div>
</div>
