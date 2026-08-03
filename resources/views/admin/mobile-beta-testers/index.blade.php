@extends('layouts.admin')

@section('title', 'Testadores do App - Admin IA Tio Ben')
@section('page_title', 'Testadores do App')
@section('page_subtitle', 'Cadastros recebidos pelo banner das paginas de liturgia em portugues.')

@section('content')
  <div class="space-y-6">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
      <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total cadastrados</div>
        <div class="mt-2 text-3xl font-black text-slate-950">{{ $totalCount }}</div>
      </div>

      <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-amber-700">Aguardando envio</div>
        <div class="mt-2 text-3xl font-black text-amber-900">{{ $pendingCount }}</div>
      </div>

      <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Link enviado</div>
        <div class="mt-2 text-3xl font-black text-emerald-900">{{ $sentCount }}</div>
      </div>
    </div>

    <form method="GET" class="grid grid-cols-1 gap-3 sm:grid-cols-[minmax(0,1fr)_220px_140px]">
      <input
        name="q"
        value="{{ $q }}"
        placeholder="Buscar por e-mail ou telefone..."
        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-slate-400"
      />

      <select name="status" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
        <option value="all" @selected($status === 'all')>Todos os status</option>
        <option value="pending" @selected($status === 'pending')>Aguardando envio</option>
        <option value="sent" @selected($status === 'sent')>Link enviado</option>
      </select>

      <button class="rounded-2xl bg-slate-950 px-4 py-3 text-sm font-bold text-white">Filtrar</button>
    </form>

    <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-slate-700">
          <tr>
            <th class="px-4 py-3 text-left">E-mail</th>
            <th class="px-4 py-3 text-left">Telefone</th>
            <th class="px-4 py-3 text-left">Data</th>
            <th class="px-4 py-3 text-left">Status</th>
            <th class="px-4 py-3 text-left">Link enviado em</th>
            <th class="px-4 py-3 text-right">Acoes</th>
          </tr>
        </thead>
        <tbody>
          @forelse($testers as $tester)
            <tr class="border-t border-slate-100">
              <td class="px-4 py-3">
                <div class="font-semibold text-slate-900">{{ $tester->google_email }}</div>
                <div class="text-xs text-slate-500">{{ $tester->source_url ?: 'Origem nao informada' }}</div>
              </td>
              <td class="px-4 py-3 text-slate-700">{{ $tester->whatsapp }}</td>
              <td class="px-4 py-3 text-slate-700">{{ $tester->created_at?->format('d/m/Y H:i') ?: '—' }}</td>
              <td class="px-4 py-3">
                <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $tester->link_sent ? 'bg-emerald-100 text-emerald-900' : 'bg-amber-100 text-amber-900' }}">
                  {{ $tester->link_sent ? 'Link enviado' : 'Pendente' }}
                </span>
              </td>
              <td class="px-4 py-3 text-slate-700">{{ $tester->link_sent_at?->format('d/m/Y H:i') ?: '—' }}</td>
              <td class="px-4 py-3 text-right">
                @if($tester->link_sent)
                  <span class="inline-flex rounded-xl border border-emerald-200 px-3 py-2 text-xs font-bold text-emerald-700">
                    Enviado
                  </span>
                @else
                  <form method="POST" action="{{ route('admin.mobile-beta-testers.mark-sent', $tester) }}" class="inline">
                    @csrf
                    @method('PATCH')
                    <button class="rounded-xl bg-slate-900 px-3 py-2 text-xs font-bold text-white hover:bg-slate-800">
                      Link enviado
                    </button>
                  </form>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-4 py-8 text-center text-slate-500">Nenhum testador cadastrado ainda.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div>{{ $testers->links() }}</div>
  </div>
@endsection
