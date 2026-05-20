@php
  $lang = $lang ?? 'pt';
  $isEn = $lang === 'en';

  $labels = [
    'title' => $isEn ? 'Ask IA Tio Ben' : 'Pergunte ao IA Tio Ben',
    'subtitle' => $isEn
      ? 'Ask about the Catholic faith, the Gospel, prayer and daily life.'
      : 'Tire dúvidas sobre fé católica, Evangelho, oração e vida espiritual.',
    'placeholder_first' => $isEn ? 'Type your first question...' : 'Digite sua primeira pergunta...',
    'btn_first' => $isEn ? 'Ask' : 'Perguntar',
    'placeholder_dock' => $isEn ? 'Type your message...' : 'Digite sua pergunta ao Tio Ben...',
    'btn_dock' => $isEn ? 'Send' : 'Enviar',
  ];
@endphp

<section class="mx-auto w-full max-w-4xl px-4">

  <div id="questionBox" class="mt-6 rounded-2xl border border-amber-200 bg-white/80 shadow-sm p-4 sm:p-6">
    <div class="flex items-start gap-3">
      <img src="/images/logo-amp.webp" alt="IA Tio Ben" class="h-12 w-12 rounded-2xl bg-white border border-amber-200 p-1" />
      <div class="min-w-0">
        <p class="text-base sm:text-lg font-extrabold text-amber-900">{{ $labels['title'] }}</p>
        <p class="mt-1 text-sm text-gray-700">{{ $labels['subtitle'] }}</p>
      </div>
    </div>

    <div class="mt-4 flex flex-col sm:flex-row gap-2">
      <textarea
        id="questionInput"
        rows="2"
        class="flex-1 resize-none rounded-xl border p-3 text-base text-gray-900 focus:outline-none focus:ring-2 focus:ring-amber-500"
        placeholder="{{ $labels['placeholder_first'] }}"></textarea>

      <button
        id="askBtn"
        class="rounded-xl bg-amber-700 px-5 py-3 text-sm font-semibold text-white disabled:opacity-40"
        disabled>
        {{ $labels['btn_first'] }}
      </button>
    </div>
  </div>

  {{-- ✅ Área onde as bolhas do chat aparecem --}}
  <section
    id="chat-root"
    data-api="/api/perguntar"
    data-lang="{{ $lang }}"
    data-avatar="/images/logo-amp.webp"
    class="mt-6 space-y-3 pb-6"
  ></section>
</section>

{{-- ✅ Dock fixo: começa escondido (aparece após a 1ª pergunta) --}}
<div
  id="chat-dock"
  class="fixed bottom-0 left-0 right-0 z-[9999] bg-white border-t shadow-xl pb-[env(safe-area-inset-bottom)] hidden">
  <div class="mx-auto max-w-4xl flex items-center gap-3 px-4 py-3">
    <img src="/images/logo-amp.webp" alt="IA Tio Ben" class="hidden sm:block h-10 w-10 rounded-xl border border-amber-200 bg-white p-1" />

    <textarea
      id="chat-input"
      class="flex-1 resize-none p-3 border rounded-xl text-base focus:outline-none focus:ring-2 focus:ring-amber-500 text-gray-900"
      rows="1"
      placeholder="{{ $labels['placeholder_dock'] }}"></textarea>

    <button
      id="chat-send"
      class="bg-amber-700 text-white px-5 py-2 rounded-xl font-semibold disabled:opacity-40"
      disabled>
      {{ $labels['btn_dock'] }}
    </button>
  </div>
</div>
