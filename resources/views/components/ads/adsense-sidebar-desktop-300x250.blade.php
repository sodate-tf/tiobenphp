{{-- resources/views/components/ads/adsense-sidebar-desktop-300x250.blade.php --}}
@include('ads.sidebar-desktop-300x250', [
  'slot' => $slot ?? null,
  'class' => $class ?? '',
])
