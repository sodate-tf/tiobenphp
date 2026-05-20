{{-- resources/views/layouts/admin.blade.php --}}
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title>@yield('title', 'Admin — IA Tio Ben')</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 text-slate-900">
  <div x-data="{ sidebarOpen: false }" class="min-h-screen">

    {{-- Topbar (mobile) --}}
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b border-slate-200 lg:hidden">
      <div class="h-14 px-4 flex items-center justify-between">
        <button
          type="button"
          class="inline-flex items-center justify-center rounded-xl p-2 border border-slate-200 bg-white shadow-sm"
          @click="sidebarOpen = true"
          aria-label="Abrir menu"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>

        <div class="flex items-center gap-2">
          <div class="h-8 w-8 rounded-xl bg-slate-900 text-white flex items-center justify-center text-xs font-bold">
            TB
          </div>
          <div class="text-sm font-semibold">Admin</div>
        </div>

        <div class="w-10"></div>
      </div>
    </header>

    {{-- Sidebar (desktop fixo) --}}
    <aside class="hidden lg:flex lg:fixed lg:inset-y-0 lg:w-72 lg:flex-col">
      <div class="h-full bg-white border-r border-slate-200 flex flex-col">
        <div class="h-16 px-6 flex items-center gap-3 border-b border-slate-200">
          <div class="h-9 w-9 rounded-2xl bg-slate-900 text-white flex items-center justify-center text-xs font-bold">
            TB
          </div>
          <div>
            <div class="text-sm font-semibold leading-tight">IA Tio Ben</div>
            <div class="text-xs text-slate-500">Admin</div>
          </div>
        </div>

        @include('admin.partials.sidebar')
      </div>
    </aside>

    {{-- Drawer sidebar (mobile) --}}
    <div class="lg:hidden">
      {{-- overlay --}}
      <div
        x-show="sidebarOpen"
        x-transition.opacity
        class="fixed inset-0 z-50 bg-black/40"
        @click="sidebarOpen = false"
        style="display:none"
      ></div>

      {{-- panel --}}
      <aside
        x-show="sidebarOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed inset-y-0 left-0 z-50 w-80 max-w-[85vw] bg-white border-r border-slate-200"
        @keydown.escape.window="sidebarOpen = false"
        style="display:none"
      >
        <div class="h-16 px-4 flex items-center justify-between border-b border-slate-200">
          <div class="flex items-center gap-3">
            <div class="h-9 w-9 rounded-2xl bg-slate-900 text-white flex items-center justify-center text-xs font-bold">
              TB
            </div>
            <div>
              <div class="text-sm font-semibold leading-tight">IA Tio Ben</div>
              <div class="text-xs text-slate-500">Admin</div>
            </div>
          </div>

          <button
            type="button"
            class="inline-flex items-center justify-center rounded-xl p-2 border border-slate-200 bg-white"
            @click="sidebarOpen = false"
            aria-label="Fechar menu"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <div @click="sidebarOpen = false" class="h-full">
          @include('admin.partials.sidebar')
        </div>
      </aside>
    </div>

    {{-- Content --}}
    <div class="lg:pl-72">
      <main class="px-4 sm:px-6 py-6">
        <div class="max-w-6xl mx-auto">

          {{-- Header do conteúdo --}}
          @php
            $subtitle = trim((string) view()->yieldContent('page_subtitle', ''));
          @endphp

          <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
              <h1 class="text-xl sm:text-2xl font-semibold tracking-tight">
                @yield('page_title', 'Dashboard')
              </h1>

              @if($subtitle !== '')
                <p class="text-sm text-slate-600 mt-1">{{ $subtitle }}</p>
              @endif
            </div>

            <div class="flex items-center gap-2">
              @yield('page_actions')
            </div>
          </div>

          {{-- Flash --}}
          @if(session('success'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
              {{ session('success') }}
            </div>
          @endif

          @if(session('error'))
            <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
              {{ session('error') }}
            </div>
          @endif

          {{-- Conteúdo --}}
          <div class="rounded-2xl bg-white border border-slate-200 shadow-sm">
            <div class="p-4 sm:p-6">
              @yield('content')
            </div>
          </div>

          <footer class="py-6 text-xs text-slate-500">
            © {{ date('Y') }} IA Tio Ben
          </footer>
        </div>
      </main>
    </div>

  </div>
</body>
</html>
