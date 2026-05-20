{{-- resources/views/ads/sidebar-mobile-320x100.blade.php --}}
@php
  $slot = trim((string) ($slot ?? ''));
  $class = trim((string) ($class ?? ''));
@endphp

@if($slot !== '')
  <div class="{{ $class }} rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
    <p class="px-1 pb-2 text-[11px] font-semibold text-slate-500">Publicidade</p>
    <div class="flex justify-center" style="min-height:100px;">
      <ins class="adsbygoogle"
           data-ads-init="1"
           style="display:inline-block;width:320px;height:100px;max-width:100%;"
           data-ad-client="ca-pub-8819996017476509"
           data-ad-slot="{{ $slot }}"></ins>
    </div>
  </div>
@endif
