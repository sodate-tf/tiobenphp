@extends('layouts.admin')

@section('title', 'Gerar Post IA - Admin')
@section('page_title', 'Gerar Post por Tema')
@section('page_subtitle', 'Mesmo fluxo do Apps Script: gerar, formatar e publicar automaticamente.')

@section('content')
  <div class="space-y-6">
    @if(session('success'))
      <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-900">
        {{ session('success') }}
      </div>
    @endif

    @if(session('warning'))
      <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
        {{ session('warning') }}
      </div>
    @endif

    @if(session('error'))
      <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-900">
        {{ session('error') }}
      </div>
    @endif

    @if($errors->any())
      <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-900">
        <ul class="list-disc pl-5 space-y-1">
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('admin.ops.generator.store') }}" class="rounded-2xl border border-slate-200 bg-white p-4 space-y-4">
      @csrf

      <div>
        <label class="text-sm font-semibold text-slate-900">Tema</label>
        <input type="text" name="topic" value="{{ old('topic') }}" required
          placeholder="Ex: Como viver a esperança cristã em tempos difíceis"
          class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div>
          <label class="text-sm font-semibold text-slate-900">Agente</label>
          <select name="agent" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
            <option value="theme" @selected(old('agent', 'theme') === 'theme')>theme</option>
            <option value="saint" @selected(old('agent') === 'saint')>saint</option>
          </select>
        </div>

        <div>
          <label class="text-sm font-semibold text-slate-900">Idioma</label>
          <select name="language" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
            <option value="pt-BR" @selected(old('language', 'pt-BR') === 'pt-BR')>pt-BR</option>
            <option value="en-US" @selected(old('language') === 'en-US')>en-US</option>
          </select>
        </div>

        <div>
          <label class="text-sm font-semibold text-slate-900">Data (opcional)</label>
          <input type="date" name="date" value="{{ old('date') }}"
            class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
      </div>

      <div>
        <label class="text-sm font-semibold text-slate-900">Keywords foco (opcional)</label>
        <input type="text" name="focusKeywords" value="{{ old('focusKeywords') }}"
          placeholder="Ex: esperança cristã, fé católica, oração diária"
          class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
      </div>

      <div>
        <label class="text-sm font-semibold text-slate-900">Fonte do Santo (opcional)</label>
        <textarea name="sourceText" rows="4" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"
          placeholder="[FONTE_SANTO] ...">{{ old('sourceText') }}</textarea>
      </div>

      <div>
        <label class="text-sm font-semibold text-slate-900">Liturgia (opcional)</label>
        <textarea name="liturgySource" rows="4" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"
          placeholder="[LITURGIA] ...">{{ old('liturgySource') }}</textarea>
      </div>

      <button class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white">
        Gerar post agora
      </button>
    </form>

    @if(!empty($lastRunId))
      <div id="run-tracker" data-run-id="{{ $lastRunId }}" class="rounded-2xl border border-sky-200 bg-sky-50 p-4 text-sm text-sky-900">
        <div class="font-semibold">Andamento da última execução</div>
        <div class="mt-2">Status: <span data-k="status">carregando...</span></div>
        <div>Etapa: <span data-k="stage">carregando...</span></div>
        <div>Mensagem: <span data-k="message">carregando...</span></div>
        <div class="mt-2 hidden" data-k="moderation_link_wrap">
          <a data-k="moderation_link" class="font-semibold underline" href="#">Abrir moderação desse artigo</a>
        </div>
      </div>
    @endif

    @if(!empty($runs) && $runs->count())
      <div class="rounded-2xl border border-slate-200 bg-white p-4">
        <h3 class="text-sm font-semibold text-slate-900">Últimas execuções</h3>
        <div class="mt-3 overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-700">
              <tr>
                <th class="px-3 py-2 text-left">Quando</th>
                <th class="px-3 py-2 text-left">Tema</th>
                <th class="px-3 py-2 text-left">Status</th>
                <th class="px-3 py-2 text-left">Etapa</th>
                <th class="px-3 py-2 text-left">Mensagem</th>
              </tr>
            </thead>
            <tbody>
              @foreach($runs as $run)
                <tr class="border-t">
                  <td class="px-3 py-2">{{ $run->created_at?->format('d/m H:i:s') }}</td>
                  <td class="px-3 py-2">{{ $run->topic }}</td>
                  <td class="px-3 py-2">{{ $run->status }}</td>
                  <td class="px-3 py-2">{{ $run->stage }}</td>
                  <td class="px-3 py-2">{{ $run->message }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    @endif

    @if(false && session('generated_article_id'))
      <div class="rounded-2xl border border-slate-200 bg-white p-4 text-sm text-slate-700">
        Artigo pipeline ID: <span class="font-mono">{{ session('generated_article_id') }}</span>
        @if(\Illuminate\Support\Facades\Route::has('admin.pipeline-moderation.show'))
          <a class="ml-2 font-semibold underline" href="{{ route('admin.pipeline-moderation.show', session('generated_article_id')) }}">
            Abrir moderação
          </a>
        @endif
      </div>
    @endif

    @if(session('api_response'))
      <div class="rounded-2xl border border-slate-200 bg-slate-900 text-slate-100 p-4">
        <h4 class="text-xs uppercase tracking-wide text-slate-300">Resposta da pipeline</h4>
        <pre class="mt-2 whitespace-pre-wrap text-xs leading-relaxed">{{ json_encode(session('api_response'), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
      </div>
    @endif
  </div>

  @if(!empty($lastRunId))
    <script>
      (function () {
        const box = document.getElementById('run-tracker');
        if (!box) return;
        const runId = box.dataset.runId;
        const statusEl = box.querySelector('[data-k="status"]');
        const stageEl = box.querySelector('[data-k="stage"]');
        const msgEl = box.querySelector('[data-k="message"]');
        const doneStatuses = ['completed', 'failed'];

        async function refresh() {
          try {
            const r = await fetch("{{ url('/admin/ops/generator/status') }}/" + encodeURIComponent(runId), {
              headers: { 'Accept': 'application/json' },
              credentials: 'same-origin',
            });
            if (!r.ok) return;
            const data = await r.json();

            statusEl.textContent = data.status || '-';
            stageEl.textContent = data.stage || '-';
            msgEl.textContent = data.message || '-';

            if (false && data.pipeline_article_id) {
              linkWrap.classList.remove('hidden');
              linkEl.href = "{{ url('/admin/pipeline-moderation') }}/" + encodeURIComponent(data.pipeline_article_id);
            }

            if (doneStatuses.includes((data.status || '').toLowerCase())) {
              clearInterval(window.__runTrackerInterval);
            }
          } catch (e) {
            // silencia erro de polling para nao poluir UI
          }
        }

        refresh();
        window.__runTrackerInterval = setInterval(refresh, 4000);
      })();
    </script>
  @endif
@endsection
