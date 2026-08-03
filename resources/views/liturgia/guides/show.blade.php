@extends('layouts.site')
@section('html_lang', 'pt-BR')
@section('title', $guide['title'].' | IA Tio Ben')
@section('meta_description', $guide['description'])
@section('canonical', url('/guias/'.$slug))
@section('robots', 'index,follow')
@section('content')
<article class="mx-auto max-w-4xl px-4 py-8 sm:py-12">
  <nav aria-label="Breadcrumb" class="text-sm text-slate-600"><a href="{{ url('/liturgia-diaria') }}" class="hover:underline">Liturgia Diária</a> / <span>{{ $guide['heading'] }}</span></nav>
  <header class="mt-6 rounded-2xl border border-amber-100 bg-amber-50/70 p-5 sm:p-8"><p class="text-xs font-bold uppercase tracking-wide text-amber-700">Guia de oração e formação</p><h1 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">{{ $guide['heading'] }}</h1><p class="mt-4 max-w-3xl text-lg leading-8 text-slate-700">{{ $guide['intro'] }}</p><a href="{{ url('/liturgia-diaria') }}" class="mt-5 inline-flex rounded-xl bg-amber-800 px-4 py-3 text-sm font-bold text-white hover:bg-amber-900">Ler a Liturgia de hoje</a></header>
  <section class="mt-8 space-y-6 leading-8 text-slate-700"><h2 class="text-2xl font-extrabold text-slate-900">Um caminho simples para começar</h2><p>Comece pela leitura do dia, permaneça alguns instantes em silêncio e escolha uma frase para guardar. A constância ajuda a Palavra a iluminar a vida cotidiana.</p><p>Na Liturgia de hoje você encontra as leituras da Missa, o Salmo Responsorial e o Evangelho. Cada parte tem seu lugar e, juntas, formam uma escuta completa da Palavra de Deus.</p></section>
  <section class="mt-8 grid gap-3 sm:grid-cols-2"><a href="{{ url('/liturgia-diaria/ano/'.$year) }}" class="rounded-xl border border-slate-200 p-4 hover:border-amber-300"><strong class="block text-slate-900">Calendário litúrgico {{ $year }}</strong><span class="mt-1 block text-sm text-slate-600">Encontre qualquer data e as leituras do ano.</span></a><a href="{{ url('/blog/como-usar-a-liturgia') }}" class="rounded-xl border border-slate-200 p-4 hover:border-amber-300"><strong class="block text-slate-900">Como usar a Liturgia no dia a dia</strong><span class="mt-1 block text-sm text-slate-600">Crie uma rotina de oração.</span></a><a href="{{ url('/blog/leituras-da-missa') }}" class="rounded-xl border border-slate-200 p-4 hover:border-amber-300"><strong class="block text-slate-900">Guia das leituras da Missa</strong><span class="mt-1 block text-sm text-slate-600">Leitura, Salmo e Evangelho.</span></a><a href="{{ url('/santo-terco/como-rezar-o-terco') }}" class="rounded-xl border border-slate-200 p-4 hover:border-amber-300"><strong class="block text-slate-900">Rezar o Santo Terço</strong><span class="mt-1 block text-sm text-slate-600">Continue sua oração com os mistérios.</span></a></section>
</article>
@endsection
