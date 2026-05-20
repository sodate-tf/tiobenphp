@php
  $link = $link ?? null;
@endphp

<div class="grid grid-cols-1 gap-4">

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
      <label class="text-sm font-semibold">Post (principal)</label>
      <select name="post_id" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required>
        <option value="">Selecione...</option>
        @foreach($posts as $p)
          <option value="{{ $p->id }}"
            @selected(old('post_id', $link->post_id ?? '') === $p->id)>
            {{ $p->title }}
          </option>
        @endforeach
      </select>
      @error('post_id') <div class="text-xs text-rose-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div>
      <label class="text-sm font-semibold">Post relacionado</label>
      <select name="linked_post_id" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required>
        <option value="">Selecione...</option>
        @foreach($posts as $p)
          <option value="{{ $p->id }}"
            @selected(old('linked_post_id', $link->linked_post_id ?? '') === $p->id)>
            {{ $p->title }}
          </option>
        @endforeach
      </select>
      @error('linked_post_id') <div class="text-xs text-rose-600 mt-1">{{ $message }}</div> @enderror
    </div>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div>
      <label class="text-sm font-semibold">Data</label>
      <input type="date" name="link_date"
             value="{{ old('link_date', optional($link->link_date ?? null)->format('Y-m-d')) }}"
             class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required />
      @error('link_date') <div class="text-xs text-rose-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div>
      <label class="text-sm font-semibold">Ordem</label>
      <input type="number" name="sort_order"
             value="{{ old('sort_order', $link->sort_order ?? 0) }}"
             class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" />
      @error('sort_order') <div class="text-xs text-rose-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="flex items-end">
      <label class="inline-flex items-center gap-2">
        <input type="checkbox" name="is_active" value="1"
               class="rounded border-slate-300"
               @checked(old('is_active', $link->is_active ?? true)) />
        <span class="text-sm font-semibold">Ativo</span>
      </label>
    </div>
  </div>

  <div>
    <label class="text-sm font-semibold">Parágrafo (texto do vínculo)</label>
    <textarea name="paragraph" rows="5"
              class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
              required>{{ old('paragraph', $link->paragraph ?? '') }}</textarea>
    @error('paragraph') <div class="text-xs text-rose-600 mt-1">{{ $message }}</div> @enderror
  </div>
</div>
