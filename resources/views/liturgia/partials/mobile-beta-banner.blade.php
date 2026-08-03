@php
  $appUrl = 'https://play.google.com/store/apps/details?id=br.com.iatioben.app';
  $appImage = asset('images/liturgia/mobile-beta-testers-tio-ben-android.png');
@endphp

<section id="mobile-beta-banner" class="mt-6 overflow-hidden rounded-[28px] border border-amber-200 bg-gradient-to-br from-amber-50 via-white to-lime-50 shadow-sm">
  <div class="grid gap-6 px-4 py-5 sm:px-6 sm:py-6 lg:grid-cols-[1.2fr_0.8fr] lg:items-center">
    <div class="min-w-0">
      <div class="inline-flex items-center rounded-full border border-amber-200 bg-white/80 px-3 py-1 text-[11px] font-extrabold uppercase tracking-[0.2em] text-amber-700">
        App oficial no Android
      </div>

      <h2 class="mt-4 max-w-2xl text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">
        Baixe o app do IA Tio Ben no Google Play
      </h2>

      <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-700 sm:text-[15px]">
        Leve a liturgia diaria com voce e acompanhe o app no Android com notificacoes,
        leitura automatica das leituras e reflexao sobre o Evangelho.
      </p>

      <div class="mt-4 flex flex-wrap gap-2 text-xs font-semibold text-slate-700 sm:text-sm">
        <span class="rounded-full bg-white px-3 py-2 ring-1 ring-amber-100">Baixar pelo Google Play</span>
        <span class="rounded-full bg-white px-3 py-2 ring-1 ring-amber-100">Notificacoes no celular</span>
        <span class="rounded-full bg-white px-3 py-2 ring-1 ring-amber-100">Leitura automatica das leituras</span>
        <span class="rounded-full bg-white px-3 py-2 ring-1 ring-amber-100">Reflexao sobre o Evangelho</span>
      </div>

      <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center">
        <a
          href="{{ $appUrl }}"
          target="_blank"
          rel="noopener noreferrer"
          class="inline-flex items-center justify-center rounded-2xl bg-amber-600 px-5 py-3 text-sm font-extrabold text-white shadow-sm transition hover:bg-amber-700"
        >
          Baixar app para Android
        </a>

        <p class="text-xs leading-6 text-slate-500 sm:max-w-xs">
          Toque para abrir a pagina do app e fazer o download direto pela Play Store.
        </p>
      </div>
    </div>

    <div class="relative mx-auto w-full max-w-[520px]">
      <div class="absolute inset-0 rounded-[32px] bg-gradient-to-br from-amber-200/40 via-transparent to-lime-200/50 blur-2xl"></div>
      <img
        src="{{ $appImage }}"
        alt="Tio Ben ao lado do mascote Android convidando para baixar o aplicativo"
        class="relative z-10 block w-full rounded-[28px] object-cover shadow-lg ring-1 ring-amber-100"
        loading="lazy"
      >
    </div>
  </div>
</section>