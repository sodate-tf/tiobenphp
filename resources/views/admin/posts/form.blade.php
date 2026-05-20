@extends('layouts.admin')

@section('title', $post->exists ? 'Editar Post' : 'Novo Post')
@section('page_title', $post->exists ? 'Editar Post' : 'Novo Post')
@section('page_subtitle', 'Campos reais da sua tabela.')

@section('content')
  @if($errors->any())
    <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
      <ul class="list-disc pl-5">
        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  @if(session('success'))
    <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
      {{ session('success') }}
    </div>
  @endif

  @if(session('status'))
    <div class="mb-4 rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-900">
      {{ session('status') }}
    </div>
  @endif

  {{-- ✅ BOTÃO GERAR COVER (somente quando post já existe) --}}
  @if($post->exists)
    @php
      $st = (string)($post->cover_generation_status ?? '');
      $disabled = in_array($st, ['processing'], true);

      $badge = match ($st) {
        'done' => 'bg-emerald-100 text-emerald-900 border-emerald-200',
        'failed' => 'bg-rose-100 text-rose-900 border-rose-200',
        'processing' => 'bg-amber-100 text-amber-900 border-amber-200',
        'queued' => 'bg-sky-100 text-sky-900 border-sky-200',
        default => 'bg-slate-100 text-slate-900 border-slate-200',
      };
    @endphp

    <div class="mb-4 rounded-2xl border border-slate-200 bg-white p-4">
      <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-wrap items-center gap-2">
          <span class="text-sm font-semibold">Cover IA</span>

          <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $badge }}">
            Status: {{ $st !== '' ? $st : '—' }}
          </span>

          @if(!empty($post->cover_generated_at))
            <span class="text-xs text-slate-600">
              Gerado em: {{ optional($post->cover_generated_at)->format('d/m/Y H:i') }}
            </span>
          @endif
        </div>

        <div class="flex gap-2">
          <form method="POST" action="{{ route('admin.posts.generateCover', $post) }}">
            @csrf
            <button type="submit"
              class="rounded-xl bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700 disabled:opacity-60 disabled:cursor-not-allowed"
              {{ $disabled ? 'disabled' : '' }}>
              Gerar cover
            </button>
          </form>

          @if(!empty($post->cover_image_url))
            <a href="{{ $post->cover_image_url }}" target="_blank" rel="noopener"
              class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-center hover:bg-slate-50">
              Abrir imagem
            </a>
          @endif
        </div>
      </div>

      <p class="mt-2 text-xs text-slate-600">
        Obs: com <code class="px-1 rounded bg-slate-100">QUEUE_CONNECTION=database</code>, precisa do cron/worker para processar a fila.
      </p>
    </div>
  @endif

  <form method="POST"
        action="{{ $post->exists ? route('admin.posts.update', $post) : route('admin.posts.store') }}"
        enctype="multipart/form-data"
        class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    @csrf
    @if($post->exists) @method('PUT') @endif

    <div class="lg:col-span-2 space-y-4">
      <div class="rounded-2xl border border-slate-200 bg-white p-4 space-y-3">
        <div>
          <label class="text-sm font-semibold">Título</label>
          <input name="title" value="{{ old('title', $post->title) }}"
                 class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" />
        </div>

        <div>
          <label class="text-sm font-semibold">Slug</label>
          <input name="slug" value="{{ old('slug', $post->slug) }}"
                 class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" />
        </div>

        <div>
          <label class="text-sm font-semibold">Keywords (texto)</label>
          <textarea name="keywords" rows="2"
            class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">{{ old('keywords', $post->keywords) }}</textarea>
        </div>

        <div>
          <label class="text-sm font-semibold">Meta description</label>
          <textarea name="meta_description" rows="3"
            class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">{{ old('meta_description', $post->meta_description) }}</textarea>
        </div>

        {{-- CAPA --}}
        <div class="space-y-2">
          <label class="text-sm font-semibold">Capa do post</label>

          @if(!empty($post->cover_image_url))
            <div class="rounded-xl border border-slate-200 p-2">
              <img src="{{ $post->cover_image_url }}"
                   alt="{{ old('title', $post->title) }}"
                   class="w-full max-w-md rounded-lg"
                   loading="lazy" decoding="async" />
            </div>
          @endif

          <div>
            <label class="text-xs text-slate-600">Upload (JPG/PNG/WEBP até 4MB)</label>
            <input type="file" name="cover_image_file" accept="image/*"
                   class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" />
            @error('cover_image_file') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="text-xs text-slate-600">ou URL (externa)</label>
            <input name="cover_image_url" value="{{ old('cover_image_url', $post->cover_image_url) }}"
                   class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" />
            @error('cover_image_url') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
          </div>

          @if(!empty($post->cover_image_url))
            <label class="flex items-center gap-2 text-sm pt-1">
              <input type="checkbox" name="remove_cover" value="1" class="rounded" />
              Remover capa atual
            </label>
          @endif
        </div>

        <div>
          <label class="text-sm font-semibold">Conteúdo</label>
          <textarea name="content" rows="14"
            class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">{{ old('content', $post->content) }}</textarea>
        </div>
      </div>
    </div>

    <div class="space-y-4">
      <div class="rounded-2xl border border-slate-200 bg-white p-4 space-y-3">
        <div>
          <label class="text-sm font-semibold">Idioma</label>
          <select name="lang" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <option value="pt" @selected(old('lang', $post->lang) === 'pt')>PT</option>
            <option value="en" @selected(old('lang', $post->lang) === 'en')>EN</option>
          </select>
        </div>

        <div>
          <label class="text-sm font-semibold">Categoria</label>
          <select name="category_id" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <option value="">—</option>
            @foreach($categories as $c)
              <option value="{{ $c->id }}" @selected((string)old('category_id', $post->category_id) === (string)$c->id)>
                {{ $c->name }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1" class="rounded"
              @checked((bool) old('is_active', $post->is_active)) />
            Ativo
          </label>

          <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_featured" value="1" class="rounded"
              @checked((bool) old('is_featured', $post->is_featured)) />
            Destaque
          </label>
        </div>

        <div>
          <label class="text-sm font-semibold">Publish date</label>
          <input type="datetime-local" name="publish_date"
            value="{{ old('publish_date', $post->publish_date ? $post->publish_date->format('Y-m-d\TH:i') : '') }}"
            class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" />
        </div>

        <div>
          <label class="text-sm font-semibold">Expiry date (opcional)</label>
          <input type="datetime-local" name="expiry_date"
            value="{{ old('expiry_date', $post->expiry_date ? $post->expiry_date->format('Y-m-d\TH:i') : '') }}"
            class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" />
        </div>

        <div class="flex gap-2 pt-2">
          <button class="flex-1 rounded-xl bg-slate-900 px-3 py-2 text-sm font-semibold text-white">Salvar</button>
          <a href="{{ route('admin.posts.index', ['lang' => old('lang', $post->lang ?? 'pt')]) }}"
             class="flex-1 rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-center hover:bg-slate-50">
            Voltar
          </a>
        </div>
      </div>
    </div>
  </form>
@endsection