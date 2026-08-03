@extends('layouts.admin')

@section('title', 'Console Artisan - Admin IA Tio Ben')
@section('page_title', 'Console Artisan')
@section('page_subtitle', 'Execute comandos permitidos do Laravel sem SSH.')

@section('content')
  <div class="space-y-6">
    @if(session('success'))
      <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-900">
        {{ session('success') }}
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

    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
      Esta tela executa apenas comandos da whitelist por seguranca. Para migracoes use, por exemplo:
      <code class="font-mono text-xs">migrate</code> ou
      <code class="font-mono text-xs">migrate:rollback --step=1</code>.
    </div>

    <form method="POST" action="{{ route('admin.ops.artisan.run') }}" class="rounded-2xl border border-slate-200 bg-white p-4">
      @csrf
      <label class="text-sm font-semibold text-slate-900">Comando Artisan</label>
      <input
        type="text"
        name="command"
        value="{{ old('command', session('last_command', 'migrate')) }}"
        placeholder="Ex: migrate"
        class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm font-mono"
      />
      <button class="mt-4 rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-semibold hover:bg-slate-50">
        Executar comando
      </button>
    </form>

    <div class="rounded-2xl border border-slate-200 bg-white p-4">
      <h3 class="text-sm font-semibold text-slate-900">Comandos permitidos</h3>
      <div class="mt-3 flex flex-wrap gap-2">
        @foreach($allowedCommands as $cmd)
          <span class="rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-xs font-mono text-slate-700">{{ $cmd }}</span>
        @endforeach
      </div>
    </div>

    @if(session('command_output'))
      <div class="rounded-2xl border border-slate-200 bg-slate-900 text-slate-100 p-4">
        <h4 class="text-xs uppercase tracking-wide text-slate-300">Saida do comando</h4>
        <pre class="mt-2 whitespace-pre-wrap text-xs leading-relaxed">{{ session('command_output') }}</pre>
      </div>
    @endif
  </div>
@endsection

