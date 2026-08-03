{{-- resources/views/layouts/site.blade.php --}}
@php
  $htmlLang = trim((string) view()->yieldContent('html_lang', app()->getLocale()));
  if ($htmlLang === '') {
    $htmlLang = 'pt-BR';
  }

  $defaultRobots = app()->environment('production')
    ? 'index,follow,max-image-preview:large'
    : 'noindex,nofollow,noarchive';

  $robots = trim((string) view()->yieldContent('robots', $defaultRobots));
  if ($robots === '') {
    $robots = $defaultRobots;
  }

  $canonical = trim((string) view()->yieldContent('canonical', url()->current()));
  if ($canonical === '') {
    $canonical = url()->current();
  }

  $pageTitle = trim((string) view()->yieldContent('title', 'IA Tio Ben'));
  if ($pageTitle === '') {
    $pageTitle = 'IA Tio Ben';
  }

  $metaDescription = trim((string) view()->yieldContent('meta_description', ''));

  $ogTitle = trim((string) view()->yieldContent('og_title', $pageTitle));
  if ($ogTitle === '') {
    $ogTitle = $pageTitle;
  }

  $ogDesc = trim((string) view()->yieldContent('og_description', $metaDescription));
  $ogUrl = trim((string) view()->yieldContent('og_url', $canonical));
  if ($ogUrl === '') {
    $ogUrl = $canonical;
  }

  $ogType = trim((string) view()->yieldContent('og_type', 'website'));
  if ($ogType === '') {
    $ogType = 'website';
  }

  $ogLocale = trim((string) view()->yieldContent(
    'og_locale',
    str_starts_with($htmlLang, 'en') ? 'en_US' : 'pt_BR'
  ));

  $twCard = trim((string) view()->yieldContent('tw_card', 'summary_large_image'));
  if ($twCard === '') {
    $twCard = 'summary_large_image';
  }

  $twTitle = trim((string) view()->yieldContent('tw_title', $ogTitle));
  $twDesc = trim((string) view()->yieldContent('tw_description', $ogDesc));
  $twImg = trim((string) view()->yieldContent('tw_image', view()->yieldContent('og_image', '')));

  $adsenseClient = 'ca-pub-8819996017476509';
@endphp

<!doctype html>
<html lang="{{ $htmlLang }}">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title>{{ $pageTitle }}</title>

  @if($metaDescription !== '')
    <meta name="description" content="{{ $metaDescription }}" />
  @endif

  @php($metaKeywords = trim((string) view()->yieldContent('meta_keywords', '')))
  @if($metaKeywords !== '')
    <meta name="keywords" content="{{ $metaKeywords }}" />
  @endif

  <meta name="robots" content="{{ $robots }}" />
  <link rel="canonical" href="{{ $canonical }}" />

  @yield('hreflang')

  <meta property="og:title" content="{{ $ogTitle }}" />

  @if($ogDesc !== '')
    <meta property="og:description" content="{{ $ogDesc }}" />
  @endif

  <meta property="og:url" content="{{ $ogUrl }}" />
  <meta property="og:site_name" content="IA Tio Ben" />
  <meta property="og:type" content="{{ $ogType }}" />
  <meta property="og:locale" content="{{ $ogLocale }}" />

  @php($ogImage = trim((string) view()->yieldContent('og_image', '')))
  @if($ogImage !== '')
    <meta property="og:image" content="{{ $ogImage }}" />

    @php($ogImageAlt = trim((string) view()->yieldContent('og_image_alt', $ogTitle)))
    @if($ogImageAlt !== '')
      <meta property="og:image:alt" content="{{ $ogImageAlt }}" />
    @endif
  @endif

  <meta name="twitter:card" content="{{ $twCard }}" />

  @if($twTitle !== '')
    <meta name="twitter:title" content="{{ $twTitle }}" />
  @endif

  @if($twDesc !== '')
    <meta name="twitter:description" content="{{ $twDesc }}" />
  @endif

  @if($twImg !== '')
    <meta name="twitter:image" content="{{ $twImg }}" />
  @endif

  {{-- Conexões antecipadas para AdSense/Ads --}}
  @if(app()->environment('production'))
    <link rel="preconnect" href="https://pagead2.googlesyndication.com" crossorigin>
    <link rel="preconnect" href="https://googleads.g.doubleclick.net" crossorigin>
    <link rel="preconnect" href="https://tpc.googlesyndication.com" crossorigin>
  @endif

  {{-- Assets principais --}}
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  {{-- Fonte do sistema: evita bloqueio por Google Fonts --}}
  <style>
    :root{
      --font-sans: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Helvetica, Arial,
                   "Apple Color Emoji", "Segoe UI Emoji";
    }

    html,
    body {
      font-family: var(--font-sans);
      text-rendering: optimizeLegibility;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }

    input,
    button,
    select,
    textarea {
      font-family: var(--font-sans);
    }
  </style>

  {{-- AdSense global sem atraso --}}
  @if(app()->environment('production'))
    <script async
      src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ $adsenseClient }}"
      crossorigin="anonymous"></script>
  @endif

  {{-- Head extras / JSON-LD / tags específicas da página --}}
  @stack('head')
  @yield('head_extras')
</head>

<body class="bg-white text-slate-900">
  @include('partials.header')

  <main>
    @yield('content')
  </main>

  @include('partials.footer')

  @if(!str_starts_with($htmlLang, 'en'))
    @include('partials.download-app-floating')
    @include('partials.download-app-modal')
  @endif

  @stack('scripts')

  @include('partials.analytics-ads', [
    'adsenseClient' => $adsenseClient,
    'ga4Id' => 'G-17GKJ4F1Q8',
  ])
</body>
</html>