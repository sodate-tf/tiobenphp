@extends('layouts.site')

@section('html_lang', $meta['html_lang'])
@section('title', $meta['title'])
@section('meta_description', $meta['description'])
@section('canonical', $meta['canonical'])
@section('og_title', $meta['og_title'])
@section('og_description', $meta['og_description'])
@section('og_url', $meta['og_url'])
@section('og_image', $meta['og_image'])
@section('robots', $meta['robots'])

@section('content')
<article class="mx-auto max-w-3xl px-4 py-10 bg-white text-slate-900 min-h-screen">
  <h1 class="text-2xl font-bold">Data inválida</h1>
  <p class="mt-2 text-sm text-slate-600">
    Use o formato <span class="font-semibold">dd-mm-aaaa</span>. Exemplo:
    <span class="font-semibold">/liturgia-diaria/05-01-2026</span>
  </p>
  <div class="mt-4">
    <a href="/liturgia-diaria"
       class="inline-flex rounded-xl bg-amber-500 text-white px-4 py-2 text-sm font-semibold hover:bg-amber-600">
      Voltar para Liturgia Diária
    </a>
  </div>
</article>
@endsection
