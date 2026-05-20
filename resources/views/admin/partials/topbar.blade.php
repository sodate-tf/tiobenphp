{{-- resources/views/admin/partials/topbar.blade.php --}}
@php
  $nav = [
    ['label' => 'Dashboard', 'icon' => '🏠', 'route' => 'admin.dashboard', 'active'=> request()->routeIs('admin.dashboard')],
    ['label' => 'Posts', 'icon' => '📝', 'route' => 'admin.posts.index', 'active'=> request()->routeIs('admin.posts.*')],
    ['label' => 'Categorias', 'icon' => '🏷️', 'route' => 'admin.categories.index', 'active'=> request()->routeIs('admin.categories.*')],
    ['label' => 'Liturgia ↔ Posts', 'icon' => '📖', 'route' => 'admin.liturgy-links.index', 'active'=> request()->routeIs('admin.liturgy-links.*'), 'show' => \Illuminate\Support\Facades\Route::has('admin.liturgy-links.index')],
  ];
  $nav = array_values(array_filter($nav, fn($i) => ($i['show'] ?? true) === true));
@endphp

<div x-data="{ open:false }" class="lg:hidden">
  {{-- Topbar --}}
  <div class="sticky top-0 z-50 bg-white border-b">
    <div class="h-16 px-4 flex items-center justify-between">
      <button type="button"
              @click="open=true"
              class="inline-flex items-center justify-center h-10 w-10 rounded-xl border border-slate-200 hover:bg-slate-50">
        ☰
      </button>

      <div class="flex items-center gap-2">
        <div class="h-9 w-9 rounded-2xl bg-amber-100 flex items-center justify-center">⛪</div>
        <div class="leading-tight">
          <div class="text-sm font-extrabold text-slate-900">Admin</div>
          <div class="text-[11px] text-slate-500">IA Tio Ben</div>
        </div>
      </div>

      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="h-10 px-3 rounded-xl border border-slate-200 text-sm font-semibold hover:bg-slate-50">
          Sair
        </button>
      </form>
    </div>
  </div>

  {{-- Backdrop --}}
  <div x-show="open" x-transition.opacity
       class="fixed inset-0 z-50 bg-black/40"
       @click="open=false" style="display:none"></div>

  {{-- Drawer --}}
  <aside x-show="open" x-transition
         class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r shadow-xl"
         @keydown.escape.window="open=false"
         style="display:none">

    <div class="h-16 px-4 flex items-center justify-between border-b">
      <div class="flex items-center gap-3">
        <div class="h-10 w-10 rounded-2xl bg-amber-100 flex items-center justify-center text-lg">⛪</div>
        <div class="min-w-0">
          <div class="font-extrabold text-slate-900 truncate">IA Tio Ben</div>
          <div class="text-xs text-slate-500 truncate">Admin</div>
        </div>
      </div>
      <button type="button"
              @click="open=false"
              class="h-10 w-10 rounded-xl border border-slate-200 hover:bg-slate-50">
        ✕
      </button>
    </div>

    <nav class="px-3 py-4 space-y-1">
      @foreach($nav as $item)
        <a href="{{ route($item['route']) }}"
           @click="open=false"
           class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-semibold
                  {{ $item['active'] ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
          <span class="text-base">{{ $item['icon'] }}</span>
          <span class="truncate">{{ $item['label'] }}</span>
        </a>
      @endforeach
    </nav>

    <div class="mt-auto p-3 border-t">
      <div class="text-xs text-slate-500">Logado como</div>
      <div class="text-sm font-semibold text-slate-900 truncate">{{ auth()->user()->name }}</div>
      <div class="text-xs text-slate-500 truncate">{{ auth()->user()->email }}</div>
    </div>
  </aside>
</div>
