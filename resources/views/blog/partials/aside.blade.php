{{-- resources/views/blog/partials/aside.blade.php --}}
@php
  $variant = $variant ?? 'desktop';
  $todaySlug = $todaySlug ?? now()->format('d-m-Y');

  // AdSense
  $adClient = $adClient ?? 'ca-pub-8819996017476509';

  /*
   | Slot real do bloco lateral.
   | Se você criou um bloco específico 300x250 no AdSense,
   | substitua este ID pelo slot correto.
   */
  $adsSlotRect = $adsSlotRect ?? '3041346283';

  $blogLinks = $blogLinks ?? [];
  $latestPosts = $latestPosts ?? [];
  $hideLatestPosts = $hideLatestPosts ?? false;

  $SITE_URL = rtrim(config('app.url') ?: url('/'), '/');

  $forceHttps = function (string $u) {
    return preg_replace('#^http://#i', 'https://', $u);
  };

  $absUrl = function (?string $u) use ($SITE_URL, $forceHttps) {
    $u = trim((string) $u);
    if ($u === '') return '';
    if (preg_match('#^https?://#i', $u)) return $forceHttps($u);
    return $forceHttps($SITE_URL.'/'.ltrim($u, '/'));
  };
@endphp

@once
  @push('head')
    <script async
      src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ $adClient }}"
      crossorigin="anonymous"></script>
  @endpush
@endonce

<aside class="min-w-0 space-y-4">
  <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
    <p class="text-xs font-semibold tracking-wide text-amber-700 uppercase">Acesso rápido</p>

    <div class="mt-3 space-y-2">
      <a href="{{ $absUrl('/liturgia-diaria') }}"
         class="block rounded-xl border border-slate-200 p-3 hover:bg-slate-50 transition">
        <p class="text-sm font-extrabold text-slate-900">Ler a Liturgia de Hoje</p>
        <p class="mt-1 text-xs text-slate-600">Leituras, salmo e evangelho • {{ $todaySlug }}</p>
      </a>

      <a href="{{ $absUrl('/santo-terco') }}"
         class="block rounded-xl border border-slate-200 p-3 hover:bg-slate-50 transition">
        <p class="text-sm font-extrabold text-slate-900">Rezar o Terço</p>
        <p class="mt-1 text-xs text-slate-600">Mistérios, guia e constância.</p>
      </a>

      <a href="{{ $absUrl('/ia') }}"
         class="block rounded-xl border border-amber-200 bg-amber-50 p-3 hover:bg-amber-100 transition">
        <p class="text-sm font-extrabold text-amber-900">IA do Tio Ben</p>
        <p class="mt-1 text-xs text-amber-800">Pergunte, reflita e reze com ajuda guiada.</p>
        <div class="mt-2 text-xs font-bold text-amber-900">Conversar agora →</div>
      </a>
    </div>
  </section>

  {{-- Publicidade real AdSense --}}
  @if(!empty($adsSlotRect))
    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
      <div class="mb-2 flex items-center justify-between">
        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">
          Publicidade
        </p>
      </div>

      <div class="mx-auto flex min-h-[250px] w-full max-w-[336px] items-center justify-center overflow-hidden rounded-xl bg-slate-50">
        <ins class="adsbygoogle"
             style="display:block;width:100%;min-width:250px;max-width:336px;height:250px"
             data-ad-client="{{ $adClient }}"
             data-ad-slot="{{ $adsSlotRect }}"
             data-ad-format="rectangle"
             data-full-width-responsive="true"></ins>
      </div>

      <script>
        window.adsbygoogle = window.adsbygoogle || [];
        try {
          window.adsbygoogle.push({});
        } catch (e) {
          console.warn('AdSense aside failed:', e);
        }
      </script>
    </section>
  @endif

  {{-- Explorar --}}
  <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
    <p class="text-xs font-semibold tracking-wide text-amber-700 uppercase">Explorar</p>

    <div class="mt-3 space-y-2">
      @foreach($blogLinks as $x)
        <a href="{{ $x['href'] }}"
           class="block rounded-xl border border-slate-200 p-3 hover:bg-slate-50 transition">
          <p class="text-sm font-bold text-slate-900">{{ $x['title'] }}</p>
          @if(!empty($x['desc']))
            <p class="mt-1 text-xs text-slate-600">{{ $x['desc'] }}</p>
          @endif
        </a>
      @endforeach
    </div>
  </section>

  {{-- Últimos posts --}}
  @if(!$hideLatestPosts)
    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
      <p class="text-xs font-semibold tracking-wide text-amber-700 uppercase">Últimos posts</p>

      <div class="mt-3 space-y-2">
        @forelse($latestPosts as $p)
          <a href="{{ $p['href'] }}" class="block rounded-xl border border-slate-200 p-3 hover:bg-slate-50 transition">
            <p class="text-sm font-extrabold text-slate-900 leading-snug line-clamp-2">{{ $p['title'] }}</p>
            @if(!empty($p['desc']))
              <p class="mt-1 text-xs text-slate-600 line-clamp-2">{{ $p['desc'] }}</p>
            @endif
          </a>
        @empty
          <div class="rounded-xl border border-slate-200 p-3">
            <p class="text-sm font-semibold text-slate-900">Sem posts recentes</p>
            <p class="mt-1 text-xs text-slate-600">Volte em breve para novos conteúdos.</p>
          </div>
        @endforelse

        <a href="{{ $absUrl('/blog/posts') }}" class="block rounded-xl border border-slate-200 p-3 hover:bg-slate-50 transition">
          <p class="text-sm font-bold text-slate-900">Ver todos os artigos</p>
          <p class="mt-1 text-xs text-slate-600">Mais conteúdos para fortalecer sua rotina.</p>
        </a>
      </div>
    </section>
  @endif
</aside>