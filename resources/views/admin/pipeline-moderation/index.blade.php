@extends('layouts.admin')

@section('title', 'Moderacao de Pipeline - Admin')
@section('page_title', 'Moderacao de Posts IA')
@section('page_subtitle', 'Aprovacao manual para artigos que nao bateram no gate automatico.')

@section('content')
  @if(session('success'))
    <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
      {{ session('success') }}
    </div>
  @endif

  <form method="GET" class="mb-4 grid grid-cols-1 sm:grid-cols-4 gap-3">
    <input name="q" value="{{ $q }}" placeholder="Buscar por topico/titulo/slug..."
      class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm sm:col-span-2" />

    <select name="status" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
      <option value="needs_review" @selected($status==='needs_review')>Precisa revisao</option>
      <option value="pending" @selected($status==='pending')>Pendente</option>
      <option value="approved_manual" @selected($status==='approved_manual')>Aprovado manual</option>
      <option value="auto_published" @selected($status==='auto_published')>Publicado automatico</option>
      <option value="rejected" @selected($status==='rejected')>Rejeitado</option>
      <option value="all" @selected($status==='all')>Todos</option>
    </select>

    <button class="rounded-xl bg-slate-900 px-3 py-2 text-sm font-semibold text-white">Filtrar</button>
  </form>

  <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 text-slate-700">
        <tr>
          <th class="px-4 py-3 text-left">Topico</th>
          <th class="px-4 py-3 text-left">Status</th>
          <th class="px-4 py-3 text-left">Qualidade</th>
          <th class="px-4 py-3 text-left">Criado</th>
          <th class="px-4 py-3 text-right">Acao</th>
        </tr>
      </thead>
      <tbody>
        @forelse($articles as $article)
          @php
            $quality = $article->quality_report ?? [];
          @endphp
          <tr class="border-t">
            <td class="px-4 py-3">
              <div class="font-semibold text-slate-900">{{ $article->title ?: $article->topic }}</div>
              <div class="text-xs text-slate-500">{{ $article->id }}</div>
            </td>
            <td class="px-4 py-3">
              <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold bg-slate-100 text-slate-700">
                {{ $article->moderation_status ?: 'pending' }}
              </span>
            </td>
            <td class="px-4 py-3 text-xs text-slate-600">
              {{ $quality['word_count'] ?? '-' }} palavras /
              H2: {{ $quality['h2_count'] ?? '-' }} /
              FAQ: {{ $quality['faq_questions'] ?? '-' }}
            </td>
            <td class="px-4 py-3">{{ $article->created_at?->format('d/m/Y H:i') }}</td>
            <td class="px-4 py-3 text-right">
              <a href="{{ route('admin.pipeline-moderation.show', $article->id) }}"
                 class="rounded-lg border border-slate-200 px-3 py-1.5 hover:bg-slate-50">
                Revisar
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="px-4 py-8 text-center text-slate-500">Nenhum item para este filtro.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $articles->links() }}</div>
@endsection

