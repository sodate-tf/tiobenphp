@extends('layouts.admin')

@section('title', 'Dashboard - Admin IA Tio Ben')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Visao geral do conteudo e atalhos.')

@section('content')
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <a href="{{ route('admin.posts.index') }}" class="rounded-2xl border border-slate-200 p-4 hover:bg-slate-50">
      <div class="text-sm font-semibold">Posts</div>
      <div class="text-xs text-slate-600 mt-1">Criar, editar e publicar.</div>
    </a>

    <a href="{{ route('admin.categories.index') }}" class="rounded-2xl border border-slate-200 p-4 hover:bg-slate-50">
      <div class="text-sm font-semibold">Categorias</div>
      <div class="text-xs text-slate-600 mt-1">Organizacao e SEO.</div>
    </a>

    <a href="{{ route('admin.liturgy-links.index') }}" class="rounded-2xl border border-slate-200 p-4 hover:bg-slate-50">
      <div class="text-sm font-semibold">Liturgia -> Posts</div>
      <div class="text-xs text-slate-600 mt-1">Vincular leitura do dia a conteudo.</div>
    </a>

    <a href="{{ route('admin.ops.backfills.index') }}" class="rounded-2xl border border-slate-200 p-4 hover:bg-slate-50">
      <div class="text-sm font-semibold">Operacoes SEO/IA</div>
      <div class="text-xs text-slate-600 mt-1">Rodar migrate e backfills sem SSH.</div>
    </a>
  </div>
@endsection

