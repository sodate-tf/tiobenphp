{{-- resources/views/ads/_init.blade.php --}}
@push('head')
  <style>
    /* ===== Ads reserve (reduz CLS) ===== */
    .ads-reserve{
      position: relative;
      width: 100%;
      contain: layout paint style;
    }
    .ads-reserve[data-min-h]{
      min-height: var(--ads-min-h, 220px);
    }
  </style>
@endpush