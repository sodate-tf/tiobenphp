@extends('layouts.admin')

@section('title', 'Liturgia ↔ Posts — Admin IA Tio Ben')
@section('page_title', 'Liturgia ↔ Posts')
@section('page_subtitle', 'Vincule um post a uma data da liturgia e conecte com outro post + parágrafo.')

@section('page_actions')
  <a href="{{ route('admin.liturgy-links.create') }}"
     class="inline-flex items-center justify-center rounded-xl bg-slate-900 text-white px-4 py-2 text-sm font-semibold hover:opacity-90">
    + Novo vínculo
  </a>
@endsection

@section('content')
  <form method="GET" class="mb-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
    <input name="q" value="{{ $q ?? '' }}" placeholder="Buscar por título..."
           class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" />

    <input type="date" name="date" value="{{ $date ?? '' }}"
           class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" />

    <button class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold hover:bg-slate-50">
      Filtrar
    </button>
  </form>

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead>
        <tr class="text-left text-slate-500">
          <th class="py-2 pr-3">Data</th>
          <th class="py-2 pr-3">Post</th>
          <th class="py-2 pr-3">Relacionado</th>
          <th class="py-2 pr-3">Ordem</th>
          <th class="py-2 pr-3">Status</th>
          <th class="py-2 text-right">Ações</th>
        </tr>
      </thead>
      <tbody class="divide-y">
        @forelse($links as $link)
          <tr class="align-top">
            <td class="py-3 pr-3 whitespace-nowrap">
              {{ optional($link->link_date)->format('d/m/Y') }}
            </td>
            <td class="py-3 pr-3">
              <div class="font-semibold text-slate-900">{{ $link->post->title ?? '—' }}</div>
              <div class="text-xs text-slate-500 line-clamp-2 mt-1">{{ $link->paragraph }}</div>
            </td>
            <td class="py-3 pr-3">
              {{ $link->linkedPost->title ?? '—' }}
            </td>
            <td class="py-3 pr-3">
              {{ $link->sort_order }}
            </td>
            <td class="py-3 pr-3">
              <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold
                {{ $link->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                {{ $link->is_active ? 'Ativo' : 'Inativo' }}
              </span>
            </td>
            <td class="py-3 text-right whitespace-nowrap">
              <a href="{{ route('admin.liturgy-links.edit', $link) }}"
                 class="inline-flex rounded-lg border px-3 py-1.5 text-xs font-semibold hover:bg-slate-50">
                Editar
              </a>

              <form method="POST" action="{{ route('admin.liturgy-links.destroy', $link) }}" class="inline">
                @csrf @method('DELETE')
                <button class="inline-flex rounded-lg border px-3 py-1.5 text-xs font-semibold hover:bg-slate-50"
                        onclick="return confirm('Desativar este vínculo?')">
                  Desativar
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="py-6 text-center text-slate-500">Nenhum vínculo encontrado.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $links->links() }}
  </div>
@endsection
