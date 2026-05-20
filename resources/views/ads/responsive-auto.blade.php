{{-- resources/views/ads/responsive-auto.blade.php --}}
@props([
  'slot' => null,
  'class' => '',
  'label' => null,
  'client' => 'ca-pub-8819996017476509',
  'minHeight' => 120,
])

@php
  $slot   = trim((string)($slot ?? ''));
  $client = trim((string)$client);
  $label  = is_string($label) ? trim($label) : $label;
  $insId  = 'ads-responsive-auto-'.substr(sha1($client.'|'.$slot), 0, 10);
@endphp

@if($slot !== '')
  @if(app()->environment('production'))
    @include('ads._init', ['adsenseClient' => $client])
  @endif

  <div class="{{ $class }}">
    @if(!empty($label))
      <p class="text-[11px] font-semibold tracking-wide text-amber-700 uppercase">{{ $label }}</p>
    @endif

    <div class="mt-3 ads-reserve" data-h="{{ (int)$minHeight }}" style="min-height:{{ (int)$minHeight }}px;">
      <ins
        id="{{ $insId }}"
        class="adsbygoogle"
        style="display:block"
        data-ads-init="1"
        data-ad-client="{{ $client }}"
        data-ad-slot="{{ $slot }}"
        data-ad-format="auto"
        data-full-width-responsive="true"
      ></ins>
    </div>
  </div>
@endif