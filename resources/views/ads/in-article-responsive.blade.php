{{-- resources/views/ads/in-article-responsive.blade.php --}}
@php
  $slot  = trim((string)($slot ?? ''));
  $label = $label ?? null;
  $class = $class ?? '';
  $client = $client ?? 'ca-pub-8819996017476509';

  $client = trim((string)$client);
  $label = is_string($label) ? trim($label) : $label;

  $insId = 'ads-generic-fluid-'.substr(sha1($client.'|'.$slot), 0, 10);
@endphp

@if($slot !== '')
  @if(app()->environment('production'))
    @include('ads._init')
  @endif

  @once
    @push('head')
      <style>
        .ads-reserve-fluid {
          position: relative;
          width: 100%;
          contain: layout paint style;
        }
        @media (max-width: 639px) { .ads-reserve-fluid { min-height: 260px; } }
        @media (min-width: 640px) { .ads-reserve-fluid { min-height: 220px; } }
      </style>
    @endpush
  @endonce

  <div class="{{ $class }}">
    @if(!empty($label))
      <div class="text-[11px] font-semibold text-slate-500 mb-2">{{ $label }}</div>
    @endif

    <div class="ads-reserve-fluid">
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
@endif