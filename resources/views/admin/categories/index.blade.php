{{-- resources/views/admin/categories/index.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-5xl px-4 py-6">
  <div class="mb-5 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
    <div>
      <h1 class="text-2xl font-black text-slate-950">Categorias</h1>
      <p class="mt-1 text-sm text-slate-600">Editorias e hubs do blog.</p>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="rounded-xl bg-slate-950 px-4 py-2 text-sm font-bold text-white hover:bg-slate-800">Nova categoria</a>
  </div>

  <form method="GET" class="mb-5 flex gap-2">
    <input name="q" value="{{ $q ?? '' }}" placeholder="Buscar categoria..." class="min-w-0 flex-1 rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-slate-400" />
    <button class="rounded-2xl bg-slate-950 px-4 py-3 text-sm font-bold text-white">Buscar</button>
  </form>

  @if(session('success'))
    <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-800">{{ session('success') }}</div>
  @endif

  <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
    <table class="w-full text-left text-sm">
      <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
        <tr>
          <th class="px-4 py-3">Nome</th>
          <th class="px-4 py-3">Slug</th>
          <th class="px-4 py-3">Meta</th>
          <th class="px-4 py-3 text-right">Ações</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($categories as $category)
          <tr>
            <td class="px-4 py-3 font-bold text-slate-950">{{ $category->name }}</td>
            <td class="px-4 py-3 text-slate-600">{{ $category->slug ?: '—' }}</td>
            <td class="px-4 py-3 text-slate-600">
              <div class="max-w-md line-clamp-2">{{ $category->meta_description ?: $category->description ?: '—' }}</div>
            </td>
            <td class="px-4 py-3 text-right">
              <a href="{{ route('admin.categories.edit', $category) }}" class="font-bold text-slate-900 hover:underline">Editar</a>
            </td>
          </tr>
        @empty
          <tr><td colspan="4" class="px-4 py-8 text-center text-slate-600">Nenhuma categoria encontrada.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-5">{{ $categories->links() }}</div>
</div>
@endsection
