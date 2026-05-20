{{-- resources/views/liturgia/month.blade.php --}}
@extends('layouts.site')

@section('html_lang', $meta['html_lang'] ?? 'pt-BR')
@section('title', $meta['title'] ?? 'Liturgia Diária — IA Tio Ben')
@section('meta_description', $meta['description'] ?? '')
@section('canonical', $meta['canonical'] ?? '')
@section('robots', $meta['robots'] ?? 'index,follow')

@section('hreflang')
  @if(!empty($meta['hreflangs']) && is_array($meta['hreflangs']))
    @foreach($meta['hreflangs'] as $langCode => $url)
      @if(!empty($langCode) && !empty($url))
        <link rel="alternate" hreflang="{{ $langCode }}" href="{{ $url }}"/>
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

  {{-- AdSense carregado sem atraso --}}
  <script async
    src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-8819996017476509"
    crossorigin="anonymous"></script>

  <style>
    .lit-grid { display:block; }
    .lit-aside-desktop { display:none; }
    .lit-aside-mobile { display:block; }

    @media (min-width: 1024px) {
      .lit-grid { display:grid; grid-template-columns:minmax(0,1fr) 320px; gap:24px; align-items:start; }
      .lit-aside-desktop { display:block; }
      .lit-aside-mobile { display:none; }
    }

    .lit-surface{
      background: linear-gradient(180deg, rgba(255,251,235,0.70) 0%, rgba(255,255,255,1) 55%);
      border-radius: 24px;
    }

    .ads-slot { width:100%; }
    .ads-slot-content { min-height:280px; }
    .ads-slot-mobile { min-height:280px; }
    .ads-slot-bottom { min-height:280px; }
    .ad-container { width:100%; }

    .cal-grid { display:grid; grid-template-columns: repeat(7, minmax(0, 1fr)); }
    .cal-cell { min-height: 64px; }
    @media (min-width: 640px) { .cal-cell { min-height: 92px; } }
  </style>
@endpush

@section('content')
@php
  $siteUrl = rtrim(config('app.url') ?: url('/'), '/');

  $dow = ['Seg','Ter','Qua','Qui','Sex','Sáb','Dom'];

  $prevHref = "/liturgia-diaria/ano/{$prevMonth['year']}/".str_pad($prevMonth['month'], 2, '0', STR_PAD_LEFT);
  $nextHref = "/liturgia-diaria/ano/{$nextMonth['year']}/".str_pad($nextMonth['month'], 2, '0', STR_PAD_LEFT);
  $todayHref = "/liturgia-diaria/{$todaySlug}";

  $ptSwitch = $ptHref ?? '/'.ltrim(request()->path(), '/');
  $enSwitch = $enHref ?? null;

  $prevLabel = ucfirst(\Carbon\Carbon::create($prevMonth['year'], $prevMonth['month'], 1)->locale('pt_BR')->translatedFormat('F'))." {$prevMonth['year']}";
  $nextLabel = ucfirst(\Carbon\Carbon::create($nextMonth['year'], $nextMonth['month'], 1)->locale('pt_BR')->translatedFormat('F'))." {$nextMonth['year']}";

  $adClient = 'ca-pub-8819996017476509';

  $slotTopAllDevices = $ads['slot_lit_month_top_responsive']
    ?? ($ads['slot_content_responsive'] ?? '8534838745');

  $slotMobileMid = $ads['slot_lit_month_mobile_mid']
    ?? ($ads['slot_blog_mobile_mid'] ?? '9515073457');

  $slotMobileInfeed1 = $ads['slot_lit_month_mobile_infeed_1']
    ?? ($ads['slot_blog_infeed_1'] ?? '4921222321');

  $slotBottomAllDevices = $ads['slot_lit_month_bottom_responsive']
    ?? ($ads['slot_blog_bottom_banner'] ?? '6552840528');

  $slotAsideTop = $ads['slot_lit_month_sidebar_300x250']
    ?? ($ads['slot_aside_300x250'] ?? '8534838745');

  $slotAsideBottom = $ads['slot_lit_month_sidebar_300x600']
    ?? ($ads['slot_sidebar_300x600'] ?? '9515073457');

  $renderAd = function (?string $slot, string $visibilityClasses = '', string $label = 'Anúncio', string $extraClass = 'ads-slot-content') use ($adClient) {
    if (empty($slot)) return '';

    ob_start(); @endphp
      <section class="mb-6 {{ $visibilityClasses }}">
        <div class="ads-slot {{ $extraClass }} rounded-2xl border border-slate-200 bg-white/90 p-4 shadow-sm">
          <p class="text-xs font-semibold tracking-wide text-amber-700 uppercase">{{ $label }}</p>

          <div class="ad-container mt-3">
            <ins class="adsbygoogle"
                 style="display:block; width:100%; min-height:280px;"
                 data-ad-client="{{ $adClient }}"
                 data-ad-slot="{{ $slot }}"
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
    @php
    return ob_get_clean();
  };
@endphp

<div class="pb-24 md:pb-0">
  <main class="lit-surface mx-auto w-full max-w-7xl px-3 sm:px-4 lg:px-6 py-4 sm:py-6 text-slate-900 leading-relaxed">
    <div class="lit-grid">

      <article class="min-w-0">
        <nav aria-label="Breadcrumb" class="mb-4 text-sm text-slate-600">
          <ol class="flex flex-wrap items-center gap-2">
            <li><a href="{{ url('/liturgia-diaria') }}" class="hover:underline">Liturgia Diária</a></li>
            <li aria-hidden="true">/</li>
            <li><a href="{{ $yearHref }}" class="hover:underline">{{ $year }}</a></li>
            <li aria-hidden="true">/</li>
            <li class="text-slate-900 font-semibold">{{ ucfirst($monthName) }}</li>
          </ol>
        </nav>

        <header class="mb-6">
          <div class="flex items-start justify-between gap-4">
            <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
              Calendário da Liturgia Diária de {{ ucfirst($monthName) }} {{ $year }}
            </h1>

            <div class="shrink-0 flex items-center gap-1 text-slate-700">
              <a href="{{ url($ptSwitch) }}" class="rounded-lg px-2 py-1 text-xs font-bold text-amber-800 hover:bg-white/70">PT</a>
              @if($enSwitch)
                <a href="{{ url($enSwitch) }}" class="rounded-lg px-2 py-1 text-xs font-bold text-amber-800 hover:bg-white/70">EN</a>
              @endif
            </div>
          </div>

          <p class="mt-2 text-sm text-slate-700 max-w-3xl">
            Consulte a <strong>Liturgia Diária</strong> de qualquer data em <strong>{{ ucfirst($monthName) }} {{ $year }}</strong>.
            Em cada dia você encontra as <strong>leituras da Missa</strong>, o <strong>salmo responsorial</strong> e o
            <strong>evangelho do dia</strong>.
          </p>

          <div class="mt-4 flex flex-wrap items-center gap-2">
            <a href="{{ $prevHref }}"
               class="rounded-xl border border-slate-200 bg-white/90 px-4 py-2 text-sm font-semibold hover:bg-white">
              ← {{ $prevLabel }}
            </a>

            <a href="{{ $nextHref }}"
               class="rounded-xl border border-slate-200 bg-white/90 px-4 py-2 text-sm font-semibold hover:bg-white">
              {{ $nextLabel }} →
            </a>

            <a href="{{ $todayHref }}"
               class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-2 text-sm font-semibold hover:bg-amber-100 text-amber-900">
              Liturgia de hoje ({{ $todayLabel }})
            </a>

            <a href="{{ $yearHref }}"
               class="rounded-xl border border-slate-200 bg-white/90 px-4 py-2 text-sm font-semibold hover:bg-white">
              Calendário anual de {{ $year }}
            </a>
          </div>
        </header>

        {{-- ANÚNCIO REAL - TOPO --}}
        {!! $renderAd($slotTopAllDevices, '', 'Anúncio', 'ads-slot-content') !!}

        {{-- ANÚNCIO REAL - MOBILE --}}
        {!! $renderAd($slotMobileMid, 'lg:hidden', 'Publicidade', 'ads-slot-mobile') !!}

        @if(!empty($sundays))
          <section class="mb-6 rounded-2xl border border-slate-200 bg-white/90 p-4 sm:p-5 shadow-sm">
            <h2 class="text-lg font-extrabold tracking-tight text-slate-900">
              Domingos de {{ ucfirst($monthName) }} {{ $year }}
            </h2>
            <p class="mt-2 text-sm text-slate-700 max-w-3xl">
              Links diretos para a Liturgia Diária de cada domingo deste mês.
            </p>

            <ul class="mt-3 flex flex-wrap gap-2">
              @foreach($sundays as $x)
                <li>
                  <a href="{{ url($x['href']) }}"
                     class="inline-flex items-center rounded-xl border border-slate-200 bg-white/90 px-3 py-2 text-sm font-semibold hover:bg-white">
                    {{ str_pad((string)$x['day'],2,'0',STR_PAD_LEFT) }}/{{ str_pad((string)$month,2,'0',STR_PAD_LEFT) }}/{{ $year }}
                  </a>
                </li>
              @endforeach
            </ul>
          </section>
        @endif

        {{-- ANÚNCIO REAL - MOBILE ANTES DO CALENDÁRIO --}}
        {!! $renderAd($slotMobileInfeed1, 'lg:hidden', 'Anúncio', 'ads-slot-mobile') !!}

        <section aria-label="Calendário mensal de {{ ucfirst($monthName) }} {{ $year }}"
          class="rounded-2xl border border-slate-200 overflow-hidden bg-white/90 shadow-sm">
          <div class="cal-grid bg-slate-50 border-b border-slate-200">
            @foreach($dow as $x)
              <div class="px-2 sm:px-3 py-2 text-[11px] sm:text-xs font-extrabold text-slate-700 uppercase tracking-wide">
                {{ $x }}
              </div>
            @endforeach
          </div>

          <div class="cal-grid">
            @foreach($cells as $idx => $cell)
              @if($cell === null)
                <div class="cal-cell border-b border-slate-100 border-r border-slate-100 bg-white/40"></div>
              @else
                @php
                  $isToday = ($cell['slug'] === $todaySlug);
                  $dtIso = $year.'-'.str_pad((string)$month,2,'0',STR_PAD_LEFT).'-'.str_pad((string)$cell['day'],2,'0',STR_PAD_LEFT);
                @endphp

                <a href="{{ url($cell['href']) }}"
                   class="cal-cell border-b border-slate-100 border-r border-slate-100 p-2 sm:p-3 hover:bg-slate-50
                          focus:outline-none focus:ring-2 focus:ring-slate-300 flex flex-col justify-between
                          {{ $isToday ? 'ring-1 ring-amber-300 bg-amber-50/40' : 'bg-white' }}"
                   aria-label="Abrir liturgia do dia {{ str_pad((string)$cell['day'],2,'0',STR_PAD_LEFT) }}/{{ str_pad((string)$month,2,'0',STR_PAD_LEFT) }}/{{ $year }}">

                  <div class="flex items-start justify-between gap-2">
                    <span class="text-sm sm:text-base font-extrabold text-slate-900">{{ $cell['day'] }}</span>
                    @if($isToday)
                      <span class="rounded-full border border-amber-200 bg-amber-50 px-2 py-0.5 text-[10px] font-bold text-amber-900">Hoje</span>
                    @endif
                  </div>

                  <time class="text-[10px] sm:text-[11px] text-slate-500" datetime="{{ $dtIso }}">
                    {{ str_pad((string)$month,2,'0',STR_PAD_LEFT) }}/{{ $year }}
                  </time>
                </a>
              @endif
            @endforeach
          </div>
        </section>

        {{-- ANÚNCIO REAL - FINAL DO CONTEÚDO --}}
        {!! $renderAd($slotBottomAllDevices, '', 'Publicidade', 'ads-slot-bottom') !!}

        <section class="mt-2 rounded-2xl border border-slate-200 bg-white/90 p-4 sm:p-5 shadow-sm">
          <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Perguntas frequentes</h2>

          <div class="mt-4 space-y-4">
            <div>
              <h3 class="text-sm font-extrabold text-slate-900">Como acessar a liturgia de hoje?</h3>
              <p class="mt-1 text-sm text-slate-700">
                Use o botão <strong>“Liturgia de hoje”</strong> no topo ou acesse
                <a class="underline hover:no-underline" href="{{ $todayHref }}"> diretamente esta página</a>.
              </p>
            </div>

            <div>
              <h3 class="text-sm font-extrabold text-slate-900">
                Esta página contém a liturgia completa de cada dia de {{ ucfirst($monthName) }} {{ $year }}?
              </h3>
              <p class="mt-1 text-sm text-slate-700">
                Sim. Este é um calendário mensal com links diretos para cada data. Em cada dia você encontra as leituras, o salmo e o evangelho completos.
              </p>
            </div>

            <div>
              <h3 class="text-sm font-extrabold text-slate-900">Posso navegar para outros meses e anos?</h3>
              <p class="mt-1 text-sm text-slate-700">
                Sim. Use os botões de mês anterior/próximo e o <strong>calendário anual</strong> para localizar outras datas rapidamente.
              </p>
            </div>
          </div>
        </section>

        <div class="lit-aside-mobile mt-6">
          <div class="rounded-2xl bg-white/90 p-4 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between">
              <p class="text-xs font-semibold tracking-wide text-amber-700 uppercase">Acesso rápido</p>
              <button type="button" id="aside-toggle"
                class="text-xs font-semibold text-slate-600 hover:text-slate-900">Mostrar</button>
            </div>

            <div id="aside-mobile" class="mt-3 hidden">
              @include('liturgia.partials.aside', [
                'year' => $year,
                'month' => $month,
                'todaySlug' => $todaySlug,
                'todayLabel' => $todayLabel,
                'prevSlug' => $prevSlug,
                'nextSlug' => $nextSlug,
                'adsSlotDesktop300x250' => $slotAsideTop,
                'adsSlotDesktopSticky' => $slotAsideBottom,
                'variant' => 'mobile',
                'pageSlug' => null,
                'lang' => 'pt',
              ])
            </div>
          </div>
        </div>
      </article>

      <aside class="lit-aside-desktop min-w-0">
        <div class="sticky top-20">
          @include('liturgia.partials.aside', [
            'year' => $year,
            'month' => $month,
            'todaySlug' => $todaySlug,
            'todayLabel' => $todayLabel,
            'prevSlug' => $prevSlug,
            'nextSlug' => $nextSlug,
            'adsSlotDesktop300x250' => $slotAsideTop,
            'adsSlotDesktopSticky' => $slotAsideBottom,
            'variant' => 'desktop',
            'pageSlug' => null,
            'lang' => 'pt',
          ])
        </div>
      </aside>

    </div>
  </main>
</div>
@endsection

@push('scripts')
<script>
(function () {
  const toggle = document.getElementById('aside-toggle');
  const panel = document.getElementById('aside-mobile');

  if (toggle && panel) {
    toggle.addEventListener('click', () => {
      const open = !panel.classList.contains('hidden');
      panel.classList.toggle('hidden', open);
      toggle.textContent = open ? 'Mostrar' : 'Ocultar';

      if (!open) {
        setTimeout(() => {
          try {
            panel.querySelectorAll('ins.adsbygoogle:not([data-adsbygoogle-status])').forEach(() => {
              (window.adsbygoogle = window.adsbygoogle || []).push({});
            });
          } catch (e) {}
        }, 50);
      }
    });
  }
})();
</script>
@endpush