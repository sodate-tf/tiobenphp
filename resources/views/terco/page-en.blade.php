{{-- resources/views/layouts/site.blade.php --}}
<!doctype html>
<html lang="@yield('html_lang', app()->getLocale())">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title>@yield('title', 'IA Tio Ben')</title>
  <meta name="description" content="@yield('meta_description', '')" />
  <meta name="keywords" content="@yield('meta_keywords', '')" />
  <meta name="robots" content="@yield('robots', 'index,follow')" />

  <link rel="canonical" href="@yield('canonical')" />
  @yield('hreflang')

  <meta property="og:title" content="@yield('og_title')" />
  <meta property="og:description" content="@yield('og_description')" />
  <meta property="og:url" content="@yield('og_url')" />
  <meta property="og:site_name" content="IA Tio Ben" />
  <meta property="og:image" content="@yield('og_image')" />

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @stack('head')

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <style>
    :root{
      --font-sans: "Inter", ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Helvetica, Arial,
                   "Apple Color Emoji","Segoe UI Emoji";
    }
    html, body {
      font-family: var(--font-sans);
      text-rendering: optimizeLegibility;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }
    input, button, select, textarea { font-family: var(--font-sans); }
  </style>

  @yield('head_extras')
</head>

<body class="bg-white text-slate-900">

  <div id="site-header">
    @include('partials.header')
  </div>

  <main>
    @yield('content')
  </main>

  @include('partials.footer')

  {{-- define --site-header-h dinamicamente (para posicionar o bottom sheet no mobile) --}}
  <script>
  (function () {
    function setHeaderH(){
      var h = document.getElementById('site-header');
      if(!h) return;
      var px = Math.round(h.getBoundingClientRect().height || 0);
      document.documentElement.style.setProperty('--site-header-h', px + 'px');
    }
    setHeaderH();
    window.addEventListener('resize', setHeaderH);
  })();
  </script>

  @stack('scripts')
</body>
</html>
