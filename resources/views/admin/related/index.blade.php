@extends('layouts.admin')

@section('title', 'Posts Relacionados — Admin IA Tio Ben')
@section('page_title', 'Posts Relacionados')
@section('page_subtitle', 'Escolha um post para gerenciar os posts relacionados.')

@section('content')
  <form method="GET" class="mb-4">
    <div class="flex flex-col sm:flex-row gap-2">
      <input
        type="text"
        name="q"
        value="{{ $q }}"
        placeholder="Buscar por título..."
        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
      />
      <button class="rounded-xl bg-amber-50 text-white px-4 py-2 text-sm font-semibold">
        Buscar
      </button>
    </div>
  </form>

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead>
        <tr class="text-left text-slate-600 border-b">
          <th class="py-2 pr-4">Título</th>
          <th class="py-2 pr-4">Lang</th>
          <th class="py-2 pr-4">Publicação</th>
          <th class="py-2 pr-2 text-right">Ação</th>
        </tr>
      </thead>
      <tbody class="divide-y">
        @foreach($posts as $p)
          <tr>
            <td class="py-3 pr-4">
              <div class="font-semibold text-slate-900">{{ $p->title }}</div>
              <div class="text-xs text-slate-500 break-all">{{ $p->id }}</div>
            </td>
            <td class="py-3 pr-4">{{ $p->lang }}</td>
            <td class="py-3 pr-4">
              {{ optional($p->publish_date)->format('d/m/Y H:i') }}
            </td>
            <td class="py-3 pr-2 text-right">
              <a href="{{ route('admin.related.edit', $p->id) }}"
                 class="inline-flex items-center rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold hover:bg-slate-50">
                Gerenciar
              </a>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $posts->links() }}
  </div>
@endsection
