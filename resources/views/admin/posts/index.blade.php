@extends('layouts.admin')

@section('title', 'Posts — Admin')
@section('page_title', 'Posts')
@section('page_subtitle', 'PT/EN, ativos e destaques.')

@section('content')
  @if(session('success'))
    <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
      {{ session('success') }}
    </div>
  @endif

  <form method="GET" class="mb-4 grid grid-cols-1 sm:grid-cols-5 gap-3">
    <input name="q" value="{{ $q }}" placeholder="Buscar por título/slug..."
      class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm sm:col-span-2" />

    <select name="lang" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
      <option value="pt" @selected($lang==='pt')>PT</option>
      <option value="en" @selected($lang==='en')>EN</option>
    </select>

    <select name="active" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
      <option value="all" @selected($active==='all')>Ativo: todos</option>
      <option value="1" @selected($active==='1')>Ativo: sim</option>
      <option value="0" @selected($active==='0')>Ativo: não</option>
    </select>

    <select name="featured" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
      <option value="all" @selected($featured==='all')>Destaque: todos</option>
      <option value="1" @selected($featured==='1')>Destaque: sim</option>
      <option value="0" @selected($featured==='0')>Destaque: não</option>
    </select>

    <div class="flex gap-2 sm:col-span-5">
      <button class="flex-1 rounded-xl bg-slate-900 px-3 py-2 text-sm font-semibold text-white">Filtrar</button>
      <a href="{{ route('admin.posts.create', ['lang' => $lang]) }}"
         class="flex-1 rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-center hover:bg-slate-50">
        + Novo
      </a>
    </div>
  </form>

  <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 text-slate-700">
        <tr>
          <th class="px-4 py-3 text-left">Título</th>
          <th class="px-4 py-3 text-left">Categoria</th>
          <th class="px-4 py-3 text-left">Ativo</th>
          <th class="px-4 py-3 text-left">Destaque</th>
          <th class="px-4 py-3 text-left">Publicação</th>
          <th class="px-4 py-3 text-right">Ações</th>
        </tr>
      </thead>
      <tbody>
        @forelse($posts as $post)
          <tr class="border-t">
            <td class="px-4 py-3">
              <div class="font-semibold text-slate-900">{{ $post->title }}</div>
              <div class="text-xs text-slate-500">/{{ $post->slug }} ({{ strtoupper($post->lang) }})</div>
            </td>
            <td class="px-4 py-3">{{ $post->category?->name ?? '—' }}</td>
            <td class="px-4 py-3">
              <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $post->is_active ? 'bg-emerald-100 text-emerald-900' : 'bg-slate-100 text-slate-700' }}">
                {{ $post->is_active ? 'sim' : 'não' }}
              </span>
            </td>
            <td class="px-4 py-3">
              <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $post->is_featured ? 'bg-amber-100 text-amber-900' : 'bg-slate-100 text-slate-700' }}">
                {{ $post->is_featured ? 'sim' : 'não' }}
              </span>
            </td>
            <td class="px-4 py-3">{{ $post->publish_date?->format('d/m/Y H:i') ?? '—' }}</td>
            <td class="px-4 py-3 text-right whitespace-nowrap">
              <a href="{{ route('admin.posts.edit', $post) }}"
                 class="rounded-lg border border-slate-200 px-3 py-1.5 hover:bg-slate-50">Editar</a>

              <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="inline">
                @csrf @method('DELETE')
                <button onclick="return confirm('Remover este post?')"
                        class="rounded-lg border border-rose-200 px-3 py-1.5 text-rose-700 hover:bg-rose-50">
                  Remover
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="px-4 py-8 text-center text-slate-500">Nenhum post encontrado.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $posts->links() }}</div>
@endsection
