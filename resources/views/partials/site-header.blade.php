<header class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b border-slate-200">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between">
    <a href="{{ url('/') }}" class="flex items-center gap-2">
      <span class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-slate-900 text-white text-xs font-bold">TB</span>
      <span class="text-sm font-semibold">IA Tio Ben</span>
    </a>

    <nav class="hidden sm:flex items-center gap-6 text-sm font-medium">
      <a href="{{ url('/liturgia-diaria') }}" class="text-slate-700 hover:text-slate-900">Liturgia</a>
      <a href="{{ url('/santo-terco') }}" class="text-slate-700 hover:text-slate-900">Santo Terço</a>
      <a href="{{ url('/blog') }}" class="text-slate-700 hover:text-slate-900">Blog</a>
    </nav>

    <div class="flex items-center gap-2">
      <a href="{{ url('/en') }}" class="text-xs px-3 py-1 rounded-xl border border-slate-200 hover:bg-slate-50">EN</a>
    </div>
  </div>

  {{-- mobile nav --}}
  <div class="sm:hidden border-t border-slate-100">
    <div class="max-w-7xl mx-auto px-4 py-2 flex gap-3 text-sm">
      <a href="{{ url('/liturgia-diaria') }}" class="text-slate-700">Liturgia</a>
      <a href="{{ url('/santo-terco') }}" class="text-slate-700">Terço</a>
      <a href="{{ url('/blog') }}" class="text-slate-700">Blog</a>
    </div>
  </div>
</header>
