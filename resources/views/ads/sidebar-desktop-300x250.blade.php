{{-- resources/views/ads/sidebar-desktop-300x250.blade.php --}}
@props([
  'slot' => null,
  'class' => '',
])

@if(!empty($slot))
  <div class="{{ $class }}">
    <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
      <p class="px-1 pb-2 text-[11px] font-semibold text-slate-500">Publicidade</p>

      <ins class="adsbygoogle"
           style="display:block; min-width:300px; min-height:250px;"
           data-ad-client="ca-pub-8819996017476509"
           data-ad-slot="{{ $slot }}"
           data-ad-format="rectangle"></ins>
    </div>
  </div>

  @push('scripts')
    <script>
      try {
        (window.adsbygoogle = window.adsbygoogle || []).push({});
      } catch (e) {}
    </script>
  @endpush
@endif