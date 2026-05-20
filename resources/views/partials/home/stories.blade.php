@php
  // Espera receber: $dd, $mm, $yyyy, $siteUrl, $lang
  // Espera receber: $storyLiturgia, $storyTerco
@endphp

<section class="bg-gradient-to-b from-amber-200 to-amber-400">
  <div class="mx-auto w-full max-w-5xl px-5 py-8">  

    {{-- HERO --}}
    <div class="text-center">
      <div class="flex justify-center">
        <img src="{{ asset('images/tio-ben-transparente.webp') }}" alt="IA Tio Ben" width="200" height="200" class="h-[200px] w-[200px]" />
      </div>

      @if($lang === 'en')
        <h1 class="mt-6 text-2xl md:text-4xl font-extrabold text-amber-900 tracking-tight">
          Catholic AI for Daily Prayer: Mass Readings, Gospel Reflections, and Practical Answers
        </h1>
        <p class="mt-3 text-gray-800 text-base md:text-lg leading-relaxed">
          Ask a faith question, get a clear explanation grounded in Catholic teaching, and follow along with
          today’s Mass readings. Built for everyday prayer—simple, organized, and usable.
        </p>
      @else
        <h1 class="mt-6 text-2xl md:text-4xl font-extrabold text-amber-900 tracking-tight">
          IA Tio Ben: Liturgia Diária, Evangelho do Dia e Reflexões Católicas
        </h1>
        <p class="mt-3 text-gray-800 text-base md:text-lg leading-relaxed">
          Faça uma pergunta sobre fé, liturgia, Evangelho, oração e vida espiritual. Acompanhe também a Liturgia
          Diária e conteúdos de formação católica.
        </p>
      @endif

      <div class="mt-6 flex flex-wrap items-center justify-center gap-2">
        <a href="/liturgia-diaria"
           class="inline-flex items-center justify-center rounded-xl bg-amber-800 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-900 transition">
          {{ $lang === 'en' ? "Open Today’s Readings" : "Abrir Liturgia Diária" }}
        </a>

        <a href="/santo-terco"
           class="inline-flex items-center justify-center rounded-xl border border-amber-900/20 bg-white/80 px-4 py-2 text-sm font-semibold text-amber-900 hover:bg-white transition">
          {{ $lang === 'en' ? "Pray the Rosary" : "Rezar o Santo Terço" }}
        </a>

        <a href="/blog"
           class="inline-flex items-center justify-center rounded-xl border border-amber-900/20 bg-white/80 px-4 py-2 text-sm font-semibold text-amber-900 hover:bg-white transition">
          {{ $lang === 'en' ? "Read the Blog" : "Ver o Blog" }}
        </a>
      </div>

      @if($lang === 'en')
        <div class="mt-4 text-xs text-amber-950/70">
          Prefer Portuguese? <a class="underline underline-offset-4" href="/">Go to PT-BR</a>
        </div>
      @endif
    </div>
  </div>
</section>