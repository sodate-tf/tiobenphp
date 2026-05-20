@php $lang = $lang ?? 'pt'; @endphp

<section class="mt-4 md:mt-6">
  <div class="bg-white/70 border border-amber-200 rounded-3xl p-5 md:p-7 shadow-sm">
    <div class="flex items-center gap-4">
      <img src="/images/tio-ben-transparente.webp" alt="IA Tio Ben" class="h-16 w-16 md:h-20 md:w-20" />

      <div class="flex-1">
        <h1 class="text-xl md:text-2xl font-extrabold text-amber-900 leading-tight">
          @if($lang==='en')
            IA Tio Ben for daily Catholic life
          @else
            IA Tio Ben para a vida católica diária
          @endif
        </h1>

        <p class="mt-1 text-sm md:text-base text-gray-800/90 leading-relaxed">
          @if($lang==='en')
            Ask about the Gospel, Mass readings, prayer, and Catholic teaching—then keep the conversation going like a real chat.
          @else
            Pergunte sobre o Evangelho, a liturgia, oração e ensinamentos católicos — e continue a conversa como um chat de verdade.
          @endif
        </p>
      </div>
    </div>

    <div class="mt-4 flex flex-wrap gap-2">
      <a href="{{ $lang==='en' ? '/en/daily-mass-readings' : '/liturgia-diaria' }}"
         class="inline-flex items-center justify-center rounded-xl bg-amber-800 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-900 transition">
        {{ $lang==='en' ? 'Open Daily Readings' : 'Abrir Liturgia Diária' }}
      </a>

      <a href="{{ $lang==='en' ? '/en/rosary' : '/santo-terco' }}"
         class="inline-flex items-center justify-center rounded-xl border border-amber-900/20 bg-white/80 px-4 py-2 text-sm font-semibold text-amber-900 hover:bg-white transition">
        {{ $lang==='en' ? 'Pray the Rosary' : 'Rezar o Santo Terço' }}
      </a>

      <a href="{{ $lang==='en' ? '/en/blog' : '/blog' }}"
         class="inline-flex items-center justify-center rounded-xl border border-amber-900/20 bg-white/80 px-4 py-2 text-sm font-semibold text-amber-900 hover:bg-white transition">
        {{ $lang==='en' ? 'Read the Blog' : 'Ver o Blog' }}
      </a>
    </div>
  </div>
</section>
