@props([
  'slot' => null,
  'style' => 'display:block',
  'format' => 'auto',
  'fullWidth' => true,
])

@php
  $isProd = app()->environment('production');
  $client = 'ca-pub-8819996017476509';
@endphp

@if($isProd && $slot)
  <ins class="adsbygoogle"
       style="{{ $style }}"
       data-ad-client="{{ $client }}"
       data-ad-slot="{{ $slot }}"
       data-ad-format="{{ $format }}"
       @if($fullWidth) data-full-width-responsive="true" @endif></ins>
  <script>
    (adsbygoogle = window.adsbygoogle || []).push({});
  </script>
@endif
