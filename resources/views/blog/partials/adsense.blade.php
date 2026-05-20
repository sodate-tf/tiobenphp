{{-- resources/views/blog/partials/adsense.blade.php --}}
@php
  $slot = trim((string) ($slot ?? ''));
  $height = max(90, (int) ($height ?? 140));
  $class = trim((string) ($class ?? ''));
@endphp

@if($slot !== '')
  <div class="{{ $class }}" style="min-height: {{ $height }}px; width:100%;">
    <ins class="adsbygoogle"
         data-ads-init="1"
         style="display:block; min-height: {{ $height }}px; width:100%;"
         data-ad-client="ca-pub-8819996017476509"
         data-ad-slot="{{ $slot }}"
         data-ad-format="auto"
         data-full-width-responsive="true"></ins>
  </div>
@endif
