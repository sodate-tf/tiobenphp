@php
  $appUrl = 'https://play.google.com/store/apps/details?id=br.com.iatioben.app';
  $appImage = asset('images/liturgia/mobile-beta-testers-tio-ben-android.png');
@endphp

<section data-download-app-modal class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="download-app-modal-title" aria-describedby="download-app-modal-description">
  <div class="relative w-full max-w-md overflow-hidden rounded-[28px] bg-white shadow-2xl ring-1 ring-black/5">
    <button type="button" data-download-app-modal-close class="absolute right-3 top-3 z-10 inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/90 text-xl text-slate-500 shadow-sm transition hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500" aria-label="Fechar convite para baixar o aplicativo">&times;</button>

    <div class="bg-gradient-to-br from-amber-50 via-white to-lime-50 px-6 pb-3 pt-8 text-center">
      <img src="{{ $appImage }}" alt="Tio Ben ao lado do mascote Android" class="mx-auto h-40 w-auto max-w-full object-contain sm:h-44">
    </div>

    <div class="px-6 pb-6 pt-4 text-center">
      <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-[11px] font-extrabold uppercase tracking-[0.16em] text-amber-800">App para Android</span>
      <h2 id="download-app-modal-title" class="mt-3 text-2xl font-extrabold tracking-tight text-slate-900">Tenha o Tio Ben sempre no seu celular</h2>
      <p id="download-app-modal-description" class="mt-3 text-sm leading-6 text-slate-600">Acompanhe a liturgia de forma mais fácil e otimizada, com novos recursos no aplicativo.</p>
      <a href="{{ $appUrl }}" target="_blank" rel="noopener noreferrer" class="mt-5 inline-flex w-full items-center justify-center rounded-2xl bg-amber-600 px-5 py-3 text-sm font-extrabold text-white shadow-sm transition hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2">Baixar app para Android</a>
      <button type="button" data-download-app-modal-close class="mt-3 text-sm font-semibold text-slate-500 transition hover:text-slate-800">Agora não</button>
    </div>
  </div>
</section>

@once
  @push('scripts')
    <script>
      (function () {
        const modal = document.querySelector('[data-download-app-modal]');
        if (!modal) return;

        const storageKey = 'ia-tio-ben-download-modal-views';
        const ua = navigator.userAgent || '';
        const isAndroid = /Android/i.test(ua);
        const isMobile = /Mobi|Android|iPhone|iPad|iPod/i.test(ua);

        if (isMobile && !isAndroid) return;

        let views = 0;
        try {
          views = Number.parseInt(localStorage.getItem(storageKey) || '0', 10) || 0;
        } catch (error) {
          return;
        }

        if (views >= 3) return;

        const closeModal = function () {
          modal.classList.add('hidden');
          modal.classList.remove('flex');
          document.body.classList.remove('overflow-hidden');
        };

        const openModal = function () {
          modal.classList.remove('hidden');
          modal.classList.add('flex');
          document.body.classList.add('overflow-hidden');
          modal.querySelector('[data-download-app-modal-close]')?.focus();
        };

        try {
          localStorage.setItem(storageKey, String(views + 1));
        } catch (error) {
          return;
        }

        openModal();
        modal.querySelectorAll('[data-download-app-modal-close]').forEach(function (button) {
          button.addEventListener('click', closeModal);
        });
        modal.addEventListener('click', function (event) {
          if (event.target === modal) closeModal();
        });
        document.addEventListener('keydown', function (event) {
          if (event.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
        });
      })();
    </script>
  @endpush
@endonce
