@extends('layouts.site')

@section('title', $post->title . ' — Blog IA Tio Ben')
@section('meta_description', $post->meta_description ?? '')
@section('canonical', url('/blog/' . $post->slug))

@section('content')
<main class="max-w-3xl mx-auto p-4 bg-white">
  <article>
    <header class="mb-6 border-b pb-4">
      <h1 class="text-3xl font-extrabold">{{ $post->title }}</h1>
      <p class="text-sm text-gray-500 mt-2">
        {{ optional($post->publish_date)->format('d/m/Y') }}
        @if($post->category) • {{ $post->category->name }} @endif
      </p>
    </header>

    {{-- Bloco liturgia associada --}}
    @if($liturgyBlocks->isNotEmpty())
      <section class="mb-6 p-4 border rounded bg-indigo-50">
        <h2 class="font-bold mb-2">Liturgia relacionada</h2>
        @foreach($liturgyBlocks as $b)
          <p class="mb-2">{{ $b->paragraph }}</p>
          <a class="text-indigo-700 underline" href="{{ $b->page_url }}">
            Ver liturgia do dia {{ \Carbon\Carbon::parse($b->liturgy_date)->format('d/m/Y') }}
          </a>
        @endforeach
      </section>
    @endif

    {{-- Conteúdo do post (você já tem HTML pronto no content?) --}}
    <div class="prose max-w-none">
      {!! $post->content !!}
    </div>

    {{-- Links contextuais para outros posts --}}
    @if($crossLinks->isNotEmpty())
      <section class="mt-8 p-4 border rounded bg-gray-50">
        <h2 class="font-bold mb-2">Veja também</h2>
        @foreach($crossLinks as $x)
          <p class="mb-2">{{ $x->paragraph }}</p>
          <a class="text-indigo-700 underline" href="{{ route('blog.show', $x->slug) }}">
            {{ $x->title }}
          </a>
        @endforeach
      </section>
    @endif

    {{-- Posts relacionados --}}
    <section class="mt-10 border-t pt-6">
      <h2 class="text-xl font-bold mb-4">Posts relacionados</h2>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @foreach($relatedPosts as $rp)
          <a class="block border rounded p-3 hover:bg-gray-50" href="{{ route('blog.show', $rp->slug) }}">
            <div class="font-semibold">{{ $rp->title }}</div>
            <div class="text-xs text-gray-500 mt-2">
              {{ optional($rp->publish_date)->format('d/m/Y') }}
            </div>
          </a>
        @endforeach
      </div>
    </section>

    @php
  $financeSlug = 'cristao-catolico-e-financas';
  $isFinance = ($category->slug ?? null) === $financeSlug;
@endphp

  @if($isFinance)
    <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
      <p class="text-sm text-slate-700">
        Este post faz parte do hub <strong>Cristão Católico e Finanças</strong>.
        <a class="font-semibold text-slate-900 underline" href="{{ route('finance.hub') }}">Ver todos os conteúdos</a>.
      </p>
    </div>
  @endif
  </article>
</main>
@endsection
