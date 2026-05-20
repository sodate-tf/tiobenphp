{{-- resources/views/ads/sidebar-mobile.blade.php --}}
@php
  $slot = trim((string) ($slot ?? ''));
  $class = trim((string) ($class ?? ''));
  $height = max(90, (int) ($height ?? 120));
@endphp

@if($slot !== '')
  <div class="{{ $class }} rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
    <p class="px-1 pb-2 text-[11px] font-semibold text-slate-500">Publicidade</p>
    <ins class="adsbygoogle"
         data-ads-init="1"
         style="display:block; min-height: {{ $height }}px; width:100%;"
         data-ad-client="ca-pub-8819996017476509"
         data-ad-slot="{{ $slot }}"
         data-ad-format="auto"
         data-full-width-responsive="true"></ins>
  </div>
@endif
