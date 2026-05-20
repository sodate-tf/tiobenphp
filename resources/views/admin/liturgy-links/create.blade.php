@extends('layouts.admin')

@section('title', 'Novo vínculo — Liturgia ↔ Post')
@section('page_title', 'Novo vínculo')
@section('page_subtitle', 'Escolha um post, defina uma data e escreva um parágrafo.')

@section('content')
  <form method="POST" action="{{ route('admin.liturgy-links.store') }}" class="space-y-4">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="text-sm font-medium">Post</label>
        <select name="post_id" class="mt-1 w-full rounded-xl border-slate-200" required>
          <option value="">Selecione...</option>
          @foreach($posts as $p)
            <option value="{{ $p->id }}" @selected(old('post_id') === $p->id)>
              {{ $p->title }}
            </option>
          @endforeach
        </select>
        @error('post_id') <div class="text-xs text-rose-600 mt-1">{{ $message }}</div> @enderror
      </div>

      <div>
        <label class="text-sm font-medium">Data (Liturgia)</label>
        <input type="date" name="link_date" value="{{ old('link_date') }}" class="mt-1 w-full rounded-xl border-slate-200" required />
        @error('link_date') <div class="text-xs text-rose-600 mt-1">{{ $message }}</div> @enderror
      </div>
    </div>

    <div>
      <label class="text-sm font-medium">Parágrafo</label>
      <textarea name="paragraph" rows="6" class="mt-1 w-full rounded-xl border-slate-200" required>{{ old('paragraph') }}</textarea>
      @error('paragraph') <div class="text-xs text-rose-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="flex items-center gap-2">
        <input id="is_active" type="checkbox" name="is_active" value="1" class="rounded border-slate-300"
               @checked(old('is_active', '1') == '1')>
        <label for="is_active" class="text-sm">Ativo</label>
      </div>

      <div>
        <label class="text-sm font-medium">Ordem</label>
        <input type="number" min="0" name="sort_order" value="{{ old('sort_order', 0) }}" class="mt-1 w-full rounded-xl border-slate-200" />
        @error('sort_order') <div class="text-xs text-rose-600 mt-1">{{ $message }}</div> @enderror
      </div>
    </div>

    <div class="flex gap-2">
      <a href="{{ route('admin.liturgy-links.index') }}" class="px-4 py-2 rounded-xl border border-slate-200">
        Voltar
      </a>
      <button class="px-4 py-2 rounded-xl bg-slate-900 text-white">
        Salvar
      </button>
    </div>
  </form>
@endsection
