@extends('layouts.site')

@section('html_lang', $meta['html_lang'] ?? 'en')
@section('title', $meta['title'] ?? 'Daily Mass Readings')
@section('meta_description', $meta['description'] ?? 'Daily Mass readings with readings, psalm and Gospel.')
@section('canonical', $meta['canonical'] ?? url('/en/daily-mass-readings'))
@section('robots', $meta['robots'] ?? 'index,follow')

@section('hreflang')
  @if(!empty($meta['hreflangs']) && is_array($meta['hreflangs']))
    @foreach($meta['hreflangs'] as $lang => $url)
      @if(!empty($lang) && !empty($url))
        <link rel="alternate" hreflang="{{ $lang }}" href="{{ $url }}"/>
      @endif
    @endforeach
  @endif
@endsection

@section('og_title', $meta['og_title'] ?? ($meta['title'] ?? 'Daily Mass Readings'))
@section('og_description', $meta['og_description'] ?? ($meta['description'] ?? 'Daily Mass readings with readings, psalm and Gospel.'))
@section('og_url', $meta['canonical'] ?? url('/en/daily-mass-readings'))
@section('og_image', $meta['og_image'] ?? url('/og/liturgia.png'))

@push('head')
  <link rel="preconnect" href="https://pagead2.googlesyndication.com" crossorigin>
  <link rel="preconnect" href="https://googleads.g.doubleclick.net" crossorigin>
  <link rel="preconnect" href="https://tpc.googlesyndication.com" crossorigin>

  {{-- AdSense sem atraso --}}
  <script async
    src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-8819996017476509"
    crossorigin="anonymous"></script>

  <style>
    .ads-slot { width: 100%; }
    .ads-slot-content { min-height: 280px; }
    .ads-slot-sidebar-top { min-height: 250px; }
    .ads-slot-sidebar-bottom { min-height: 600px; }
  </style>
@endpush

@section('content')
@php
  $adClient = 'ca-pub-8819996017476509';

  $year = $year ?? (int) now('America/Sao_Paulo')->format('Y');

  $today = now('America/Sao_Paulo');
  $todaySlug = \App\Support\LiturgiaDate::slugFrom(
    (int) $today->format('d'),
    (int) $today->format('m'),
    (int) $today->format('Y')
  );

  $slotTop = $ads['slot_lit_hub_en_top_responsive']
    ?? ($ads['slot_content_responsive'] ?? '8534838745');

  $slotSidebarTop = $ads['slot_lit_hub_en_sidebar_300x250']
    ?? ($ads['slot_sidebar_300x250'] ?? '8534838745');

  $slotSidebarBottom = $ads['slot_lit_hub_en_sidebar_300x600']
    ?? ($ads['slot_sidebar_300x600'] ?? '9515073457');
@endphp

<div class="grid gap-6 lg:grid-cols-3">
  <section class="lg:col-span-2">
    <h1 class="text-2xl font-bold">Daily Mass Readings</h1>
    <p class="mt-2 text-gray-600">Browse by day, month and year.</p>

    <div class="mt-6 flex flex-wrap gap-3">
      <a class="rounded-lg border px-4 py-2 text-sm hover:bg-gray-50"
         href="{{ url('/en/daily-mass-readings/'.$todaySlug) }}">
        Go to today
      </a>

      <a class="rounded-lg border px-4 py-2 text-sm hover:bg-gray-50"
         href="{{ url('/en/daily-mass-readings/year/'.$year) }}">
        View {{ $year }}
      </a>
    </div>

    {{-- REAL ADSENSE - CONTENT --}}
    <section class="mt-6">
      <div class="ads-slot ads-slot-content rounded-xl border bg-white p-4">
        <p class="text-xs font-semibold tracking-wide text-amber-700 uppercase">Advertisement</p>

        <div class="mt-3 flex justify-center">
          <ins class="adsbygoogle"
               style="display:block; width:100%; min-height:280px;"
               data-ad-client="{{ $adClient }}"
               data-ad-slot="{{ $slotTop }}"
               data-ad-format="auto"
               data-full-width-responsive="true"></ins>
        </div>

        <script>
          try {
            (window.adsbygoogle = window.adsbygoogle || []).push({});
          } catch (e) {}
        </script>
      </div>
    </section>
  </section>

  <aside class="space-y-6">
    {{-- REAL ADSENSE - SIDEBAR TOP --}}
    <section class="rounded-xl border bg-white p-4">
      <p class="text-xs font-semibold tracking-wide text-amber-700 uppercase">Advertisement</p>

      <div class="ads-slot ads-slot-sidebar-top mt-3 overflow-hidden rounded-lg bg-white">
        <ins class="adsbygoogle"
             style="display:block; min-width:300px; min-height:250px;"
             data-ad-client="{{ $adClient }}"
             data-ad-slot="{{ $slotSidebarTop }}"
             data-ad-format="rectangle"
             data-full-width-responsive="false"></ins>
      </div>

      <script>
        try {
          (window.adsbygoogle = window.adsbygoogle || []).push({});
        } catch (e) {}
      </script>
    </section>

    <section class="rounded-xl border p-4">
      <div class="text-sm font-semibold">PT</div>
      <p class="mt-2 text-sm text-gray-600">Liturgia diária.</p>

      <a class="mt-3 inline-block rounded-lg bg-gray-900 px-4 py-2 text-sm text-white hover:bg-gray-800"
         href="{{ url('/liturgia-diaria') }}">
        Open PT hub
      </a>
    </section>

    {{-- REAL ADSENSE - SIDEBAR BOTTOM --}}
    <section class="rounded-xl border bg-white p-4">
      <p class="text-xs font-semibold tracking-wide text-amber-700 uppercase">Advertisement</p>

      <div class="ads-slot ads-slot-sidebar-bottom mt-3 overflow-hidden rounded-lg bg-white">
        <ins class="adsbygoogle"
             style="display:block; min-width:300px; min-height:600px;"
             data-ad-client="{{ $adClient }}"
             data-ad-slot="{{ $slotSidebarBottom }}"
             data-ad-format="auto"
             data-full-width-responsive="false"></ins>
      </div>

      <script>
        try {
          (window.adsbygoogle = window.adsbygoogle || []).push({});
        } catch (e) {}
      </script>
    </section>
  </aside>
</div>
@endsection