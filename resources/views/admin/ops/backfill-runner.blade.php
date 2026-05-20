@extends('layouts.admin')

@section('title', 'Operações SEO/IA — Admin IA Tio Ben')
@section('page_title', 'Operações SEO/IA')
@section('page_subtitle', 'Execute migração e backfills direto pelo navegador (sem SSH).')

@section('content')
  <div class="space-y-6">
    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
      Use primeiro os modos <strong>DRY-RUN</strong> e só depois rode em <strong>WRITE</strong>.
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      <form method="POST" action="{{ route('admin.ops.backfills.migrate') }}" class="rounded-2xl border border-slate-200 p-4">
        @csrf
        <h3 class="text-sm font-semibold">1) Rodar Migration</h3>
        <p class="text-xs text-slate-600 mt-1">Aplica migrations pendentes (`php artisan migrate --force`).</p>
        <button class="mt-4 rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-semibold hover:bg-slate-50">
          Executar Migration
        </button>
      </form>

      <form method="POST" action="{{ route('admin.ops.backfills.english') }}" class="rounded-2xl border border-slate-200 p-4">
        @csrf
        <h3 class="text-sm font-semibold">2) Backfill EN</h3>
        <p class="text-xs text-slate-600 mt-1">Gera posts em inglês para posts PT antigos.</p>

        <div class="mt-3">
          <label class="text-xs text-slate-600">Limit</label>
          <input type="number" name="limit" value="20" min="1" max="500" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>

        <label class="mt-3 flex items-center gap-2 text-sm">
          <input type="checkbox" name="write" value="1" class="rounded border-slate-300" />
          Rodar em WRITE (desmarcado = DRY-RUN)
        </label>

        <button class="mt-4 rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-semibold hover:bg-slate-50">
          Executar Backfill EN
        </button>
      </form>

      <form method="POST" action="{{ route('admin.ops.backfills.pairs') }}" class="rounded-2xl border border-slate-200 p-4 lg:col-span-2">
        @csrf
        <h3 class="text-sm font-semibold">3) Parear PT/EN por UUID</h3>
        <p class="text-xs text-slate-600 mt-1">Faz pareamento técnico para hreflang e alternates.</p>

        <div class="mt-3 max-w-sm">
          <label class="text-xs text-slate-600">Limit</label>
          <input type="number" name="limit" value="200" min="1" max="5000" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>

        <label class="mt-3 flex items-center gap-2 text-sm">
          <input type="checkbox" name="write" value="1" class="rounded border-slate-300" />
          Rodar em WRITE (desmarcado = DRY-RUN)
        </label>

        <button class="mt-4 rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-semibold hover:bg-slate-50">
          Executar Pareamento PT/EN
        </button>
      </form>
    </div>

    @if(session('command_output'))
      <div class="rounded-2xl border border-slate-200 bg-slate-900 text-slate-100 p-4">
        <h4 class="text-xs uppercase tracking-wide text-slate-300">Saída do comando</h4>
        <pre class="mt-2 whitespace-pre-wrap text-xs leading-relaxed">{{ session('command_output') }}</pre>
      </div>
    @endif
  </div>
@endsection

