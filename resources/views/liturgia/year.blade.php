{{-- resources/views/liturgia/year.blade.php --}}

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

  {{-- Perf: melhora handshake DNS/TLS do Ads (baixo risco) --}}
  <link rel="preconnect" href="https://pagead2.googlesyndication.com" crossorigin>
  <link rel="preconnect" href="https://googleads.g.doubleclick.net" crossorigin>
  <link rel="preconnect" href="https://tpc.googlesyndication.com" crossorigin>

  <style>
    .lit-day-grid { display: block; }
    .lit-aside-desktop { display: none; }
    .lit-aside-mobile { display: block; }

    @media (min-width: 1024px) {
      .lit-day-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 340px;
        gap: 24px;
        align-items: start;
      }
      .lit-aside-desktop { display: block; }
      .lit-aside-mobile { display: none; }
    }

    .lit-surface {
      background: linear-gradient(180deg, rgba(255,251,235,0.70) 0%, rgba(255,255,255,1) 55%);
      border-radius: 24px;
    }

    /* Reserva mínima para evitar salto */
    .ads-slot { min-height: 120px; }
    .ad-container { width: 100%; }
  </style>
@endpush

@section('content')
@php
  $siteUrl = rtrim(config('app.url') ?: url('/'), '/');

  $ptHref = url("/liturgia-diaria/ano/{$year}");
  $enHref = url("/en/daily-mass-readings/year/{$year}");

  $todayISO = \Carbon\Carbon::today()->toDateString();
  $todaySlug = \Carbon\Carbon::parse($todayISO)->format('d-m-Y');
  $todayLabel = \Carbon\Carbon::parse($todayISO)->format('d/m/Y');
  $prevSlug = \Carbon\Carbon::parse($todayISO)->subDay()->format('d-m-Y');
  $nextSlug = \Carbon\Carbon::parse($todayISO)->addDay()->format('d-m-Y');

  $asideMonth = (int)\Carbon\Carbon::parse($todayISO)->format('m');

  $defaultBlogLinks = [
    ['href' => url('/blog/como-usar-a-liturgia'), 'title' => 'Como usar a Liturgia Diária', 'desc' => 'Um jeito simples de rezar e se preparar para a Missa.'],
    ['href' => url('/blog/ano-liturgico'), 'title' => 'Ano litúrgico: tempos, cores e calendário', 'desc' => 'Entenda o que muda ao longo do ano e como acompanhar.'],
    ['href' => url('/blog/leituras-da-missa'), 'title' => 'Guia das leituras da Missa', 'desc' => 'Primeira leitura, salmo, evangelho e como seguir.'],
    ['href' => url('/blog/como-rezar-com-a-liturgia-em-5-minutos'), 'title' => 'Rezar com a Liturgia em 5 minutos', 'desc' => 'Um passo a passo para criar consistência.'],
    ['href' => url('/guias/evangelho-do-dia'), 'title' => 'Liturgia Diária vs Evangelho do dia', 'desc' => 'O que cada um traz e quando usar.'],
  ];
  $effectiveBlogLinks = $defaultBlogLinks;

  // ✅ AdSense
  $adClient = 'ca-pub-8819996017476509';

  /**
   * Slot dedicado do Year hub
   */
  $slotTop = $ads['slot_lit_year_top_responsive'] ?? ($ads['slot_content_responsive'] ?? null);
@endphp

<div class="pb-24 md:pb-0">
  <article class="lit-surface mx-auto w-full max-w-7xl px-3 sm:px-4 lg:px-6 py-4 sm:py-6 text-slate-900 leading-relaxed">
    <div class="lit-day-grid gap-5 items-start">

      {{-- MAIN --}}
      <section class="min-w-0">

        {{-- Breadcrumbs --}}
        <nav aria-label="Breadcrumb" class="mb-4 text-sm text-slate-600">
          <ol class="flex flex-wrap items-center gap-2">
            <li><a href="{{ url('/liturgia-diaria') }}" class="hover:underline">Liturgia Diária</a></li>
            <li aria-hidden="true">/</li>
            <li class="text-slate-900 font-semibold">{{ $year }}</li>
          </ol>
        </nav>

        <header class="mb-6">
          <div class="flex items-start justify-between gap-4">
            <div class="min-w-0">
              <p class="text-xs font-semibold tracking-wide text-amber-700 uppercase">
                IA Tio Ben • Liturgia Diária
              </p>
              <h1 class="mt-2 text-2xl sm:text-4xl font-extrabold tracking-tight">
                Calendário da Liturgia Diária – Ano {{ $year }}
              </h1>
            </div>

            <div class="shrink-0 flex items-center gap-1 text-slate-700">
              <a href="{{ $ptHref }}" class="rounded-lg px-2 py-1 text-xs font-bold text-amber-800 hover:bg-white/70">PT</a>
              <a href="{{ $enHref }}" class="rounded-lg px-2 py-1 text-xs font-bold text-amber-800 hover:bg-white/70">EN</a>
            </div>
          </div>

          <p class="mt-3 text-[15px] leading-7 text-slate-700 max-w-3xl">
            Este é o calendário anual da <strong>Liturgia Diária</strong> em <strong>{{ $year }}</strong>.
            Escolha um mês para acessar a liturgia completa de cada dia, com <strong>leituras da Missa</strong>,
            <strong>salmo responsorial</strong> e <strong>evangelho do dia</strong>.
          </p>

          @include('liturgia.partials.mobile-beta-banner')


          {{-- Navegação limitada + pesquisa --}}
          <div class="mt-4 flex flex-wrap items-center gap-2">
            @if($yearPrev >= $yearMin)
              <a href="{{ url("/liturgia-diaria/ano/{$yearPrev}") }}"
                 class="rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm font-semibold hover:bg-white">
                ← Ano {{ $yearPrev }}
              </a>
            @endif

            @if($yearNext <= $yearMax)
              <a href="{{ url("/liturgia-diaria/ano/{$yearNext}") }}"
                 class="rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm font-semibold hover:bg-white">
                Ano {{ $yearNext }} →
              </a>
            @endif

            <a href="{{ url('/liturgia-diaria') }}"
               class="rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm font-semibold hover:bg-white">
              Voltar para a Liturgia Diária
            </a>

            <div class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white/80 px-3 py-2">
              <span class="text-xs font-semibold text-slate-600">Pesquisar ano</span>
              <input
                id="year-search"
                type="number"
                inputmode="numeric"
                min="{{ $yearMin }}"
                max="{{ $yearMax }}"
                value="{{ $year }}"
                class="w-24 rounded-lg border border-slate-200 bg-white px-2 py-1 text-sm font-semibold text-slate-800"
                aria-label="Pesquisar ano"
              />
              <button id="year-go" type="button"
                class="rounded-lg bg-amber-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-amber-700">
                Ir
              </button>
            </div>
          </div>
        </header>

        {{-- Ano Litúrgico (A/B/C) --}}
        <section class="rounded-2xl bg-white/90 p-4 sm:p-5 shadow-sm border border-slate-200">
          <p class="text-xs font-semibold tracking-wide text-amber-700 uppercase">
            Ano Litúrgico (ciclo A/B/C)
          </p>

          <h2 class="mt-2 text-lg sm:text-xl font-extrabold tracking-tight text-slate-900">
            Atualmente estamos no Ano {{ $lit['letter'] }} (2026)
          </h2>

          <p class="mt-2 text-sm text-slate-700 leading-7">
            O Ano Litúrgico começa no <strong>1º Domingo do Advento</strong>. Em 2026, o ciclo atual é o
            <strong>Ano {{ $lit['letter'] }}</strong>, iniciado no Advento ({{ $lit['advent_start_human'] }}).
            Esse rodízio de três anos organiza as leituras dominicais em <strong>A</strong>, <strong>B</strong> e <strong>C</strong>.
          </p>

          <div class="mt-4 grid grid-cols-1 gap-3">
            <div class="rounded-xl bg-amber-50/70 p-3 border border-amber-100">
              <p class="text-sm font-extrabold text-slate-900">Ano A</p>
              <p class="mt-1 text-sm text-slate-700 leading-7">{{ $lit['textA'] }}</p>
            </div>
            <div class="rounded-xl bg-amber-50/70 p-3 border border-amber-100">
              <p class="text-sm font-extrabold text-slate-900">Ano B</p>
              <p class="mt-1 text-sm text-slate-700 leading-7">{{ $lit['textB'] }}</p>
            </div>
            <div class="rounded-xl bg-amber-50/70 p-3 border border-amber-100">
              <p class="text-sm font-extrabold text-slate-900">Ano C</p>
              <p class="mt-1 text-sm text-slate-700 leading-7">{{ $lit['textC'] }}</p>
            </div>
          </div>
        </section>

        {{-- ✅ AdSense (SEM script inline; o push fica global no final) --}}
        @if(!empty($slotTop))
          <section class="mt-6">
            <div class="ads-slot rounded-2xl border border-slate-200 bg-white/90 p-4 shadow-sm">
              <p class="text-xs font-semibold tracking-wide text-amber-700 uppercase">Anúncio</p>

              <div class="ad-container mt-3">
                <ins class="adsbygoogle js-adsbygoogle"
                     style="display:block; width:100%;"
                     data-ad-client="{{ $adClient }}"
                     data-ad-slot="{{ $slotTop }}"
                     data-ad-format="auto"
                     data-full-width-responsive="true"></ins>
              </div>
            </div>
          </section>
        @endif

        {{-- Meses --}}
        <section class="mt-6" aria-label="Meses do ano {{ $year }}">
          <h2 class="text-lg font-extrabold tracking-tight text-slate-900">
            Escolha um mês
          </h2>
          <p class="mt-2 text-sm text-slate-700 max-w-3xl">
            Selecione um mês para abrir o calendário mensal e acessar qualquer dia.
          </p>

          <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($months as $x)
              <a href="{{ $x['href'] }}"
                 class="rounded-2xl border border-slate-200 bg-white/90 p-4 hover:bg-white transition"
                 aria-label="Abrir calendário da Liturgia Diária de {{ $x['label'] }}">
                <p class="text-sm font-extrabold text-slate-900">{{ $x['label'] }}</p>
                <p class="mt-1 text-xs text-slate-700">
                  Abrir calendário do mês e acessar qualquer dia
                </p>
              </a>
            @endforeach
          </div>
        </section>

        {{-- MOBILE: Aside collapsible --}}
        <div class="lit-aside-mobile mt-6">
          <div class="rounded-2xl bg-white/90 p-4 shadow-sm">
            <div class="flex items-center justify-between">
              <p class="text-xs font-semibold tracking-wide text-amber-700 uppercase">Acesso rápido</p>
              <button type="button" id="aside-toggle"
                class="text-xs font-semibold text-slate-600 hover:text-slate-900">Mostrar</button>
            </div>

            <div id="aside-mobile" class="mt-3 hidden">
              @include('liturgia.partials.aside', [
                'year' => $year,
                'month' => $asideMonth,
                'todaySlug' => $todaySlug,
                'todayLabel' => $todayLabel,
                'prevSlug' => $prevSlug,
                'nextSlug' => $nextSlug,
                'variant' => 'mobile',
                'pageSlug' => null,
                'lang' => 'pt',
                'blogLinks' => $effectiveBlogLinks,
              ])
            </div>
          </div>
        </div>

        <footer class="mt-8 border-t border-slate-200 pt-6">
          <p class="text-xs text-slate-500 break-words">
            Compartilhe o calendário e ajude alguém a acompanhar a Liturgia Diária.
          </p>
        </footer>
      </section>

      {{-- ASIDE Desktop --}}
      <aside class="lit-aside-desktop min-w-0">
        <div class="sticky top-20">
          @include('liturgia.partials.aside', [
            'year' => $year,
            'month' => $asideMonth,
            'todaySlug' => $todaySlug,
            'todayLabel' => $todayLabel,
            'prevSlug' => $prevSlug,
            'nextSlug' => $nextSlug,
            'adsSlotDesktop300x250' => $ads['slot_desktop'] ?? null,
            'blogLinks' => $effectiveBlogLinks,
            'variant' => 'desktop',
            'pageSlug' => null,
            'lang' => 'pt',
          ])
        </div>
      </aside>

    </div>
  </article>
</div>
@endsection

@push('scripts')
<script>
(function(){
  // aside toggle
  const toggle = document.getElementById('aside-toggle');
  const panel = document.getElementById('aside-mobile');
  if(toggle && panel){
    toggle.addEventListener('click', ()=>{
      const open = !panel.classList.contains('hidden');
      panel.classList.toggle('hidden', open);
      toggle.textContent = open ? 'Mostrar' : 'Ocultar';
    });
  }

  // year search (limitado)
  const input = document.getElementById('year-search');
  const go = document.getElementById('year-go');
  const min = Number(input?.getAttribute('min'));
  const max = Number(input?.getAttribute('max'));

  function navigateYear(){
    if(!input) return;
    const y = Number(input.value || '');
    if(!Number.isFinite(y)) return;
    if(y < min || y > max){
      input.value = String({{ $year }});
      return;
    }
    window.location.href = `/liturgia-diaria/ano/${y}`;
  }

  go && go.addEventListener('click', navigateYear);
  input && input.addEventListener('keydown', (e)=>{
    if(e.key === 'Enter') navigateYear();
  });

  // ===== AdSense init (um único lugar) =====
  function pushAllAds() {
    try {
      window.adsbygoogle = window.adsbygoogle || [];
      const insList = document.querySelectorAll('ins.adsbygoogle.js-adsbygoogle:not([data-adsbygoogle-status])');
      insList.forEach(() => {
        try { window.adsbygoogle.push({}); } catch (e) {}
      });
    } catch (e) {}
  }

  function waitAdsenseAndPush(maxTries, intervalMs) {
    let tries = 0;
    const t = setInterval(() => {
      tries++;
      if (window.adsbygoogle && typeof window.adsbygoogle.push === 'function') {
        clearInterval(t);
        if ('requestIdleCallback' in window) requestIdleCallback(pushAllAds, { timeout: 1500 });
        else setTimeout(pushAllAds, 250);
        return;
      }
      if (tries >= maxTries) {
        clearInterval(t);
        setTimeout(pushAllAds, 0);
      }
    }, intervalMs);
  }

  window.addEventListener('load', function () {
    waitAdsenseAndPush(24, 250); // ~6s
  }, { once: true });

})();
</script>
@endpush