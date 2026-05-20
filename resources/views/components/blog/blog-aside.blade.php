@php
  use Carbon\Carbon;

  // Props (com defaults)
  /** @var string|null $currentSlug */
  /** @var string|null $adsSlotDesktop300x250 */
  /** @var string|null $class */
  /** @var 'desktop'|'mobile' $variant */
  /** @var array|null $blogLinks */
  /** @var array|null $latestPosts */
  /** @var bool $hideLatestPosts */

  $variant = $variant ?? 'desktop';
  $hideLatestPosts = (bool) ($hideLatestPosts ?? false);

  // Data atual (dd-mm-aaaa) para abrir Liturgia direto no dia
  $todaySlug = Carbon::now('America/Sao_Paulo')->format('d-m-Y');

  $defaultBlogLinks = [
    [
      'href' => '/blog/como-usar-a-liturgia',
      'title' => 'Como usar a Liturgia no dia a dia',
      'desc' => 'Um jeito simples de rezar e se preparar para a Missa.',
    ],
    [
      'href' => '/blog/ano-liturgico',
      'title' => 'Ano litúrgico: tempos, cores e calendário',
      'desc' => 'Entenda o que muda ao longo do ano e como acompanhar.',
    ],
    [
      'href' => '/blog/leituras-da-missa',
      'title' => 'Guia das leituras da Missa',
      'desc' => 'Primeira leitura, salmo, evangelho e como acompanhar.',
    ],
    [
      'href' => '/blog/como-rezar-com-a-liturgia-em-5-minutos',
      'title' => 'Como rezar com a Liturgia em 5 minutos',
      'desc' => 'Um passo a passo prático para criar rotina e constância.',
    ],
    [
      'href' => '/blog/liturgia-diaria-ou-evangelho-do-dia',
      'title' => 'Liturgia diária x Evangelho do dia: qual a diferença?',
      'desc' => 'Entenda o que cada um inclui e quando usar.',
    ],
  ];

  $effectiveBlogLinks = array_slice(
    (!empty($blogLinks) ? $blogLinks : $defaultBlogLinks),
    0,
    5
  );

  // latestPosts já vem pronto do controller (para evitar query no Blade)
  $fetched = $hideLatestPosts ? [] : ($latestPosts ?? []);

  // Filtra: precisa ter href/title e não mostrar o post atual
  $latest = array_values(array_filter($fetched, function ($p) use ($currentSlug) {
    if (empty($p['href']) || empty($p['title'])) return false;
    if (!$currentSlug) return true;
    return !str_ends_with($p['href'], "/blog/{$currentSlug}");
  }));

  $latest = array_slice($latest, 0, 5);

  $rootClass = trim("min-w-0 " . ($class ?? ''));
@endphp

<aside class="{{ $rootClass }}">
  <div class="sticky top-4 space-y-4">
    {{-- Anúncio (somente desktop) --}}
    @if($variant === 'desktop' && !empty($adsSlotDesktop300x250))
      @include('components.ads.adsense-sidebar-desktop-300x250', ['slot' => $adsSlotDesktop300x250])
    @endif

    {{-- Acesso rápido --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
      <p class="text-xs font-semibold tracking-wide text-amber-700 uppercase">Acesso rápido</p>

      <div class="mt-3 space-y-2">
        <a
          href="{{ url("/liturgia-diaria/{$todaySlug}") }}"
          class="block rounded-xl border border-slate-200 p-3 hover:bg-slate-50 transition"
        >
          <div class="flex items-start gap-2">
            <div class="mt-[2px] text-slate-700">
              {{-- ícone simples (sem lucide): --}}
              <span aria-hidden="true">📖</span>
            </div>
            <div class="min-w-0">
              <p class="text-sm font-bold text-slate-900">Ler a Liturgia de Hoje</p>
              <p class="mt-1 text-xs text-slate-600">
                Leituras, salmo e evangelho • {{ str_replace('-', '/', $todaySlug) }}
              </p>
            </div>
          </div>
        </a>

        <a
          href="{{ url('/santo-terco/como-rezar-o-terco') }}"
          class="block rounded-xl border border-slate-200 p-3 hover:bg-slate-50 transition"
        >
          <div class="flex items-start gap-2">
            <div class="mt-[2px] text-slate-700"><span aria-hidden="true">⭕</span></div>
            <div class="min-w-0">
              <p class="text-sm font-bold text-slate-900">Rezar o Terço</p>
              <p class="mt-1 text-xs text-slate-600">Aprenda, medite os mistérios e crie constância.</p>
            </div>
          </div>
        </a>

        <a
          href="{{ url('/ia') }}"
          class="block rounded-xl border border-amber-200 bg-amber-50 p-3 hover:bg-amber-100 transition"
        >
          <div class="flex items-start gap-3">
            <div class="h-10 w-10 rounded-xl bg-white flex items-center justify-center shadow-sm border border-amber-100">
              <img
                src="{{ asset('images/tio-ben-transparente.webp') }}"
                alt="Tio Ben"
                width="56"
                height="56"
                class="h-7 w-7 object-contain"
                loading="{{ $variant === 'desktop' ? 'eager' : 'lazy' }}"
              />
            </div>

            <div class="min-w-0">
              <p class="text-sm font-extrabold text-amber-900">IA do Tio Ben</p>
              <p class="mt-1 text-xs text-amber-800">Pergunte, reflita o Evangelho e reze com ajuda guiada.</p>

              <div class="mt-2 inline-flex items-center gap-1 text-xs font-bold text-amber-900">
                <span aria-hidden="true">🤖</span>
                Conversar agora
              </div>
            </div>
          </div>
        </a>
      </div>
    </div>

    {{-- Explorar --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
      <p class="text-xs font-semibold tracking-wide text-amber-700 uppercase">Explorar</p>

      <div class="mt-3 space-y-2">
        @foreach($effectiveBlogLinks as $p)
          <a
            href="{{ url($p['href']) }}"
            class="block rounded-xl border border-slate-200 p-3 hover:bg-slate-50 transition"
          >
            <p class="text-sm font-bold text-slate-900">{{ $p['title'] }}</p>
            @if(!empty($p['desc']))
              <p class="mt-1 text-xs text-slate-600">{{ $p['desc'] }}</p>
            @endif
          </a>
        @endforeach
      </div>
    </div>

    {{-- Últimos posts (desktop only) --}}
    @if($variant === 'desktop' && !$hideLatestPosts)
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <p class="text-xs font-semibold tracking-wide text-amber-700 uppercase">Últimos posts</p>

        <div class="mt-3 space-y-2">
          @if(count($latest))
            @foreach($latest as $p)
              <a
                href="{{ url($p['href']) }}"
                class="block rounded-xl border border-slate-200 p-3 hover:bg-slate-50 transition"
              >
                <p class="text-sm font-bold text-slate-900">{{ $p['title'] }}</p>
                @if(!empty($p['desc']))
                  <p class="mt-1 text-xs text-slate-600">{{ $p['desc'] }}</p>
                @endif
              </a>
            @endforeach
          @else
            <div class="rounded-xl border border-slate-200 p-3">
              <p class="text-sm font-semibold text-slate-900">Sem posts recentes</p>
              <p class="mt-1 text-xs text-slate-600">Volte em breve para novos conteúdos.</p>
            </div>
          @endif

          <a
            href="{{ url('/blog') }}"
            class="block rounded-xl border border-slate-200 p-3 hover:bg-slate-50 transition"
          >
            <p class="text-sm font-bold text-slate-900">Ver todos os artigos</p>
            <p class="mt-1 text-xs text-slate-600">Mais conteúdos do Tio Ben para fortalecer sua rotina.</p>
          </a>
        </div>
      </div>
    @endif
  </div>
</aside>
