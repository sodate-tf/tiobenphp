{{-- resources/views/terco/page.blade.php --}}
@extends('layouts.site')

@section('html_lang', $meta['html_lang'] ?? 'pt-BR')
@section('title', $meta['title'] ?? 'Santo Terço — IA Tio Ben')
@section('meta_description', $meta['description'] ?? '')
@section('canonical', $meta['canonical'] ?? '')
@section('robots', $meta['robots'] ?? 'index,follow')

@section('hreflang')
  @if(!empty($meta['hreflangs']) && is_array($meta['hreflangs']))
    @foreach($meta['hreflangs'] as $langCode => $url)
      @if(!empty($langCode) && !empty($url))
        <link rel="alternate" hreflang="{{ $langCode }}" href="{{ $url }}"/>
      @endif
    @endforeach
  @endif
@endsection

@section('og_title', data_get($meta, 'og.title', $meta['title'] ?? ''))
@section('og_description', data_get($meta, 'og.description', $meta['description'] ?? ''))
@section('og_url', data_get($meta, 'og.url', $meta['canonical'] ?? ''))
@section('og_image', data_get($meta, 'og.image', ''))

@push('head')
  @php
    // Lang/route (padrão consistente com o resto do site)
    $lang = $lang ?? (request()->is('en') || request()->is('en/*') ? 'en' : 'pt');
    $isEn = $lang === 'en';

    // Canonical
    $canonical = $meta['canonical'] ?? url()->current();

    // Cache bust (tudo com a MESMA versão)
    $assetV = $assetV ?? '1';

    // Contexto do terço (do controller -> $initial)
    $initial = $initial ?? [];
    $setKey = data_get($initial, 'setKey'); // gozosos/dolorosos/gloriosos/luminosos (ou null)

    // Copys básicos (SEO + schema)
    $pageName = $isEn ? 'Daily Rosary' : 'Santo Terço Diário';
    $pageDesc = $meta['description'] ?? (
      $isEn
        ? 'Pray the Rosary step by step on your phone: interactive beads, mysteries of the day, reflections, and a prayer guide.'
        : 'Reze o Santo Terço passo a passo no celular: contas interativas, mistérios do dia, reflexões com referências bíblicas e manual de orações.'
    );

    // Schema WebPage
    $webPageJsonLd = [
      '@context' => 'https://schema.org',
      '@type' => 'WebPage',
      'name' => $pageName,
      'url' => $canonical,
      'description' => $pageDesc,
      'inLanguage' => $isEn ? 'en' : 'pt-BR',
    ];

    // FAQ schema
    $faqJsonLd = [
      '@context' => 'https://schema.org',
      '@type' => 'FAQPage',
      'mainEntity' => [
        [
          '@type' => 'Question',
          'name' => $isEn ? 'Which mysteries should I pray today?' : 'Quais são os mistérios do terço de hoje?',
          'acceptedAnswer' => [
            '@type' => 'Answer',
            'text' => $isEn
              ? 'Traditionally: Monday and Saturday (Joyful), Tuesday and Friday (Sorrowful), Wednesday and Sunday (Glorious), Thursday (Luminous). You can also choose manually on the page.'
              : 'Tradicionalmente: segunda e sábado (Gozosos), terça e sexta (Dolorosos), quarta e domingo (Gloriosos), quinta (Luminosos). Você pode escolher manualmente na página.',
          ],
        ],
        [
          '@type' => 'Question',
          'name' => $isEn ? 'How do I pray the Rosary step by step?' : 'Como rezar o Santo Terço passo a passo?',
          'acceptedAnswer' => [
            '@type' => 'Answer',
            'text' => $isEn
              ? 'Opening: Sign of the Cross, Creed, Our Father, 3 Hail Marys, Glory Be. Each decade: Our Father, 10 Hail Marys, Glory Be and the Fatima Prayer. Closing: Hail Holy Queen and final prayer.'
              : 'Abertura: Sinal da Cruz, Creio, Pai-Nosso, 3 Ave-Marias e Glória ao Pai. Depois, em cada dezena: Pai-Nosso, 10 Ave-Marias, Glória ao Pai e Oração de Fátima. Ao final: Salve Rainha e oração final.',
          ],
        ],
        [
          '@type' => 'Question',
          'name' => $isEn ? 'Is the Fatima Prayer mandatory?' : 'A Oração de Fátima é obrigatória?',
          'acceptedAnswer' => [
            '@type' => 'Answer',
            'text' => $isEn
              ? 'No. It is a traditional prayer commonly added after the Glory Be at the end of each decade. Here it is included in the full flow.'
              : 'Não. É uma oração tradicionalmente acrescentada após o Glória ao Pai no fim de cada dezena. Aqui ela está incluída no fluxo completo.',
          ],
        ],
      ],
    ];
  @endphp

  {{-- Schema --}}
  <script type="application/ld+json">{!! json_encode($webPageJsonLd, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
  <script type="application/ld+json">{!! json_encode($faqJsonLd, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>

  {{-- Contexto do app --}}
  <script>
    window.__ROSARY_INITIAL__ = @json($initial);
  </script>

  {{-- Preload do app (mesma versão) --}}
  <link rel="preload" href="/js/rosary/rosary-app.js?v={{ $assetV }}" as="script">

  <style>
    /* ===== Layout grid (fallback sem Tailwind lg:) ===== */
    .rosary-grid { display: block; }
    .rosary-aside-desktop { display: none; }
    .rosary-aside-mobile { display: block; }

    @media (min-width: 1024px) {
      .rosary-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 340px;
        gap: 24px;
        align-items: start;
      }
      .rosary-aside-desktop { display: block; }
      .rosary-aside-mobile { display: none; }
    }

    /* ===== Fundo suave (igual vibe Liturgia) ===== */
    .rosary-surface {
      background: linear-gradient(180deg, rgba(251,191,36,0.55) 0%, rgba(255,255,255,1) 55%);
      border-radius: 24px;
    }
  </style>
@endpush

@section('content')
  @php
    // Mantém consistência caso view seja chamada sem esses params
    $lang = $lang ?? (request()->is('en') || request()->is('en/*') ? 'en' : 'pt');
    $isEn = $lang === 'en';

    $initial = $initial ?? [];
    $setKey = data_get($initial, 'setKey');
  @endphp

  <div class="pb-24 md:pb-0 mt-10">
    <article class="rosary-surface mx-auto w-full max-w-7xl px-3 sm:px-4 lg:px-6 py-4 sm:py-6 text-slate-900 leading-relaxed">

      {{-- SEO: H1 real (fora do app) --}}
      <header class="mb-4 sm:mb-6">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-amber-950">
          {{ $meta['h1'] ?? ($isEn ? 'Pray the Rosary step by step' : 'Reze o Santo Terço passo a passo') }}
        </h1>
        <p class="mt-2 text-sm sm:text-base text-amber-950/80">
          {{ $meta['lead'] ?? ($isEn
            ? 'Interactive beads, mysteries of the day, and a complete prayer guide.'
            : 'Contas interativas, mistérios do dia e manual completo de orações.'
          ) }}
        </p>
      </header>

      <div class="rosary-grid gap-5 items-start">

        {{-- MAIN --}}
        <section class="min-w-0">
          <div id="rosary-app" class="min-h-[320px]">
            <noscript>
              <div class="rounded-2xl border border-amber-200 bg-white p-5">
                <h2 class="text-lg font-extrabold text-gray-900">
                  {{ $isEn ? 'Rosary' : 'Santo Terço' }}
                </h2>
                <p class="mt-2 text-gray-700">
                  {{ $isEn
                    ? 'To use the interactive Rosary, enable JavaScript in your browser.'
                    : 'Para usar o terço interativo, ative o JavaScript no navegador.'
                  }}
                </p>
              </div>
            </noscript>
          </div>

          {{-- SEO extra (conteúdo estático leve) --}}
          <section class="mt-6 rounded-3xl bg-white/75 shadow-sm backdrop-blur-sm p-4 sm:p-5 border border-white/40">
            <p class="text-[11px] font-bold uppercase tracking-wide text-amber-700">
              {{ $isEn ? 'How it works' : 'Como funciona' }}
            </p>
            <div class="mt-3 text-sm text-slate-700 leading-relaxed space-y-2">
              @if($isEn)
                <p>Use the buttons to advance step by step. The bead timeline helps you keep track of each decade.</p>
                <p>You can switch mysteries and prayer mode at any time. Your progress updates automatically.</p>
              @else
                <p>Use os botões para avançar passo a passo. A linha de contas ajuda a acompanhar cada dezena.</p>
                <p>Você pode trocar o conjunto de mistérios e o modo de oração a qualquer momento. O progresso é atualizado automaticamente.</p>
              @endif
            </div>
          </section>

          {{-- MOBILE: Aside colapsável (depois do app e do texto) --}}
          <div class="rosary-aside-mobile mt-6">
            <div class="rounded-2xl bg-white/90 p-4 shadow-sm ring-1 ring-slate-200/60">
              <div class="flex items-center justify-between">
                <p class="text-xs font-semibold tracking-wide text-amber-700 uppercase">
                  {{ $isEn ? 'Quick access' : 'Acesso rápido' }}
                </p>
                <button type="button" id="rosary-aside-toggle"
                  class="text-xs font-semibold text-slate-600 hover:text-slate-900">
                  {{ $isEn ? 'Show' : 'Mostrar' }}
                </button>
              </div>

              <div id="rosary-aside-mobile-panel" class="mt-3 hidden">
                @include('terco.partials.aside', [
                  'lang' => $lang,
                  'variant' => 'mobile',
                  'setKey' => $setKey,
                  'adsSlotDesktop300x250' => $adsSlotDesktop300x250 ?? 'SEU_SLOT_300X250',
                  'adsSlotMobile' => $adsSlotMobile ?? 'SEU_SLOT_MOBILE',
                  'blogLinks' => $blogLinks ?? null,
                ])
              </div>
            </div>
          </div>
        </section>

        {{-- ASIDE Desktop (fixo à direita) --}}
        <aside class="rosary-aside-desktop min-w-0">
          <div class="sticky top-20">
            @include('terco.partials.aside', [
              'lang' => $lang,
              'variant' => 'desktop',
              'setKey' => $setKey,
              'adsSlotDesktop300x250' => $adsSlotDesktop300x250 ?? 'SEU_SLOT_300X250',
              'adsSlotMobile' => $adsSlotMobile ?? 'SEU_SLOT_MOBILE',
              'blogLinks' => $blogLinks ?? null,
            ])
          </div>
        </aside>

      </div>
    </article>
  </div>

  {{-- Scripts (mesma versão em TODOS) --}}
  @if($isEn)
    <script defer src="/js/rosary/rosary-dataset-en.js?v={{ $assetV }}"></script>
  @else
    <script defer src="/js/rosary/rosary-dataset-pt.js?v={{ $assetV }}"></script>
  @endif

  <script defer src="/js/rosary/rosary-engine.js?v={{ $assetV }}"></script>
  <script defer src="/js/rosary/rosary-app.js?v={{ $assetV }}"></script>
@endsection

@push('scripts')
<script>
(function(){
  const toggle = document.getElementById('rosary-aside-toggle');
  const panel = document.getElementById('rosary-aside-mobile-panel');
  if(!toggle || !panel) return;

  toggle.addEventListener('click', ()=>{
    const open = !panel.classList.contains('hidden');
    panel.classList.toggle('hidden', open);
    toggle.textContent = open ? (document.documentElement.lang === 'en' ? 'Show' : 'Mostrar') : (document.documentElement.lang === 'en' ? 'Hide' : 'Ocultar');
  });
})();
</script>
@endpush
