@php
  $appUrl = 'https://play.google.com/store/apps/details?id=br.com.iatioben.app';
  $appImage = asset('images/liturgia/mobile-beta-testers-tio-ben-android.png');
@endphp

<aside
  data-download-app-banner
  class="hidden fixed inset-x-0 bottom-0 z-[90] border-t border-amber-300 bg-gradient-to-r from-amber-300 via-amber-200 to-yellow-200 shadow-[0_-6px_18px_rgba(120,53,15,0.16)] lg:block"
  aria-label="Banner para baixar o app Android do IA Tio Ben"
>
  <a
    href="{{ $appUrl }}"
    target="_blank"
    rel="noopener noreferrer"
    aria-label="Baixar app do IA Tio Ben no Google Play"
    title="Baixar app para Android"
    class="mx-auto flex h-[30px] w-full max-w-6xl items-center justify-center gap-3 px-4 text-[12px] font-bold text-amber-950"
  >
    <img
      src="{{ $appImage }}"
      alt="Tio Ben com o Android convidando para baixar o aplicativo"
      class="h-6 w-auto shrink-0 object-contain"
      loading="lazy"
    >
    <span class="whitespace-nowrap">Android</span>
    <span class="truncate">Baixe o app mobile do IA Tio Ben</span>
  </a>
</aside>

@once
  @push('scripts')
    <script>
      (function () {
        const banner = document.querySelector('[data-download-app-banner]');
        if (!banner) return;

        const ua = navigator.userAgent || '';
        const isAndroid = /Android/i.test(ua);
        const isDesktop = window.matchMedia('(min-width: 1024px)').matches;
        const shouldShow = isDesktop && !isAndroid;

        if (!shouldShow) {
          banner.style.display = 'none';
          return;
        }

        banner.style.display = 'block';
      })();
    </script>
  @endpush
@endonce
