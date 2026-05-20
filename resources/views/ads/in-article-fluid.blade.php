{{-- resources/views/ads/in-article-fluid.blade.php --}}
@props([
  'slot'  => '5469336488',
  'class' => '',
  'label' => 'Anúncio',
  'client' => 'ca-pub-8819996017476509',
  'minHeightMobile' => 260,
  'minHeightDesktop' => 220,
])

@php
  $slot   = trim((string)$slot);
  $client = trim((string)$client);
  $label  = is_string($label) ? trim($label) : $label;
  $insId  = 'ads-in-article-fluid-'.substr(sha1($client.'|'.$slot), 0, 10);
@endphp

@if($slot !== '')
  {{-- garante que o init global foi incluído (não quebra se já incluiu no layout) --}}
  @if(app()->environment('production'))
    @include('ads._init', ['adsenseClient' => $client])
  @endif

  <div class="{{ $class }}">
    <div class="rounded-2xl border border-slate-200 bg-white/90 shadow-sm p-3 sm:p-4">
      @if(!empty($label))
        <p class="text-[11px] font-semibold tracking-wide text-amber-700 uppercase">{{ $label }}</p>
      @endif

      <div class="mt-2 ads-reserve"
           data-h="{{ (int)$minHeightMobile }}"
           style="min-height: {{ (int)$minHeightMobile }}px;">
        <ins
          id="{{ $insId }}"
          class="adsbygoogle"
          style="display:block; text-align:center;"
          data-ads-init="1"
          data-ad-layout="in-article"
          data-ad-format="fluid"
          data-ad-client="{{ $client }}"
          data-ad-slot="{{ $slot }}"
        ></ins>
      </div>
    </div>
  </div>
@endif