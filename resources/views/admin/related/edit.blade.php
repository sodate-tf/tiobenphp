@extends('layouts.admin')

@section('title', 'Gerenciar Relacionados — Admin IA Tio Ben')
@section('page_title', 'Gerenciar Relacionados')
@section('page_subtitle', $post->title)

@section('page_actions')
  <a href="{{ route('admin.related.index') }}"
     class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold hover:bg-slate-50">
    Voltar
  </a>
@endsection

@section('content')
  {{-- Adicionar relacionado --}}
  <div class="rounded-2xl border border-slate-200 p-4 mb-6">
    <div class="text-sm font-semibold mb-3">Adicionar post relacionado</div>

    @if($errors->any())
      <div class="mb-3 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-800">
        <ul class="list-disc pl-5">
          @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('admin.related.store', $post->id) }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
      @csrf

      <div class="sm:col-span-8">
        <label class="block text-xs font-semibold text-slate-600 mb-1">Post relacionado</label>
        <select name="related_post_id" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
          <option value="">Selecione...</option>
          @foreach($availablePosts as $p)
            <option value="{{ $p->id }}">
              {{ $p->title }} ({{ $p->lang }}) — {{ optional($p->publish_date)->format('d/m/Y') }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="sm:col-span-2">
        <label class="block text-xs font-semibold text-slate-600 mb-1">Ordem</label>
        <input
          type="number"
          name="sort_order"
          value="0"
          min="0"
          class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
        />
      </div>

      <div class="sm:col-span-2 flex items-end">
        <button class="w-full rounded-xl bg-slate-900 text-white px-4 py-2 text-sm font-semibold">
          Adicionar
        </button>
      </div>
    </form>
  </div>

  {{-- Lista + ordenação --}}
  <div class="flex items-center justify-between mb-3">
    <div class="text-sm font-semibold">Relacionados ({{ $items->count() }})</div>
  </div>

  @if($items->isEmpty())
    <div class="text-sm text-slate-600">
      Nenhum post relacionado ainda.
    </div>
  @else
    <form method="POST" action="{{ route('admin.related.reorder', $post->id) }}" class="mb-4">
      @csrf

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="text-left text-slate-600 border-b">
              <th class="py-2 pr-4">Relacionado</th>
              <th class="py-2 pr-4 w-28">Ordem</th>
              <th class="py-2 pr-2 text-right">Ação</th>
            </tr>
          </thead>

          <tbody class="divide-y">
            @foreach($items as $i => $item)
              <tr>
                <td class="py-3 pr-4">
                  <div class="font-semibold text-slate-900">
                    {{ optional($item->relatedPost)->title ?? '(Post não encontrado)' }}
                  </div>
                  <div class="text-xs text-slate-500">
                    {{ optional($item->relatedPost)->lang }} — {{ optional(optional($item->relatedPost)->publish_date)->format('d/m/Y') }}
                  </div>
                </td>

                <td class="py-3 pr-4">
                  <input type="hidden" name="orders[{{ $i }}][id]" value="{{ $item->id }}">
                  <input
                    type="number"
                    name="orders[{{ $i }}][sort_order]"
                    value="{{ $item->sort_order }}"
                    min="0"
                    class="w-24 rounded-xl border border-slate-200 px-3 py-2 text-sm"
                  />
                </td>

                <td class="py-3 pr-2 text-right">
                  <form method="POST" action="{{ route('admin.related.destroy', [$post->id, $item->id]) }}">
                    @csrf
                    @method('DELETE')
                    <button class="inline-flex items-center rounded-xl border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-50">
                      Remover
                    </button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="mt-3 flex justify-end">
        <button class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold hover:bg-slate-50">
          Salvar ordem
        </button>
      </div>
    </form>
  @endif
@endsection
