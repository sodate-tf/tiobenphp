{{-- resources/views/admin/categories/form.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-6">
  <div class="mb-5 flex items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl font-black text-slate-950">{{ $category->exists ? 'Editar categoria' : 'Nova categoria' }}</h1>
      <p class="mt-1 text-sm text-slate-600">O slug é usado nas URLs públicas do blog.</p>
    </div>
    <a href="{{ route('admin.categories.index') }}" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-bold text-slate-700 hover:bg-slate-50">Voltar</a>
  </div>

  @if($errors->any())
    <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
      <ul class="list-disc pl-5">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}" class="space-y-5 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
    @csrf
    @if($category->exists)
      @method('PUT')
    @endif

    <div>
      <label class="text-sm font-bold text-slate-800" for="name">Nome</label>
      <input id="name" name="name" value="{{ old('name', $category->name) }}" required class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-slate-400" />
    </div>

    <div>
      <label class="text-sm font-bold text-slate-800" for="slug">Slug</label>
      <input id="slug" name="slug" value="{{ old('slug', $category->slug) }}" placeholder="deixe vazio para gerar automaticamente" class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-slate-400" />
      <p class="mt-1 text-xs text-slate-500">Use apenas letras minúsculas, números e hífen. Ex.: cristao-catolico-e-financas.</p>
    </div>

    <div>
      <label class="text-sm font-bold text-slate-800" for="description">Descrição editorial</label>
      <textarea id="description" name="description" rows="3" class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-slate-400">{{ old('description', $category->description) }}</textarea>
    </div>

    <div>
      <label class="text-sm font-bold text-slate-800" for="meta_title">Meta title da categoria</label>
      <input id="meta_title" name="meta_title" value="{{ old('meta_title', $category->meta_title) }}" class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-slate-400" />
    </div>

    <div>
      <label class="text-sm font-bold text-slate-800" for="meta_description">Meta description da categoria</label>
      <textarea id="meta_description" name="meta_description" rows="3" class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-slate-400">{{ old('meta_description', $category->meta_description) }}</textarea>
    </div>

    <div class="flex justify-end gap-2">
      <a href="{{ route('admin.categories.index') }}" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-bold text-slate-700 hover:bg-slate-50">Cancelar</a>
      <button class="rounded-xl bg-slate-950 px-5 py-2 text-sm font-bold text-white hover:bg-slate-800">Salvar</button>
    </div>
  </form>
</div>
@endsection
