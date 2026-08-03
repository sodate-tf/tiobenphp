@extends('layouts.admin')

@section('title', 'Revisao de Pipeline - Admin')
@section('page_title', 'Revisao de Artigo IA')
@section('page_subtitle', 'Avalie qualidade, conteudo e decida aprovar ou recusar.')

@section('content')
  @if(session('success'))
    <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
      {{ session('success') }}
    </div>
  @endif

  <div class="mb-4 rounded-2xl border border-slate-200 bg-white p-4">
    <div class="text-xs text-slate-500">ID</div>
    <div class="font-mono text-xs text-slate-700 break-all">{{ $article->id }}</div>
    <h2 class="mt-2 text-xl font-bold text-slate-900">{{ $article->title ?: $article->topic }}</h2>
    <div class="mt-2 text-sm text-slate-600">
      Status: <b>{{ $article->moderation_status ?: 'pending' }}</b> |
      Publicado em: <b>{{ $article->published_at?->format('d/m/Y H:i') ?: '-' }}</b>
    </div>
  </div>

  @php $quality = $article->quality_report ?? []; @endphp
  <div class="mb-4 rounded-2xl border border-slate-200 bg-white p-4">
    <h3 class="font-semibold text-slate-900">Resumo de Qualidade</h3>
    <div class="mt-2 grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
      <div class="rounded-xl border p-2">Aprovado: <b>{{ ($quality['approved'] ?? false) ? 'sim' : 'nao' }}</b></div>
      <div class="rounded-xl border p-2">Palavras: <b>{{ $quality['word_count'] ?? '-' }}</b></div>
      <div class="rounded-xl border p-2">H2: <b>{{ $quality['h2_count'] ?? '-' }}</b></div>
      <div class="rounded-xl border p-2">FAQ: <b>{{ $quality['faq_questions'] ?? '-' }}</b></div>
    </div>
  </div>

  <div class="mb-4 rounded-2xl border border-slate-200 bg-white p-4">
    <h3 class="font-semibold text-slate-900 mb-2">Markdown Gerado</h3>
    <pre class="max-h-[520px] overflow-auto whitespace-pre-wrap rounded-xl border border-slate-200 bg-slate-50 p-3 text-xs text-slate-800">{{ $article->content_raw }}</pre>
  </div>

  @if(!$article->published_at)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      <form method="POST" action="{{ route('admin.pipeline-moderation.approve', $article->id) }}" class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
        @csrf
        <h3 class="font-semibold text-emerald-900">Aprovar e Publicar</h3>
        <textarea name="review_notes" rows="3" placeholder="Observacao interna (opcional)"
          class="mt-2 w-full rounded-xl border border-emerald-200 px-3 py-2 text-sm"></textarea>
        <button class="mt-3 w-full rounded-xl bg-emerald-700 px-3 py-2 text-sm font-semibold text-white">
          Aprovar agora
        </button>
      </form>

      <form method="POST" action="{{ route('admin.pipeline-moderation.reject', $article->id) }}" class="rounded-2xl border border-rose-200 bg-rose-50 p-4">
        @csrf
        <h3 class="font-semibold text-rose-900">Recusar</h3>
        <textarea name="rejection_reason" rows="3" required placeholder="Motivo da recusa"
          class="mt-2 w-full rounded-xl border border-rose-200 px-3 py-2 text-sm"></textarea>
        <button class="mt-3 w-full rounded-xl bg-rose-700 px-3 py-2 text-sm font-semibold text-white">
          Recusar artigo
        </button>
      </form>
    </div>
  @endif
@endsection

