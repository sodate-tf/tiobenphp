{{-- resources/views/en/rosary/page.blade.php --}}
@extends('layouts.site')

@section('html_lang', $meta['html_lang'] ?? 'en')
@section('title', $meta['title'] ?? 'Rosary — IA Tio Ben')
@section('meta_description', $meta['description'] ?? 'Pray the Rosary step by step: interactive beads, mysteries, reflections, and a complete prayer guide.')
@section('canonical', $meta['canonical'] ?? url()->current())
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

@section('og_title', data_get($meta, 'og.title', $meta['title'] ?? 'Rosary — IA Tio Ben'))
@section('og_description', data_get($meta, 'og.description', $meta['description'] ?? 'Pray the Rosary step by step with interactive beads and a prayer guide.'))
@section('og_url', data_get($meta, 'og.url', $meta['canonical'] ?? url()->current()))
@section('og_image', data_get($meta, 'og.image', ''))

@section('head_extras')
  @php
    $lang = 'en';
    $isEn = true;

    $assetV = $assetV ?? '1';

    // inicial vem do controller
    $initial = $initial ?? [];
    // garanta coerência para o JS:
    $initial['lang'] = $initial['lang'] ?? 'en';
    $initial['route'] = $initial['route'] ?? 'en';

    $setKey = data_get($initial, 'setKey'); // gozosos/dolorosos/gloriosos/luminosos

    $pageName = 'Rosary';
    $pageDesc = $meta['description'] ?? 'Pray the Rosary step by step: interactive beads, mysteries, reflections, and a complete prayer guide.';

    $webPageJsonLd = [
      '@context' => 'https://schema.org',
      '@type' => 'WebPage',
      'name' => $pageName,
      'url' => $meta['canonical'] ?? url()->current(),
      'description' => $pageDesc,
      'inLanguage' => 'en',
    ];
  @endphp

  <script type="application/ld+json">{!! json_encode($webPageJsonLd, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>

  <script>
    window.__ROSARY_INITIAL__ = @json($initial);
  </script>

  <link rel="preload" href="/js/rosary/rosary-app.js?v={{ $assetV }}" as="script">
@endsection

@section('content')
  <div class="bg-amber-400">
    <div class="mx-auto w-full max-w-6xl px-3 sm:px-6 py-6">

      <header class="mb-4 sm:mb-6">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-amber-950">
          {{ $meta['h1'] ?? 'Pray the Rosary step by step' }}
        </h1>
        <p class="mt-2 text-sm sm:text-base text-amber-950/80">
          {{ $meta['lead'] ?? 'Interactive beads, mysteries of the day, and a complete prayer guide.' }}
        </p>
      </header>

      <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
        <div class="min-w-0 lg:col-span-8">
          <div id="rosary-app" class="min-h-[320px]">
            <noscript>
              <div class="rounded-2xl border border-amber-200 bg-white p-5">
                <h2 class="text-lg font-extrabold text-gray-900">Rosary</h2>
                <p class="mt-2 text-gray-700">Enable JavaScript to use the interactive Rosary.</p>
              </div>
            </noscript>
          </div>
        </div>

        <div class="min-w-0 lg:col-span-4">
          @include('terco.partials.aside', [
            'lang' => 'en',
            'variant' => 'desktop',
            'setKey' => $setKey,
            'adsSlotDesktop300x250' => $adsSlotDesktop300x250 ?? null,
            'adsSlotMobile' => $adsSlotMobile ?? null,
            'blogLinks' => $blogLinks ?? null,
          ])
        </div>
      </div>
    </div>
  </div>

  {{-- scripts --}}
  <script defer src="/js/rosary/rosary-dataset-en.js?v={{ $assetV }}"></script>
  <script defer src="/js/rosary/rosary-engine.js?v={{ $assetV }}"></script>
  <script defer src="/js/rosary/rosary-app.js?v={{ $assetV }}"></script>
@endsection
