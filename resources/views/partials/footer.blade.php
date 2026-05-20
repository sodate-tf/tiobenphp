{{-- resources/views/partials/footer.blade.php --}}
@php
  $tz = 'America/Sao_Paulo';

  $today     = \Carbon\Carbon::now($tz)->startOfDay();
  $yesterday = $today->copy()->subDay();
  $tomorrow  = $today->copy()->addDay();

  $yesterdaySlug = $yesterday->format('d-m-Y');
  $tomorrowSlug  = $tomorrow->format('d-m-Y');



  $isEn = request()->is('en') || request()->is('en/*');
  $prefix = $isEn ? '/en' : '';

  $homeUrl    = $isEn ? '/en' : '/';
  $blogUrl    = $prefix.'/blog';
  $rosaryUrl  = $isEn ? '/en/rosary' : '/santo-terco';
  $liturgyHub = $isEn ? '/en/daily-mass-readings' : '/liturgia-diaria';

  $liturgyYesterday = $isEn
      ? "/en/daily-mass-readings/{$yesterdaySlug}"
      : "/liturgia-diaria/{$yesterdaySlug}";

  $liturgyTomorrow = $isEn
      ? "/en/daily-mass-readings/{$tomorrowSlug}"
      : "/liturgia-diaria/{$tomorrowSlug}";

  $termUrl = $isEn
      ? '/en/terms-of-responsibility'
      : '/termo-de-responsabilidade';

  $brandLogo = '/images/tio-ben-transparente.webp';
  $yearNow   = (int) $today->format('Y');


  $todaySlug = $today->format('d-m-Y');

  $liturgyToday = $isEn
    ? "/en/daily-mass-readings/{$todaySlug}"
    : "/liturgia-diaria/{$todaySlug}";

@endphp

<footer class="mt-12 w-full border-t border-amber-200 bg-amber-50/80">
  <div class="mx-auto w-full max-w-6xl px-4 py-12">

    <div class="grid grid-cols-1 gap-10 md:grid-cols-12">

      <section class="md:col-span-5">
        <a href="{{ $homeUrl }}" class="flex items-center gap-3">
          <div class="h-12 w-12 rounded-2xl border border-amber-200 bg-white p-1 shadow-sm">
            <img src="{{ $brandLogo }}"
                 alt="IA Tio Ben"
                 class="h-full w-full object-contain"
                 width="48"
                 height="48"
                 decoding="async"
                 loading="lazy">
          </div>
          <div>
            <p class="text-lg font-extrabold text-amber-900">IA Tio Ben</p>
            <p class="text-xs text-gray-600">
              {{ $isEn
                  ? 'Daily Liturgy, Rosary, and Catholic formation'
                  : 'Liturgia diária, Santo Terço e formação católica' }}
            </p>
          </div>
        </a>

        <p class="mt-4 text-sm text-gray-700 leading-relaxed">
          {{ $isEn
              ? 'Catholic content organized to support your daily prayer: Liturgy, Gospel, Rosary, and formation articles.'
              : 'Conteúdo católico organizado para fortalecer sua oração diária: Liturgia, Evangelho do dia, Santo Terço e artigos de formação.' }}
        </p>

        <div class="mt-5 flex flex-wrap gap-2">
          <a href="{{ $blogUrl }}"
             class="rounded-xl border border-amber-200 bg-white px-3 py-2 text-sm font-semibold text-amber-900 hover:bg-amber-50">
            Blog
          </a>

          <a href="{{ $rosaryUrl }}"
             class="rounded-xl border border-amber-200 bg-white px-3 py-2 text-sm font-semibold text-amber-900 hover:bg-amber-50">
            {{ $isEn ? 'Rosary' : 'Santo Terço' }}
          </a>

          <a href="{{ $liturgyToday }}"
             class="rounded-xl border border-amber-200 bg-white px-3 py-2 text-sm font-semibold text-amber-900 hover:bg-amber-50">
            {{ $isEn ? 'Daily Liturgy' : 'Liturgia Diária' }}
          </a>
        </div>
      </section>

      <nav class="md:col-span-4">
        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide">
          {{ $isEn ? 'Daily Liturgy' : 'Liturgia Diária' }}
        </h3>

        <ul class="mt-4 space-y-3 text-sm">
          <li>
            <a href="{{ $liturgyYesterday }}"
               class="flex justify-between rounded-xl border border-amber-200 bg-white px-3 py-2 hover:bg-amber-50">
              <span class="font-semibold text-amber-900">
                {{ $isEn ? 'Yesterday' : 'Ontem' }}
              </span>
            </a>
          </li>

          <li>
            <a href="{{ $liturgyHub }}"
               class="flex justify-between rounded-xl border border-amber-300 bg-amber-100/70 px-3 py-2 hover:bg-amber-100">
              <span class="font-bold text-amber-900">
                {{ $isEn ? 'Today' : 'Hoje' }}
              </span>
            </a>
          </li>

          <li>
            <a href="{{ $liturgyTomorrow }}"
               class="flex justify-between rounded-xl border border-amber-200 bg-white px-3 py-2 hover:bg-amber-50">
              <span class="font-semibold text-amber-900">
                {{ $isEn ? 'Tomorrow' : 'Amanhã' }}
              </span>
            </a>
          </li>
        </ul>

        <div class="mt-4">
          <a href="{{ $liturgyHub }}"
             class="text-sm font-semibold text-blue-800 hover:underline">
            {{ $isEn ? 'View Daily Liturgy hub' : 'Ver página principal da Liturgia' }}
          </a>
        </div>
      </nav>

      <nav class="md:col-span-3">
        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide">
          {{ $isEn ? 'Content' : 'Conteúdo' }}
        </h3>

        <ul class="mt-4 space-y-2 text-sm">
          <li>
            <a href="{{ $blogUrl }}" class="hover:text-amber-900 hover:underline">
              {{ $isEn ? 'Articles & Formation' : 'Artigos e Formação' }}
            </a>
          </li>
          <li>
            <a href="{{ $rosaryUrl }}" class="hover:text-amber-900 hover:underline">
              {{ $isEn ? 'Rosary (guided prayer)' : 'Santo Terço (oração guiada)' }}
            </a>
          </li>
          <li>
            <a href="{{ $homeUrl }}" class="hover:text-amber-900 hover:underline">
              {{ $isEn ? 'Home' : 'Página Inicial' }}
            </a>
          </li>
          <li>
            <a href="{{ $termUrl }}" class="hover:text-amber-900 hover:underline">
              {{ $isEn ? 'Terms of Responsibility' : 'Termo de Responsabilidade' }}
            </a>
          </li>
        </ul>
      </nav>

    </div>

    <div class="mt-12 border-t border-amber-200 pt-6 text-sm text-gray-700 flex flex-col gap-2 sm:flex-row sm:justify-between">
      <p>
        © {{ $yearNow }} <span class="font-semibold text-amber-900">IA Tio Ben</span>.
        {{ $isEn ? 'All rights reserved.' : 'Todos os direitos reservados.' }}
      </p>

      <p>
        {{ $isEn ? 'Built by' : 'Desenvolvido por' }}
        <a href="https://4udevelops.com.br"
           target="_blank"
           rel="noopener noreferrer"
           class="font-semibold text-blue-800 hover:underline">
          4U Develops
        </a>
      </p>
    </div>

  </div>
  <script type="text/javascript">
    (function(c,l,a,r,i,t,y){
        c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
        t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
        y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
    })(window, document, "clarity", "script", "wjv8hqp693");
</script>
</footer>
