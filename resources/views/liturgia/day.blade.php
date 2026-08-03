{{-- resources/views/liturgia/day.blade.php --}}

@extends('layouts.site')

@section('html_lang', $meta['html_lang'] ?? 'pt-BR')
@section('title', $meta['title'] ?? 'Liturgia Diária — IA Tio Ben')
@section('meta_description', $meta['description'] ?? '')
@section('canonical', $meta['canonical'] ?? '')
@section('robots', $meta['robots'] ?? 'index,follow')

@section('hreflang')
  @if(!empty($meta['hreflangs']) && is_array($meta['hreflangs']))
    @foreach($meta['hreflangs'] as $lang => $url)
      @if(!empty($lang) && !empty($url))
        <link rel="alternate" hreflang="{{ $lang }}" href="{{ $url }}"/>
      @endif
    @endforeach
  @endif
@endsection

@section('og_title', $meta['og_title'] ?? ($meta['title'] ?? ''))
@section('og_description', $meta['og_description'] ?? ($meta['description'] ?? ''))
@section('og_url', $meta['og_url'] ?? ($meta['canonical'] ?? ''))
@section('og_image', $meta['og_image'] ?? '')

@push('head')
  @if(!empty($meta['jsonld_blocks']) && is_array($meta['jsonld_blocks']))
    @foreach($meta['jsonld_blocks'] as $b)
      @if(!empty($b['id']) && !empty($b['json']))
        <script id="{{ $b['id'] }}" type="application/ld+json">{!! json_encode($b['json'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
      @endif
    @endforeach
  @endif

  <link rel="preconnect" href="https://pagead2.googlesyndication.com" crossorigin>
  <link rel="preconnect" href="https://googleads.g.doubleclick.net" crossorigin>
  <link rel="preconnect" href="https://tpc.googlesyndication.com" crossorigin>

  {{-- AdSense sem atraso --}}
  <style>
    .lit-day-grid{ display:block; }

    @media (min-width: 1024px){
      .lit-day-grid{
        display:grid;
        grid-template-columns:minmax(0,1fr) 340px;
        gap:20px;
        align-items:start;
      }
      .lit-aside{ position:sticky; top:20px; }
    }

    .lit-surface{ background:#fff; }

    .lit-bible{
      font-family: var(--font-sans);
      font-size: 18px;
      line-height: 1.85;
      color:#0f172a;
      text-rendering: optimizeLegibility;
    }

    .lit-bible p{ margin:0.8em 0; }

    .lit-bible h2,.lit-bible h3,.lit-bible h4{
      font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji","Segoe UI Emoji";
      letter-spacing:-0.01em;
    }

    .lit-content-ad-reserve{ min-height:120px; }

    .lit-card{
      background:#fff;
      border:1px solid #e2e8f0;
      border-radius:8px;
    }

    .lit-chip{
      display:inline-flex;
      align-items:center;
      border:1px solid #e2e8f0;
      border-radius:999px;
      padding:4px 10px;
      font-weight:600;
      font-size:12px;
      color:#334155;
      background:#fff;
    }

    .lit-chip-amber{
      border-color:#fcd34d;
      background:#fffbeb;
      color:#92400e;
    }

    .lit-btn{
      display:inline-flex;
      justify-content:center;
      align-items:center;
      padding:8px 12px;
      border:1px solid #e2e8f0;
      border-radius:8px;
      background:#fff;
      color:#0f172a;
      font-weight:700;
      font-size:14px;
      line-height:1;
      text-decoration:none;
    }

    .lit-btn:hover{ background:#f8fafc; }

    .lit-btn-primary{
      border-color:#d97706;
      background:#d97706;
      color:#fff;
    }

    .lit-btn-primary:hover{
      background:#b45309;
      border-color:#b45309;
    }

    .lit-toolbar{
      display:flex;
      flex-wrap:wrap;
      align-items:center;
      justify-content:space-between;
      gap:10px;
      padding:10px 12px;
      border:1px solid #e2e8f0;
      border-radius:8px;
      background:#fff;
    }

    .lit-input{
      border:1px solid #e2e8f0;
      border-radius:8px;
      padding:6px 10px;
      font-weight:700;
      font-size:14px;
      color:#0f172a;
      background:#fff;
    }

    .lit-mini-btn{
      border:1px solid #e2e8f0;
      border-radius:8px;
      padding:6px 10px;
      background:#fff;
      font-weight:700;
      color:#0f172a;
      font-size:13px;
      line-height:1;
    }

    .lit-mini-btn:hover{ background:#f8fafc; }

    .lit-mini-btn-primary{
      border-color:#d97706;
      background:#d97706;
      color:#fff;
    }

    .lit-mini-btn-primary:hover{
      background:#b45309;
      border-color:#b45309;
    }

    .lit-tabs-row{
      display:flex;
      gap:8px;
      padding-bottom:8px;
      padding-top:6px;
    }

    .lit-tab{
      border:1px solid #e2e8f0;
      border-radius:999px;
      padding:8px 14px;
      font-weight:800;
      font-size:13px;
      background:#fff;
      color:#334155;
      white-space:nowrap;
    }

    .lit-tab:hover{ background:#f8fafc; }

    .lit-tab-active{
      border-color:#d97706;
      background:#d97706;
      color:#fff;
    }

    .lit-panel{
      border:1px solid #e2e8f0;
      border-radius:8px;
      overflow:hidden;
      background:#fff;
    }

    .lit-bar{ height:6px; background:#94a3b8; }
    .lit-bar-reading{ background:#6366f1; }
    .lit-bar-psalm{ background:#10b981; }
    .lit-bar-gospel{ background:#f43f5e; }
    .lit-bar-antiphons{ background:#f59e0b; }
    .lit-bar-prayers{ background:#0ea5e9; }
    .lit-bar-extras{ background:#94a3b8; }

    .ads-slot{
      width:100%;
      min-height:120px;
    }

    .ads-slot-sidebar-top{
      min-height:250px;
    }

    .ads-slot-sidebar-bottom{
      min-height:600px;
    }

    @media (min-width: 1024px){
      .lit-aside summary{ display:none; }
      .lit-aside details{ border:none; padding:0; }
    }
  </style>
@endpush

@section('content')
@php
  $siteUrl     = config('app.url') ?: url('/');
  $dateSlug    = $page['dateSlug'] ?? '';
  $dateISO     = $page['dateISO'] ?? '';
  $celebration = $page['celebration'] ?? '';
  $color       = $page['color'] ?? '';
  $pageUrl     = rtrim($siteUrl,'/').'/liturgia-diaria/'.$dateSlug;

  $lang = 'pt';

  $slugFromIso = function(string $iso) {
    $c = \Carbon\Carbon::parse($iso, 'America/Sao_Paulo');
    return $c->format('d-m-Y');
  };

  $todayISO   = \Carbon\Carbon::today('America/Sao_Paulo')->toDateString();
  $todaySlug  = $todaySlug ?? $slugFromIso($todayISO);
  $todayLabel = $todayLabel ?? \Carbon\Carbon::parse($todayISO, 'America/Sao_Paulo')->format('d/m/Y');

  $baseISO  = $dateISO ?: $todayISO;
  $prevSlug = $prevSlug ?? $slugFromIso(\Carbon\Carbon::parse($baseISO, 'America/Sao_Paulo')->subDay()->toDateString());
  $nextSlug = $nextSlug ?? $slugFromIso(\Carbon\Carbon::parse($baseISO, 'America/Sao_Paulo')->addDay()->toDateString());

  $carbon = $dateISO
    ? \Carbon\Carbon::parse($dateISO, 'America/Sao_Paulo')
    : \Carbon\Carbon::today('America/Sao_Paulo');

  $year   = (int) $carbon->format('Y');
  $month  = (int) $carbon->format('m');

  $tabs = [];

  $leituras = $page['leiturasFull'] ?? [];
  $oracoes  = $page['oracoesFull'] ?? [];
  $antif    = $page['antifonasFull'] ?? [];

  $primeiras = is_array($leituras['primeiraLeitura'] ?? null) ? $leituras['primeiraLeitura'] : [];
  $segundas  = is_array($leituras['segundaLeitura'] ?? null) ? $leituras['segundaLeitura'] : [];
  $salmos    = is_array($leituras['salmo'] ?? null) ? $leituras['salmo'] : [];
  $evangs    = is_array($leituras['evangelho'] ?? null) ? $leituras['evangelho'] : [];
  $extras    = is_array($leituras['extras'] ?? null) ? $leituras['extras'] : [];

  $readingGroups = [];

  if (count($primeiras)) {
    $readingGroups[] = ['label'=>'1ª Leitura','items'=>$primeiras];
  }

  if (count($segundas)) {
    $readingGroups[] = ['label'=>'2ª Leitura','items'=>$segundas];
  }

  $leituraExtras = array_values(array_filter($extras, function($x){
    $tipo = mb_strtolower((string)($x['tipo'] ?? ''));
    return str_contains($tipo, 'leitura') || str_contains($tipo, 'epístola') || str_contains($tipo, 'epistola');
  }));

  foreach ($leituraExtras as $idx => $x) {
    $label = (string)($x['tipo'] ?? $x['titulo'] ?? ('Leitura '.($idx+3)));
    $readingGroups[] = ['label'=>$label,'items'=>[$x]];
  }

  $max = max(count($readingGroups), count($salmos));

  for ($i=0; $i<$max; $i++) {
    if (isset($readingGroups[$i])) {
      $tabs[] = [
        'id'=>'reading-'.($i+1),
        'label'=>$readingGroups[$i]['label'],
        'kind'=>'reading',
        'payload'=>$readingGroups[$i],
      ];
    }

    if (isset($salmos[$i])) {
      $tabs[] = [
        'id'=>'psalm-'.($i+1),
        'label'=>(count($salmos)>1 ? 'Salmo '.($i+1) : 'Salmo'),
        'kind'=>'psalm',
        'payload'=>$salmos[$i],
      ];
    }
  }

  if (count($evangs)) {
    $tabs[] = [
      'id'=>'evangelho',
      'label'=>'Evangelho',
      'kind'=>'gospel',
      'payload'=>$evangs,
    ];
  }

  $outrosExtras = array_values(array_filter($extras, function($x){
    $tipo = mb_strtolower((string)($x['tipo'] ?? ''));
    return !(str_contains($tipo, 'leitura') || str_contains($tipo, 'epístola') || str_contains($tipo, 'epistola'));
  }));

  if (count($outrosExtras)) {
    $tabs[] = [
      'id'=>'extras',
      'label'=>'Extras',
      'kind'=>'extras',
      'payload'=>$outrosExtras,
    ];
  }

  $hasText = function($x) {
    if (is_string($x)) return trim($x) !== '';
    if (!is_array($x)) return false;

    $t = trim((string)($x['texto'] ?? $x['text'] ?? ''));
    $h = trim((string)($x['textoHtml'] ?? $x['html'] ?? ''));

    return ($h !== '') || ($t !== '');
  };

  $hasAntEntrada = $hasText($antif['entrada'] ?? '');
  $hasAntCom     = $hasText($antif['comunhao'] ?? '');

  if ($hasAntEntrada || $hasAntCom) {
    $tabs[] = [
      'id'=>'antifonas',
      'label'=>'Antífonas',
      'kind'=>'antiphons',
      'payload'=>$antif,
    ];
  }

  $hasPrayers = false;

  if (!empty($oracoes['coleta']) || !empty($oracoes['oferendas']) || !empty($oracoes['comunhao'])) {
    $hasPrayers = true;
  }

  if (is_array($oracoes['extras'] ?? null) && count($oracoes['extras'])) {
    $hasPrayers = true;
  }

  if ($hasPrayers) {
    $tabs[] = [
      'id'=>'oracoes',
      'label'=>'Orações',
      'kind'=>'prayers',
      'payload'=>$oracoes,
    ];
  }

  $defaultTab = $tabs[0]['id'] ?? '';

  $dateHumanPt = $dateISO
    ? \Carbon\Carbon::parse($dateISO, 'America/Sao_Paulo')->locale('pt_BR')->translatedFormat('j \d\e F \d\e Y')
    : \Carbon\Carbon::parse($todayISO, 'America/Sao_Paulo')->locale('pt_BR')->translatedFormat('j \d\e F \d\e Y');

  $defaultBlogLinks = [
    ['href' => url('/blog/como-usar-a-liturgia'), 'title' => 'Como usar a Liturgia no dia a dia', 'desc' => 'Um jeito simples de rezar e se preparar para a Missa.'],
    ['href' => url('/blog/ano-liturgico'), 'title' => 'Ano litúrgico: tempos, cores e calendário', 'desc' => 'Entenda o que muda ao longo do ano e como acompanhar.'],
    ['href' => url('/blog/leituras-da-missa'), 'title' => 'Guia das leituras da Missa', 'desc' => 'Primeira leitura, salmo, evangelho e como acompanhar.'],
    ['href' => url('/blog/como-rezar-com-a-liturgia-em-5-minutos'), 'title' => 'Como rezar com a Liturgia em 5 minutos', 'desc' => 'Um passo a passo prático para criar rotina e constância.'],
    ['href' => url('/blog/liturgia-diaria-ou-evangelho-do-dia'), 'title' => 'Liturgia diária x Evangelho do dia: qual a diferença?', 'desc' => 'Entenda o que cada um inclui e quando usar.'],
  ];

  $effectiveBlogLinks = (!empty($blogLinks) && is_array($blogLinks)) ? $blogLinks : $defaultBlogLinks;
  $effectiveBlogLinks = array_slice($effectiveBlogLinks, 0, 5);

  $pageSlug = $dateSlug ?: null;

  $adClient = 'ca-pub-8819996017476509';

  $slotTop = $ads['slot_lit_day_top_responsive']
    ?? ($ads['slot_content_responsive'] ?? '8534838745');

  $slotMiddle = $ads['slot_lit_day_middle_responsive']
    ?? ($ads['slot_in_article'] ?? '5469336488');

  $slotBottom = $ads['slot_lit_day_bottom_responsive']
    ?? ($ads['slot_blog_bottom_banner'] ?? '6552840528');

  $slotAsideTop = $ads['slot_lit_day_sidebar_300x250']
    ?? ($ads['slot_sidebar_300x250'] ?? ($ads['slot_desktop'] ?? '8534838745'));

  $slotAsideBottom = $ads['slot_lit_day_sidebar_300x600']
    ?? ($ads['slot_sidebar_300x600'] ?? '9515073457');
@endphp

<article class="lit-surface mx-auto w-full max-w-7xl px-3 sm:px-4 lg:px-6 py-4 sm:py-6 text-slate-900 leading-relaxed">
  <div class="lit-day-grid gap-5 items-start">

    {{-- MAIN --}}
    <section class="min-w-0">

      {{-- TOP HEADER --}}
      <header class="mb-6 sm:mb-8 mt-2">
        <p class="text-xs font-semibold tracking-wide text-amber-700 uppercase">
          IA Tio Ben • Liturgia
        </p>

        <h1 class="mt-2 text-2xl sm:text-4xl font-extrabold tracking-tight">
          Liturgia diária de {{ $dateHumanPt }}: Evangelho e Leituras da Missa
        </h1>

        <div class="mt-2 flex flex-wrap items-center gap-2">
          @if(!empty($celebration))
            <span class="lit-chip">{{ $celebration }}</span>
          @endif

          @if(!empty($color))
            <span class="lit-chip lit-chip-amber">Cor: {{ $color }}</span>
          @endif
        </div>

        @if(!empty($dailyParagraph))
          <p class="mt-3 text-[15px] leading-7 text-slate-700">
            {{ $dailyParagraph }}
          </p>
        @endif

        @include('liturgia.partials.mobile-beta-banner')


        <nav class="mt-4 grid grid-cols-3 gap-2 sm:flex sm:flex-wrap sm:gap-2" aria-label="Navegação por datas">
          <a href="{{ url('/liturgia-diaria/'.$prevSlug) }}" class="lit-btn">Ontem</a>
          <a href="{{ url('/liturgia-diaria/'.$todaySlug) }}" class="lit-btn lit-btn-primary">Hoje</a>
          <a href="{{ url('/liturgia-diaria/'.$nextSlug) }}" class="lit-btn">Amanhã</a>
        </nav>

        @if(!empty($blogComplement))
          <section class="mt-5 lit-card p-4 sm:p-5">
            <p class="text-[11px] font-semibold text-amber-700 uppercase tracking-wide">
              Reflexão do Blog IA Tio Ben
            </p>

            <h2 class="mt-1 text-base sm:text-lg font-extrabold text-slate-900">
              {{ $blogComplement['title'] ?? '' }}
            </h2>

            <p class="mt-2 text-sm sm:text-[15px] leading-7 text-slate-700">
              {{ $blogComplement['paragraph'] ?? '' }}
            </p>

            <a href="{{ url('/blog/'.($blogComplement['slug'] ?? '')) }}"
               class="mt-3 inline-flex text-sm font-semibold text-amber-700 hover:text-amber-800">
              Aprofundar a reflexão no blog →
            </a>
          </section>
        @endif
      </header>

      {{-- ANÚNCIO REAL - TOPO --}}
      <section class="mb-6">
        <div class="ads-slot rounded-xl border border-slate-200 bg-white p-4">
          <p class="text-xs font-semibold tracking-wide text-amber-700 uppercase">Anúncio</p>

          <div class="mt-3">
            <ins class="adsbygoogle"
                 style="display:block; width:100%;"
                 data-ad-client="{{ $adClient }}"
                 data-ad-slot="{{ $slotTop }}"
                 data-ad-format="auto"
                 data-full-width-responsive="true"></ins>
          </div>

          <script>
            try {
              (window.adsbygoogle = window.adsbygoogle || []).push({});
            } catch (e) {}
          </script>
        </div>
      </section>

      {{-- CARD CONTROLES + TABS --}}
      <section
        class="lit-card p-4 sm:p-5"
        data-page-url="{{ $pageUrl }}"
        data-default-tab="{{ $defaultTab }}"
        data-date-iso="{{ $dateISO ?: $todayISO }}"
        data-date-human="{{ $dateHumanPt }}"
        data-celebration="{{ $celebration ?: 'Missa do dia' }}"
      >
        <div class="lit-toolbar mb-4">
          <div class="flex items-center gap-2">
            <span class="text-xs font-semibold text-slate-600">Data</span>

            <input
              id="lit-date"
              type="date"
              value="{{ $dateISO ?: $todayISO }}"
              class="lit-input"
              aria-label="Selecionar data"
            />
          </div>

          <div class="flex items-center gap-2">
            <button id="font-minus" type="button" class="lit-mini-btn" aria-label="Diminuir fonte">A−</button>
            <button id="font-reset" type="button" class="lit-mini-btn" aria-label="Fonte padrão">Padrão</button>
            <button id="font-plus" type="button" class="lit-mini-btn" aria-label="Aumentar fonte">A+</button>

            <button id="share-btn" type="button" class="lit-mini-btn lit-mini-btn-primary" aria-label="Compartilhar">
              Compartilhar
            </button>
          </div>
        </div>

        <div class="mt-2">
          <p class="text-sm font-semibold text-slate-800">
            Leituras, Salmos e Evangelho
          </p>

          <div class="mt-3 -mx-1 px-1 pt-1 overflow-x-auto overflow-y-visible">
            <div class="lit-tabs-row snap-x snap-mandatory overflow-visible">
              @foreach($tabs as $t)
                @php $isDefaultBtn = (($t['id'] ?? '') === ($defaultTab ?? '')); @endphp

                <button
                  type="button"
                  class="tab-btn lit-tab snap-start shrink-0 {{ $isDefaultBtn ? 'lit-tab-active' : '' }}"
                  data-tab="{{ $t['id'] }}"
                  aria-selected="{{ $isDefaultBtn ? 'true' : 'false' }}"
                >
                  {{ $t['label'] }}
                </button>
              @endforeach
            </div>
          </div>
        </div>

        <div class="mt-5" id="tab-panels">
          @foreach($tabs as $t)
            @php
              $kind = $t['kind'] ?? 'reading';

              $barClass = match($kind) {
                'gospel' => 'lit-bar-gospel',
                'psalm' => 'lit-bar-psalm',
                'reading' => 'lit-bar-reading',
                'antiphons' => 'lit-bar-antiphons',
                'prayers' => 'lit-bar-prayers',
                'extras' => 'lit-bar-extras',
                default => '',
              };

              $isDefault = (($t['id'] ?? '') === ($defaultTab ?? ''));
            @endphp

            <div class="tab-panel {{ $isDefault ? '' : 'hidden' }}" data-panel="{{ $t['id'] }}">
              <div class="lit-panel">
                <div class="lit-bar {{ $barClass }}"></div>

                <div class="p-4 sm:p-5">
                  <div class="lit-bible max-w-[68ch] mx-auto" data-font-body>
                    @if($t['kind'] === 'reading')
                      @php $items = $t['payload']['items'] ?? []; $cur = $items[0] ?? []; @endphp

                      @include('liturgia.partials.card-reading', [
                        'label' => $t['payload']['label'] ?? 'Leitura',
                        'ref' => $cur['referencia'] ?? null,
                        'subtitle' => $cur['titulo'] ?? null,
                        'html' => $cur['textoHtml'] ?? null,
                        'text' => $cur['texto'] ?? null,
                        'kind' => 'reading',
                      ])
                    @elseif($t['kind'] === 'psalm')
                      @php $ps = $t['payload'] ?? []; @endphp

                      @include('liturgia.partials.card-psalm', [
                        'ref' => $ps['referencia'] ?? null,
                        'refrao' => $ps['refrao'] ?? null,
                        'html' => $ps['textoHtml'] ?? null,
                        'text' => $ps['texto'] ?? null,
                      ])
                    @elseif($t['kind'] === 'gospel')
                      @php $items = $t['payload'] ?? []; $cur = $items[0] ?? []; @endphp

                      @include('liturgia.partials.card-reading', [
                        'label' => 'Evangelho',
                        'ref' => $cur['referencia'] ?? null,
                        'subtitle' => $cur['titulo'] ?? 'Proclamação do Evangelho',
                        'html' => $cur['textoHtml'] ?? null,
                        'text' => $cur['texto'] ?? null,
                        'kind' => 'gospel',
                      ])
                    @elseif($t['kind'] === 'extras')
                      @php $items = $t['payload'] ?? []; @endphp

                      <div class="space-y-4">
                        @foreach($items as $idx => $x)
                          @include('liturgia.partials.card-extra', [
                            'title' => (string)($x['titulo'] ?? $x['tipo'] ?? ('Extra '.($idx+1))),
                            'ref' => $x['referencia'] ?? null,
                            'html' => $x['textoHtml'] ?? null,
                            'text' => $x['texto'] ?? null,
                          ])
                        @endforeach
                      </div>
                    @elseif($t['kind'] === 'antiphons')
                      @php $a = $t['payload'] ?? []; @endphp

                      @include('liturgia.partials.card-antiphons', [
                        'entradaHtml' => $a['entradaHtml'] ?? null,
                        'entradaText' => $a['entrada'] ?? null,
                        'comunhaoHtml' => $a['comunhaoHtml'] ?? null,
                        'comunhaoText' => $a['comunhao'] ?? null,
                      ])
                    @elseif($t['kind'] === 'prayers')
                      @php $o = $t['payload'] ?? []; @endphp

                      @include('liturgia.partials.card-prayers', ['o' => $o])
                    @endif
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </section>

      {{-- ANÚNCIO REAL - MEIO / IN-ARTICLE --}}

      {{-- ANÚNCIO REAL - FINAL --}}
      <section class="mt-8">
        <div class="ads-slot rounded-xl border border-slate-200 bg-white p-4">
          <p class="text-xs font-semibold tracking-wide text-amber-700 uppercase">Publicidade</p>

          <div class="mt-3">
            <ins class="adsbygoogle"
                 style="display:block; width:100%;"
                 data-ad-client="{{ $adClient }}"
                 data-ad-slot="{{ $slotBottom }}"
                 data-ad-format="auto"
                 data-full-width-responsive="true"></ins>
          </div>

          <script>
            try {
              (window.adsbygoogle = window.adsbygoogle || []).push({});
            } catch (e) {}
          </script>
        </div>
      </section>

      <section class="mt-8 rounded-2xl border border-amber-100 bg-amber-50/60 p-5" aria-labelledby="liturgia-guides-title">
        <p class="text-xs font-bold uppercase tracking-wide text-amber-700">Aprofunde a sua oração</p>
        <h2 id="liturgia-guides-title" class="mt-1 text-xl font-extrabold text-slate-900">Guias para rezar com a Liturgia</h2>
        <p class="mt-2 text-sm leading-6 text-slate-700">Entenda cada parte das leituras e encontre um caminho simples para transformar a Palavra em oração diária.</p>
        <div class="mt-4 grid gap-2 sm:grid-cols-2">
          <a href="{{ url('/guias/liturgia-diaria') }}" class="rounded-xl bg-white px-3 py-3 text-sm font-bold text-slate-800 ring-1 ring-slate-200 hover:ring-amber-300">Como acompanhar a Liturgia Diária</a>
          <a href="{{ url('/guias/evangelho-do-dia') }}" class="rounded-xl bg-white px-3 py-3 text-sm font-bold text-slate-800 ring-1 ring-slate-200 hover:ring-amber-300">Como rezar o Evangelho do dia</a>
          <a href="{{ url('/guias/salmo-do-dia') }}" class="rounded-xl bg-white px-3 py-3 text-sm font-bold text-slate-800 ring-1 ring-slate-200 hover:ring-amber-300">Como rezar o Salmo responsorial</a>
          <a href="{{ url('/guias/calendario-liturgico') }}" class="rounded-xl bg-white px-3 py-3 text-sm font-bold text-slate-800 ring-1 ring-slate-200 hover:ring-amber-300">Calendário litúrgico e ciclos da Igreja</a>
        </div>
        <a href="{{ url('/santo-terco') }}" class="mt-4 inline-flex text-sm font-bold text-amber-800 hover:text-amber-950">Continue a oração com o Santo Terço →</a>
      </section>
      <footer class="mt-8 border-t border-slate-200 pt-6">
        <p class="text-xs text-slate-500 break-words">
          Compartilhe a Liturgia e ajude alguém a rezar hoje.
        </p>
      </footer>
    </section>

    <aside class="lit-aside min-w-0">
      <details class="lit-card p-4" open>
        <div class="mt-3">
          @include('liturgia.partials.aside', [
            'year' => $year,
            'month' => $month,
            'todaySlug' => $todaySlug,
            'todayLabel' => $todayLabel,
            'prevSlug' => $prevSlug,
            'nextSlug' => $nextSlug,
            'adsSlotDesktop300x250' => $slotAsideTop,
            'adsSlotDesktopSticky' => $slotAsideBottom,
            'blogLinks' => $effectiveBlogLinks,
            'variant' => 'desktop',
            'pageSlug' => $pageSlug,
            'lang' => $lang,
          ])
        </div>
      </details>
    </aside>

  </div>
</article>
@endsection

@push('scripts')
<script>
(function(){
  const root = document.querySelector('[data-default-tab]');
  if(!root) return;

  const contentAdClient = @json($adClient);
  const contentAdSlot = @json($slotMiddle);


  const pageUrl = root.getAttribute('data-page-url') || window.location.href;
  const dateHuman = root.getAttribute('data-date-human') || '';
  const celebration = root.getAttribute('data-celebration') || 'Missa do dia';

  const fontBodies = Array.from(document.querySelectorAll('#tab-panels [data-font-body]'));

  let fontStep = 0;

  const clamp = (n,min,max)=>Math.max(min,Math.min(max,n));
  const getFontPx = ()=> clamp(17 + fontStep * 1, 15, 23);

  function applyFont(){
    const px = getFontPx();

    for (const el of fontBodies){
      el.style.fontSize = px+'px';
      el.style.lineHeight = '1.85';
    }
  }

  function scheduleContentAd(tabId){ const panel = document.querySelector(`[data-panel="${tabId}"] .lit-panel`); if (!panel || panel.dataset.contentAdLoaded === "1") return; panel.dataset.contentAdLoaded = "1"; const target = document.createElement("div"); target.className = "lit-content-ad-target"; target.style.minHeight = "1px"; panel.appendChild(target); const observer = new IntersectionObserver((entries) => { if (!entries.some(entry => entry.isIntersecting)) return; observer.disconnect(); const section = document.createElement("section"); section.className = "lit-content-ad-reserve rounded-xl border border-slate-200 bg-white p-4 mt-8"; section.setAttribute("aria-label", "Publicidade"); section.innerHTML = `<p class="text-xs font-semibold tracking-wide text-amber-700 uppercase">Publicidade</p>`; const holder = document.createElement("div"); holder.className = "mt-3"; const ins = document.createElement("ins"); ins.className = "adsbygoogle"; ins.style.display = "block"; ins.style.width = "100%"; ins.dataset.adClient = contentAdClient; ins.dataset.adSlot = contentAdSlot; ins.dataset.adFormat = "fluid"; ins.dataset.adLayout = "in-article"; holder.appendChild(ins); section.appendChild(holder); target.replaceWith(section); try { (window.adsbygoogle = window.adsbygoogle || []).push({}); } catch (e) {} }, { threshold: 0.25 }); observer.observe(target); }

  function setActive(tabId){
    document.querySelectorAll('.tab-panel').forEach(p=>{
      p.classList.toggle('hidden', p.getAttribute('data-panel') !== tabId);
    });

    document.querySelectorAll('.tab-btn').forEach(b=>{
      const on = b.getAttribute('data-tab') === tabId;
      b.setAttribute('aria-selected', on ? 'true' : 'false');
      b.className = on
        ? "tab-btn lit-tab lit-tab-active snap-start shrink-0"
        : "tab-btn lit-tab snap-start shrink-0";
    });

    scheduleContentAd(tabId);
  }

  const tabsRow = document.querySelector('.lit-tabs-row');

  tabsRow && tabsRow.addEventListener('click', (e)=>{
    const btn = e.target.closest && e.target.closest('.tab-btn');
    if(!btn) return;

    const id = btn.getAttribute('data-tab');

    if(id) setActive(id);
  });

  scheduleContentAd(root.getAttribute("data-default-tab") || "");

  const minus = document.getElementById('font-minus');
  const plus  = document.getElementById('font-plus');
  const reset = document.getElementById('font-reset');

  minus && minus.addEventListener('click', ()=>{
    fontStep = clamp(fontStep-1, -2, 6);
    applyFont();
  });

  plus && plus.addEventListener('click', ()=>{
    fontStep = clamp(fontStep+1, -2, 6);
    applyFont();
  });

  reset && reset.addEventListener('click', ()=>{
    fontStep = 0;
    applyFont();
  });

  applyFont();

  function goToDate(){
    const input = document.getElementById('lit-date');
    if(!input) return;

    const iso = input.value;
    if(!iso) return;

    const parts = iso.split('-');
    if(parts.length !== 3) return;

    const [y,m,d] = parts;

    if(!y || !m || !d) return;

    const slug = `${d}-${m}-${y}`;

    window.location.href = `/liturgia-diaria/${slug}`;
  }

  const input = document.getElementById('lit-date');

  input && input.addEventListener('change', goToDate);

  const shareBtn = document.getElementById('share-btn');

  async function doShare(){
    const shareTitle = `Liturgia diária de ${dateHuman}: Evangelho e Leituras da Missa`;
    const shareText =
      `Liturgia de ${dateHuman} (${celebration}). ` +
      `Leia o Evangelho e as leituras completas aqui:`;

    try{
      if(navigator.share){
        await navigator.share({
          title: shareTitle,
          text: shareText,
          url: pageUrl
        });
        return;
      }

      if(navigator.clipboard && navigator.clipboard.writeText){
        await navigator.clipboard.writeText(`${shareText}\n${pageUrl}`);

        if(shareBtn) shareBtn.textContent = "Copiado ✓";

        setTimeout(()=>{
          if(shareBtn) shareBtn.textContent="Compartilhar";
        }, 1600);

        return;
      }

      window.prompt("Copie e compartilhe:", `${shareText}\n${pageUrl}`);
    }catch(e){}
  }

  shareBtn && shareBtn.addEventListener('click', doShare);
})();
</script>
@endpush