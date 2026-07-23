@extends('layouts.site')

@section('html_lang', 'pt-BR')
@section('title', 'Liturgia Diária')
@section('meta_description', 'Liturgia diária com leituras, salmo e evangelho.')
@section('canonical', url('/liturgia-diaria'))
@section('robots', 'index,follow')

@section('og_title', 'Liturgia Diária')
@section('og_description', 'Liturgia diária com leituras, salmo e evangelho.')
@section('og_url', url('/liturgia-diaria'))
@section('og_image', url('/og/liturgia.png'))

@push('head')
  <link rel="preconnect" href="https://pagead2.googlesyndication.com" crossorigin>
  <link rel="preconnect" href="https://googleads.g.doubleclick.net" crossorigin>
  <link rel="preconnect" href="https://tpc.googlesyndication.com" crossorigin>

  <style>
    .ads-slot { width: 100%; }
    .ads-slot-content { min-height: 120px; }
    .ads-slot-sidebar-top { min-height: 250px; }
    .ads-slot-sidebar-bottom { min-height: 600px; }
  </style>
@endpush

@section('content')
@php
  $adClient = 'ca-pub-8819996017476509';

  $slotTop = $ads['slot_lit_hub_pt_top_responsive']
    ?? ($ads['slot_content_responsive'] ?? '8534838745');

  $slotSidebarTop = $ads['slot_lit_hub_pt_sidebar_300x250']
    ?? ($ads['slot_sidebar_300x250'] ?? '8534838745');

  $slotSidebarBottom = $ads['slot_lit_hub_pt_sidebar_300x600']
    ?? ($ads['slot_sidebar_300x600'] ?? '9515073457');

  $today = \Carbon\Carbon::today('America/Sao_Paulo');
  $todaySlug = $today->format('d-m-Y');
  $year = (int) $today->format('Y');
@endphp

<div class="grid gap-6 lg:grid-cols-3">
  <section class="lg:col-span-2">
    <h1 class="text-2xl font-bold">Liturgia Diária</h1>
    <p class="mt-2 text-gray-600">Acesse a liturgia por dia, mês e ano.</p>

    <div class="mt-6 flex flex-wrap gap-3">
      <a class="rounded-lg border px-4 py-2 text-sm hover:bg-gray-50"
         href="{{ url('/liturgia-diaria/'.$todaySlug) }}">
        Ir para hoje
      </a>

      <a class="rounded-lg border px-4 py-2 text-sm hover:bg-gray-50"
         href="{{ url('/liturgia-diaria/ano/'.$year) }}">
        Ver calendário de {{ $year }}
      </a>
    </div>

    {{-- ANÚNCIO REAL - CONTEÚDO --}}
    <section class="mt-6">
      <div class="ads-slot ads-slot-content rounded-xl border bg-white p-4">
        <p class="text-xs font-semibold tracking-wide text-amber-700 uppercase">Anúncio</p>

        <div class="mt-3 flex justify-center">
          <ins class="adsbygoogle"
               style="display:block; width:100%;"
               data-ad-client="{{ $adClient }}"
               data-ad-slot="{{ $slotTop }}"
               data-ad-format="auto"
               data-full-width-responsive="true"></ins>
        </div>
      </div>
    </section>

    @push('scripts')
      <script>
        try {
          (window.adsbygoogle = window.adsbygoogle || []).push({});
        } catch (e) {}
      </script>
    @endpush
  </section>

  <aside class="space-y-6">
    {{-- ANÚNCIO REAL - TOPO DO ASIDE --}}
    <section class="rounded-xl border bg-white p-4">
      <p class="text-xs font-semibold tracking-wide text-amber-700 uppercase">Anúncio</p>

      <div class="ads-slot ads-slot-sidebar-top mt-3 overflow-hidden rounded-lg bg-white">
        <ins class="adsbygoogle"
             style="display:block; min-width:300px; min-height:250px;"
             data-ad-client="{{ $adClient }}"
             data-ad-slot="{{ $slotSidebarTop }}"
             data-ad-format="rectangle"
             data-full-width-responsive="false"></ins>
      </div>
    </section>

    @push('scripts')
      <script>
        try {
          (window.adsbygoogle = window.adsbygoogle || []).push({});
        } catch (e) {}
      </script>
    @endpush

    <section class="rounded-xl border p-4">
      <div class="text-sm font-semibold">EN</div>
      <p class="mt-2 text-sm text-gray-600">Daily Mass Readings.</p>
      <a class="mt-3 inline-block rounded-lg bg-gray-900 px-4 py-2 text-sm text-white hover:bg-gray-800"
         href="{{ url('/en/daily-mass-readings') }}">
        Open EN hub
      </a>
    </section>

    {{-- ANÚNCIO REAL - FINAL DO ASIDE --}}
    <section class="rounded-xl border bg-white p-4">
      <p class="text-xs font-semibold tracking-wide text-amber-700 uppercase">Anúncio</p>

      <div class="ads-slot ads-slot-sidebar-bottom mt-3 overflow-hidden rounded-lg bg-white">
        <ins class="adsbygoogle"
             style="display:block; min-width:300px; min-height:600px;"
             data-ad-client="{{ $adClient }}"
             data-ad-slot="{{ $slotSidebarBottom }}"
             data-ad-format="auto"
             data-full-width-responsive="false"></ins>
      </div>
    </section>

    @push('scripts')
      <script>
        try {
          (window.adsbygoogle = window.adsbygoogle || []).push({});
        } catch (e) {}
      </script>
    @endpush
  </aside>
</div>
@endsection