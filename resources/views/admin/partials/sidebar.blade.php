{{-- resources/views/admin/partials/sidebar.blade.php --}}
@php
  $nav = [
    [
      'label' => 'Dashboard',
      'icon'  => 'Home',
      'route' => 'admin.dashboard',
      'active'=> request()->routeIs('admin.dashboard'),
    ],
    [
      'label' => 'Posts',
      'icon'  => 'Posts',
      'route' => 'admin.posts.index',
      'active'=> request()->routeIs('admin.posts.*'),
    ],
    [
      'label' => 'Categorias',
      'icon'  => 'Tags',
      'route' => 'admin.categories.index',
      'active'=> request()->routeIs('admin.categories.*'),
    ],
    [
      'label' => 'Liturgia -> Posts',
      'icon'  => 'Links',
      'route' => 'admin.liturgy-links.index',
      'active'=> request()->routeIs('admin.liturgy-links.*'),
      'show'  => \Illuminate\Support\Facades\Route::has('admin.liturgy-links.index'),
    ],
    [
      'label'  => 'Posts Relacionados',
      'icon'   => 'Related',
      'route'  => 'admin.related.index',
      'active' => request()->routeIs('admin.related.*'),
      'show'   => \Illuminate\Support\Facades\Route::has('admin.related.index'),
    ],
    [
      'label'  => 'Operacoes SEO/IA',
      'icon'   => 'Ops',
      'route'  => 'admin.ops.backfills.index',
      'active' => request()->routeIs('admin.ops.backfills.*'),
      'show'   => \Illuminate\Support\Facades\Route::has('admin.ops.backfills.index'),
    ],
  ];

  $nav = array_values(array_filter($nav, fn($i) => ($i['show'] ?? true) === true));
@endphp

<aside class="hidden lg:flex lg:flex-col lg:fixed lg:inset-y-0 lg:w-72 lg:border-r lg:bg-white">
  <div class="h-16 px-4 flex items-center gap-3 border-b">
    <div class="h-10 w-10 rounded-2xl bg-amber-100 flex items-center justify-center text-sm font-bold">TB</div>
    <div class="min-w-0">
      <div class="font-extrabold text-slate-900 truncate">IA Tio Ben</div>
      <div class="text-xs text-slate-500 truncate">Admin</div>
    </div>
  </div>

  <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
    @foreach($nav as $item)
      <a href="{{ route($item['route']) }}"
         class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-semibold
                {{ $item['active'] ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
        <span class="text-xs uppercase tracking-wide">{{ $item['icon'] }}</span>
        <span class="truncate">{{ $item['label'] }}</span>
      </a>
    @endforeach
  </nav>

  <div class="p-3 border-t">
    <div class="rounded-2xl border bg-slate-50 px-3 py-3">
      <div class="text-xs text-slate-500">Logado como</div>
      <div class="text-sm font-semibold text-slate-900 truncate">{{ auth()->user()->name }}</div>
      <div class="text-xs text-slate-500 truncate">{{ auth()->user()->email }}</div>

      <form method="POST" action="{{ route('logout') }}" class="mt-3">
        @csrf
        <button class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold hover:bg-slate-50">
          Sair
        </button>
      </form>
    </div>
  </div>
</aside>

