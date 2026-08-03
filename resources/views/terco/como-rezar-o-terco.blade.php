{{-- resources/views/santo-terco/como-rezar-o-terco.blade.php --}}
@extends('layouts.site')

@php
  $SITE_URL = 'https://www.iatioben.com.br';
  $PAGE_PATH = '/santo-terco/como-rezar-o-terco';
  $CANONICAL_URL = $SITE_URL.$PAGE_PATH;

  // Adsense
  $ADS_CLIENT = 'ca-pub-8819996017476509';
  $ADS_SLOT_BODY_TOP = '7474884427';
  $ADS_SLOT_IN_ARTICLE = '6161802751';
  $ADS_SLOT_SIDEBAR_DESKTOP = '8534838745';
  $ADS_SLOT_SIDEBAR_MOBILE = '1573844576';
  $ADS_SLOT_IN_ARTICLE_2 = '5469336488';

  $OG_IMAGE = $SITE_URL.'/og/terco.png?v=1';

  $misterioLinks = [
    ['title' => 'Mistérios Gozosos',   'tipo' => 'misterios-gozosos',   'desc' => 'A infância de Jesus contemplada com Maria.',               'day' => 'Segunda e Sábado'],
    ['title' => 'Mistérios Dolorosos', 'tipo' => 'misterios-dolorosos', 'desc' => 'A Paixão e a Cruz: fé que permanece.',                  'day' => 'Terça e Sexta'],
    ['title' => 'Mistérios Gloriosos', 'tipo' => 'misterios-gloriosos', 'desc' => 'Ressurreição e esperança que não decepciona.',         'day' => 'Quarta e Domingo'],
    ['title' => 'Mistérios Luminosos', 'tipo' => 'misterios-luminosos', 'desc' => 'A vida pública de Jesus: luz para o caminho.',        'day' => 'Quinta'],
  ];

  $recommendedBlogLinks = [
    ['title' => 'Terço passo a passo (bem detalhado)', 'slug' => 'terco-passo-a-passo', 'desc' => 'Para quem quer aprender sem pressa, com explicações claras.'],
    ['title' => 'Diferença entre Terço e Rosário', 'slug' => 'diferenca-terco-e-rosario', 'desc' => 'Entenda os termos com simplicidade e precisão.'],
    ['title' => 'Qual mistério rezar em cada dia?', 'slug' => 'misterios-do-terco-por-dia-da-semana', 'desc' => 'Tabela + explicação espiritual para organizar a semana.'],
    ['title' => 'Como meditar os mistérios (sem “rezar no automático”)', 'slug' => 'como-meditar-os-misterios-do-terco', 'desc' => 'Práticas simples para rezar com o coração presente.'],
    ['title' => 'Terço para iniciantes', 'slug' => 'terco-para-iniciantes', 'desc' => 'Um começo leve e possível para quem está retomando a fé.'],
    ['title' => 'Rezar o terço sozinho: é válido? Como fazer?', 'slug' => 'rezar-o-terco-sozinho', 'desc' => 'Um guia pastoral para oração no dia a dia.'],
    ['title' => 'Rezar o terço online: é válido?', 'slug' => 'rezar-o-terco-online-e-valido', 'desc' => 'Como usar recursos digitais sem perder o espírito da oração.'],
    ['title' => 'Erros comuns ao rezar o terço', 'slug' => 'erros-comuns-ao-rezar-o-terco', 'desc' => 'Correções com carinho, sem culpas, com direção.'],
  ];

  // ===== SEO Meta =====
  $meta = [
    'html_lang' => 'pt-BR',
    'title' => 'Aprenda a rezar o Santo Terço passo a passo | IA Tio Ben',
    'description' => 'Descubra como rezar o Terço com sentido: ordem das orações, mistérios e dias certos. Um guia simples para começar hoje.',
    'canonical' => $CANONICAL_URL,
    'robots' => 'index,follow',
    'hreflangs' => [
      'pt-BR' => $CANONICAL_URL,
      'en' => $SITE_URL.'/en/rosary/how-to-pray-the-rosary',
      'x-default' => $CANONICAL_URL,
    ],
    'og_title' => 'Aprenda a rezar o Santo Terço passo a passo',
    'og_description' => 'Aprenda a rezar com sentido: passo a passo, mistérios e como manter constância. Clique e comece hoje.',
    'og_url' => $CANONICAL_URL,
    'og_image' => $OG_IMAGE,
  ];

  // ===== JSON-LD =====
  $date = now()->toDateString();

  $faq = [
    ['q' => 'O terço precisa ser rezado em voz alta?', 'a' => 'Não. Pode ser em voz alta, em silêncio ou mentalmente. O essencial é manter atenção e fé durante a oração.'],
    ['q' => 'Posso rezar o terço sem ter um terço nas mãos?', 'a' => 'Sim. Você pode acompanhar por texto e seguir a sequência com calma. O valor está na oração feita com o coração presente.'],
    ['q' => 'Qual é a diferença entre terço e rosário?', 'a' => 'O Rosário é o conjunto completo dos mistérios. O Terço é a forma mais comum do dia a dia, com cinco mistérios.'],
    ['q' => 'Qual mistério rezar em cada dia da semana?', 'a' => 'Segunda e sábado: gozosos. Terça e sexta: dolorosos. Quarta e domingo: gloriosos. Quinta: luminosos.'],
    ['q' => 'Rezar o terço online é válido?', 'a' => 'Sim. Recursos digitais podem ajudar na constância e na atenção. O importante é rezar com o coração presente, meditando os mistérios.'],
    ['q' => 'Preciso rezar tudo de uma vez?', 'a' => 'Não. Se necessário, reze por partes (um mistério por vez). O importante é perseverar e rezar com sentido.'],
    ['q' => 'Como evitar rezar “no automático”?', 'a' => 'Antes de cada dezena, anuncie o mistério e faça uma meditação breve (10–20 segundos). Se distrair, retorne com serenidade.'],
  ];

  $articleSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $CANONICAL_URL],
    'headline' => 'Como rezar o Terço: guia completo (com mistérios)',
    'description' => 'Guia completo e pastoral para entender e rezar o Terço Católico com sentido: resposta rápida, passo a passo, mistérios, dias da semana e dúvidas frequentes.',
    'inLanguage' => 'pt-BR',
    'author' => ['@type' => 'Organization', 'name' => 'IA Tio Ben', 'url' => $SITE_URL],
    'publisher' => [
      '@type' => 'Organization',
      'name' => 'IA Tio Ben',
      'url' => $SITE_URL,
      'logo' => ['@type' => 'ImageObject', 'url' => $SITE_URL.'/images/tio-ben-transparente.webp'],
    ],
    'datePublished' => $date,
    'dateModified' => $date,
    'image' => $SITE_URL.'/images/santo-do-dia-ia-tio-ben.png',
  ];

  $breadcrumbSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
      ['@type' => 'ListItem', 'position' => 1, 'name' => 'Início', 'item' => $SITE_URL.'/'],
      ['@type' => 'ListItem', 'position' => 2, 'name' => 'Santo Terço', 'item' => $SITE_URL.'/santo-terco'],
      ['@type' => 'ListItem', 'position' => 3, 'name' => 'Como rezar o Terço', 'item' => $CANONICAL_URL],
    ],
  ];

  $faqSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'inLanguage' => 'pt-BR',
    'mainEntity' => array_map(function ($x) {
      return [
        '@type' => 'Question',
        'name' => $x['q'],
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $x['a']],
      ];
    }, $faq),
  ];

  $howToSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'HowTo',
    'name' => 'Como rezar o Terço Católico (passo a passo)',
    'description' => 'Passo a passo simples para rezar o Terço Católico com calma, meditando os mistérios da vida de Jesus.',
    'inLanguage' => 'pt-BR',
    'estimatedCost' => ['@type' => 'MonetaryAmount', 'currency' => 'BRL', 'value' => '0'],
    'supply' => [['@type' => 'HowToSupply', 'name' => 'Um terço (opcional)']],
    'tool' => [['@type' => 'HowToTool', 'name' => 'Texto de acompanhamento (opcional)']],
    'step' => [
      ['@type' => 'HowToStep', 'position' => 1,  'name' => 'Sinal da Cruz', 'text' => 'Inicie com o Sinal da Cruz, oferecendo a oração a Deus.'],
      ['@type' => 'HowToStep', 'position' => 2,  'name' => 'Credo', 'text' => 'Reze o Credo, professando a fé.'],
      ['@type' => 'HowToStep', 'position' => 3,  'name' => 'Pai-Nosso', 'text' => 'Reze um Pai-Nosso.'],
      ['@type' => 'HowToStep', 'position' => 4,  'name' => 'Três Ave-Marias', 'text' => 'Reze três Ave-Marias, pedindo fé, esperança e caridade.'],
      ['@type' => 'HowToStep', 'position' => 5,  'name' => 'Glória', 'text' => 'Reze o Glória ao Pai.'],
      ['@type' => 'HowToStep', 'position' => 6,  'name' => 'Anunciar o mistério', 'text' => 'Anuncie o 1º mistério e faça uma breve meditação.'],
      ['@type' => 'HowToStep', 'position' => 7,  'name' => 'Pai-Nosso da dezena', 'text' => 'Reze um Pai-Nosso antes da dezena.'],
      ['@type' => 'HowToStep', 'position' => 8,  'name' => 'Dez Ave-Marias', 'text' => 'Reze dez Ave-Marias, contemplando o mistério.'],
      ['@type' => 'HowToStep', 'position' => 9,  'name' => 'Glória', 'text' => 'Reze o Glória ao Pai ao final da dezena.'],
      ['@type' => 'HowToStep', 'position' => 10, 'name' => 'Repetir até cinco mistérios', 'text' => 'Repita a sequência para os cinco mistérios do dia.'],
      ['@type' => 'HowToStep', 'position' => 11, 'name' => 'Salve-Rainha e oração final', 'text' => 'Finalize com a Salve-Rainha e uma oração final, confiando suas intenções.'],
    ],
  ];

  $itemListSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'ItemList',
    'name' => 'Navegação principal: como rezar o terço e mistérios',
    'itemListElement' => array_merge(
      [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Resposta rápida', 'url' => $CANONICAL_URL.'#resposta-rapida'],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Passo a passo', 'url' => $CANONICAL_URL.'#como-rezar'],
        ['@type' => 'ListItem', 'position' => 3, 'name' => 'Mistérios', 'url' => $CANONICAL_URL.'#misterios'],
        ['@type' => 'ListItem', 'position' => 4, 'name' => 'Dias da semana', 'url' => $CANONICAL_URL.'#dias'],
        ['@type' => 'ListItem', 'position' => 5, 'name' => 'Dúvidas frequentes', 'url' => $CANONICAL_URL.'#faq'],
      ],
      array_map(function ($m, $i) use ($SITE_URL) {
        return [
          '@type' => 'ListItem',
          'position' => 6 + $i,
          'name' => $m['title'],
          'url' => $SITE_URL.'/santo-terco/'.$m['tipo'],
        ];
      }, $misterioLinks, array_keys($misterioLinks))
    ),
  ];

  $webPageSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    '@id' => $CANONICAL_URL,
    'url' => $CANONICAL_URL,
    'name' => 'Como rezar o Terço: guia completo (com mistérios)',
    'inLanguage' => 'pt-BR',
    'isPartOf' => ['@type' => 'WebSite', 'name' => 'IA Tio Ben', 'url' => $SITE_URL],
    'primaryImageOfPage' => ['@type' => 'ImageObject', 'url' => $SITE_URL.'/images/santo-do-dia-ia-tio-ben.png'],
    'mainEntity' => [$howToSchema, $faqSchema],
  ];

  $jsonFlags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;

  // ===== Params do Aside (no formato do seu partial) =====
  $asideBlogLinks = array_map(function ($x) {
    return [
      'href'  => url('/blog/'.$x['slug']),
      'title' => $x['title'],
      'desc'  => $x['desc'],
    ];
  }, $recommendedBlogLinks);

  // Esta página não é um "set" -> fixa um padrão só pra visual (sem sumir highlight)
  $asideSetKey = 'luminosos';
@endphp

@section('html_lang', $meta['html_lang'])
@section('title', $meta['title'])
@section('meta_description', $meta['description'])
@section('canonical', $meta['canonical'])
@section('robots', $meta['robots'])

@section('hreflang')
  @foreach($meta['hreflangs'] as $lang => $url)
    <link rel="alternate" hreflang="{{ $lang }}" href="{{ $url }}"/>
  @endforeach
@endsection

@section('og_title', $meta['og_title'])
@section('og_description', $meta['og_description'])
@section('og_url', $meta['og_url'])
@section('og_image', $meta['og_image'])

@push('head')
  <style>
    /* ===== Layout grid (fallback sem Tailwind lg:) ===== */
    .rosary-grid { display: block; }
    .rosary-aside-desktop { display: none; }
    .rosary-aside-mobile { display: block; }

    @media (min-width: 1024px) {
      .rosary-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 320px;
        gap: 24px;
        align-items: start;
      }
      .rosary-aside-desktop { display: block; }
      .rosary-aside-mobile { display: none; }
    }
  </style>
@endpush

@section('content')
  {{-- Adsense loader (1x por página) --}}
  <script
    async
    src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ $ADS_CLIENT }}"
    crossorigin="anonymous"></script>

  {{-- JSON-LD (SEO/AEO) --}}
  <script type="application/ld+json">{!! json_encode($webPageSchema, $jsonFlags) !!}</script>
  <script type="application/ld+json">{!! json_encode($articleSchema, $jsonFlags) !!}</script>
  <script type="application/ld+json">{!! json_encode($breadcrumbSchema, $jsonFlags) !!}</script>
  <script type="application/ld+json">{!! json_encode($faqSchema, $jsonFlags) !!}</script>
  <script type="application/ld+json">{!! json_encode($howToSchema, $jsonFlags) !!}</script>
  <script type="application/ld+json">{!! json_encode($itemListSchema, $jsonFlags) !!}</script>

  <article
    class="post-santo mx-auto w-full max-w-6xl px-3 pb-16 pt-6 sm:px-6 lg:px-8 text-foreground mt-10"
    itemscope itemtype="https://schema.org/Article"
  >
    {{-- HERO --}}
    <header class="space-y-4 mb-6">
      <div class="inline-flex items-center gap-2 rounded-full border border-border bg-muted px-3 py-1 text-xs font-semibold text-foreground">
        IA Tio Ben • Hub do Santo Terço
      </div>

      <h1 class="font-reading text-3xl font-extrabold tracking-tight sm:text-4xl">
        Como rezar o Terço: guia completo para rezar com sentido
      </h1>

      <p class="font-reading text-base leading-relaxed text-muted-foreground sm:text-lg">
        Se você quer uma explicação simples e uma forma prática de começar agora, este guia foi feito para você:
        resposta rápida, passo a passo, mistérios e dúvidas comuns — tudo no mesmo lugar.
      </p>

      {{-- CTAs (mobile-first) --}}
      <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        <a
          href="{{ url('/santo-terco') }}"
          class="group inline-flex items-center justify-center gap-2 rounded-xl bg-amber-600 px-4 py-3 text-sm font-semibold text-white shadow-sm ring-1 ring-amber-700/20 transition hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500"
        >
          Rezar agora no Santo Terço
          <span class="text-white/90 transition group-hover:translate-x-0.5">→</span>
        </a>

        <a
          href="#resposta-rapida"
          class="inline-flex items-center justify-center gap-2 rounded-xl border border-border bg-card px-4 py-3 text-sm font-semibold shadow-sm transition hover:bg-muted focus:outline-none focus:ring-2 focus:ring-amber-500"
        >
          Ver resposta rápida (60s)
        </a>

        <a
          href="#misterios"
          class="inline-flex items-center justify-center gap-2 rounded-xl border border-border bg-card px-4 py-3 text-sm font-semibold shadow-sm transition hover:bg-muted focus:outline-none focus:ring-2 focus:ring-amber-500 sm:col-span-2 lg:col-span-1"
        >
          Ver os mistérios
        </a>
      </div>
    </header>

    {{-- GRID: conteúdo + aside (igual modelo page.blade.php) --}}
    <div class="rosary-grid gap-6 items-start">
      {{-- MAIN --}}
      <section class="min-w-0 rounded-2xl border border-border bg-card p-4 shadow-sm sm:p-6 lg:p-8">


        {{-- Resposta rápida --}}
        <section id="resposta-rapida" class="rounded-2xl border border-amber-200 bg-amber-50 p-4 sm:p-5 mb-6" aria-label="Resposta rápida">
          <h2 class="text-base font-semibold text-foreground"><strong>Resposta rápida: como rezar o terço</strong></h2>
          <p class="mt-1 text-sm text-muted-foreground">Se você só quer a sequência sem explicações longas, siga isso:</p>

          <ol class="mt-3 list-decimal space-y-1 pl-5 text-sm text-foreground">
            <li>Sinal da Cruz</li>
            <li>Credo</li>
            <li>Pai-Nosso</li>
            <li>3 Ave-Marias</li>
            <li>Glória</li>
            <li>Anuncie 1 mistério + breve meditação (10–20s)</li>
            <li>Pai-Nosso + 10 Ave-Marias + Glória (1 dezena)</li>
            <li>Repita até completar 5 mistérios</li>
            <li>Salve-Rainha + oração final</li>
          </ol>

          <div class="mt-4 grid gap-3 sm:grid-cols-2">
            <a href="{{ url('/santo-terco') }}"
              class="inline-flex items-center justify-center rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500">
              Rezar agora (guiado)
            </a>
            <a href="#como-rezar"
              class="inline-flex items-center justify-center rounded-xl border border-amber-200 bg-card px-4 py-2.5 text-sm font-semibold text-amber-900 shadow-sm transition hover:bg-amber-100 focus:outline-none focus:ring-2 focus:ring-amber-500">
              Ver passo a passo detalhado
            </a>
          </div>
        </section>

        {{-- Conteúdo (prose) --}}
        <div class="prose prose-amber max-w-none font-reading leading-relaxed
                    prose-p:my-4 prose-li:my-1
                    prose-h2:mt-10 prose-h2:mb-4 prose-h2:font-extrabold prose-h2:text-2xl prose-h2:text-foreground
                    prose-h3:mt-8 prose-h3:mb-3 prose-h3:font-bold prose-h3:text-xl prose-h3:text-foreground">

          <h2 id="o-que-e"><strong>O que é o Terço Católico?</strong></h2>
          <p>
            O terço é uma oração mariana que nos conduz a Jesus. Enquanto rezamos o Pai-Nosso, a Ave-Maria e o Glória,
            meditamos os <strong>mistérios da vida de Cristo</strong>.
          </p>

          <h2><strong>Para que serve rezar o terço?</strong></h2>
          <ul>
            <li>Silenciar o coração e retomar o foco em Deus</li>
            <li>Meditar o Evangelho com calma</li>
            <li>Confiar intenções pessoais e familiares</li>
            <li>Aprender a perseverar na oração</li>
            <li>Encontrar paz no meio das lutas</li>
          </ul>

          <h2><strong>Terço e Rosário: é a mesma coisa?</strong></h2>
          <p>
            O <strong>Rosário</strong> é o conjunto completo dos mistérios. O <strong>Terço</strong> é a forma mais comum do dia a dia:
            <strong> cinco mistérios</strong>.
          </p>

          <h2 id="como-rezar"><strong>Como rezar o terço (passo a passo)</strong></h2>
          <p>
            Rezar o terço não é corrida. É caminho. Se possível, antes de começar, faça uma intenção simples (por alguém, por uma causa,
            por gratidão).
          </p>

          <h3><strong>Checklist rápido (para não se perder)</strong></h3>
          <ol>
            <li>Sinal da Cruz</li>
            <li>Credo</li>
            <li>Pai-Nosso</li>
            <li>Três Ave-Marias</li>
            <li>Glória</li>
            <li>Anunciar o 1º mistério + meditar</li>
            <li>Pai-Nosso</li>
            <li>Dez Ave-Marias</li>
            <li>Glória</li>
            <li>Repetir até cinco mistérios</li>
            <li>Salve-Rainha e oração final</li>
          </ol>

          <p>
            Para evitar rezar “no automático”, faça uma pausa curta antes de cada dezena: anuncie o mistério, respire, e pense no Evangelho.
            Se a mente se distrair, volte com serenidade.
          </p>

          <div class="not-prose mt-6 grid gap-3 sm:grid-cols-2 mb-6">
            <a href="{{ url('/blog/como-meditar-os-misterios-do-terco') }}"
              class="inline-flex items-center justify-center rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-900 shadow-sm transition hover:bg-amber-100 focus:outline-none focus:ring-2 focus:ring-amber-500">
              Como meditar os mistérios (blog)
            </a>
            <a href="{{ url('/blog/rezar-o-terco-sozinho') }}"
              class="inline-flex items-center justify-center rounded-xl border border-border bg-card px-4 py-3 text-sm font-semibold shadow-sm transition hover:bg-muted focus:outline-none focus:ring-2 focus:ring-amber-500">
              Erros comuns ao rezar (blog)
            </a>
          </div>

          {{-- Anúncio meio do conteúdo --}}
          <div class="not-prose rounded-2xl border border-border bg-card p-3 shadow-sm mb-6">
            <p class="px-1 pb-2 text-[11px] font-semibold text-muted-foreground">Publicidade</p>
            <div class="flex justify-center">
              <ins class="adsbygoogle"
                style="display:block; min-height:160px"
                data-ad-client="{{ $ADS_CLIENT }}"
                data-ad-slot="{{ $ADS_SLOT_IN_ARTICLE }}"
                data-ad-format="auto"
                data-full-width-responsive="true"></ins>
            </div>
            <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
          </div>

          <h2 id="misterios"><strong>Mistérios do Terço</strong></h2>
          <p>Os mistérios são momentos da vida de Jesus contemplados durante a oração. Abra o tipo de mistério do dia e reze com calma.</p>
        </div>

        {{-- Cards dos mistérios --}}
        <div class="mt-6 grid gap-3 sm:grid-cols-2 mb-6">
          @foreach($misterioLinks as $m)
            <a href="{{ url('/santo-terco/'.$m['tipo']) }}"
              class="rounded-2xl border border-amber-200 bg-card p-4 shadow-sm transition hover:bg-amber-50 focus:outline-none focus:ring-2 focus:ring-amber-500">
              <div class="flex items-start justify-between gap-3">
                <div>
                  <p class="text-sm font-semibold text-foreground">{{ $m['title'] }}</p>
                  <p class="mt-1 text-sm text-muted-foreground">{{ $m['desc'] }}</p>
                </div>
                <span class="shrink-0 rounded-full border border-amber-200 bg-amber-50 px-2 py-1 text-xs font-semibold text-amber-900">
                  {{ $m['day'] }}
                </span>
              </div>
              <p class="mt-3 text-xs font-semibold text-amber-900">Abrir página →</p>
            </a>
          @endforeach
        </div>


        {{-- Dias da semana --}}
        <div class="prose prose-amber max-w-none font-reading leading-relaxed mb-4
                    prose-h2:font-extrabold prose-h2:text-2xl prose-h2:text-foreground">
          <h2 id="dias"><strong>Qual mistério rezar em cada dia?</strong></h2>
        </div>

        <div class="overflow-hidden rounded-2xl border border-amber-200 bg-card mb-6">
          <div class="grid grid-cols-1 divide-y divide-amber-100 sm:grid-cols-2 sm:divide-x sm:divide-y-0">
            @php
              $dias = [
                ['Segunda-feira', 'Mistérios Gozosos'],
                ['Terça-feira', 'Mistérios Dolorosos'],
                ['Quarta-feira', 'Mistérios Gloriosos'],
                ['Quinta-feira', 'Mistérios Luminosos'],
                ['Sexta-feira', 'Mistérios Dolorosos'],
                ['Sábado', 'Mistérios Gozosos'],
                ['Domingo', 'Mistérios Gloriosos'],
              ];
            @endphp
            @foreach($dias as $idx => $row)
              <div class="p-4 {{ $idx === 6 ? 'sm:col-span-2' : '' }}">
                <p class="text-sm font-semibold text-foreground">{{ $row[0] }}</p>
                <p class="text-sm text-muted-foreground">{{ $row[1] }}</p>
              </div>
            @endforeach
          </div>
        </div>

        {{-- FAQ --}}
        <div class="prose prose-amber max-w-none font-reading leading-relaxed mb-4
                    prose-h2:font-extrabold prose-h2:text-2xl prose-h2:text-foreground">
          <h2 id="faq"><strong>Dúvidas frequentes</strong></h2>
        </div>


        <div class="space-y-3 mb-6">
          @foreach([
            ['O terço precisa ser rezado em voz alta?', 'Não. Pode ser em voz alta, em silêncio ou mentalmente. O essencial é a atenção e a fé.'],
            ['Posso rezar o terço sem ter um terço nas mãos?', 'Sim. Você pode acompanhar por texto e seguir a sequência com calma. O valor está na oração.'],
            ['Qual a diferença entre terço e rosário?', 'O Rosário é o conjunto completo. O Terço é a forma mais comum do dia a dia: cinco mistérios.'],
            ['Preciso rezar tudo de uma vez?', 'Não. Reze por partes (um mistério por vez) se necessário. O importante é perseverar.'],
            ['Rezar o terço online é válido?', 'Sim. Pode ajudar na constância e na atenção. O importante é rezar com o coração presente.'],
          ] as $qa)
            <details class="rounded-xl border border-amber-200 bg-card p-4">
              <summary class="cursor-pointer text-sm font-semibold text-foreground">{{ $qa[0] }}</summary>
              <p class="mt-3 text-sm text-muted-foreground">{{ $qa[1] }}</p>
            </details>
          @endforeach
        </div>

        {{-- CTA final --}}
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 sm:p-6">
          <div class="space-y-3">
            <p class="text-base font-semibold text-amber-900"><strong>Vamos dar um passo hoje?</strong></p>
            <p class="text-sm leading-relaxed text-amber-900/90">
              Se você está cansado ou ansioso, comece por um mistério. Um só. Com calma. Deus já está te ouvindo.
            </p>
            <div class="grid gap-3 sm:grid-cols-2">
              <a href="{{ url('/santo-terco') }}"
                class="inline-flex items-center justify-center rounded-xl bg-amber-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500">
                Rezar agora no Santo Terço
              </a>
              <a href="{{ url('/blog/rezar-o-terco-sozinho') }}"
                class="inline-flex items-center justify-center rounded-xl border border-amber-200 bg-card px-4 py-3 text-sm font-semibold text-amber-900 shadow-sm transition hover:bg-amber-100 focus:outline-none focus:ring-2 focus:ring-amber-500">
                Rezar sozinho (blog)
              </a>
            </div>
          </div>
        </div>
      </section>

      {{-- MOBILE: Aside colapsável (igual modelo) --}}
      <div class="rosary-aside-mobile mt-6 lg:hidden">
        <div class="rounded-2xl bg-white/90 p-4 shadow-sm ring-1 ring-slate-200/60">
          <div class="flex items-center justify-between">
            <p class="text-xs font-semibold tracking-wide text-amber-700 uppercase">Acesso rápido</p>
            <button type="button" id="rosary-aside-toggle"
              class="text-xs font-semibold text-slate-600 hover:text-slate-900">
              Mostrar
            </button>
          </div>

          <div id="rosary-aside-mobile-panel" class="mt-3 hidden">
            @include('terco.partials.aside', [
              'lang' => 'pt',
              'variant' => 'mobile',
              'setKey' => $asideSetKey,
              'adsSlotDesktop300x250' => $ADS_SLOT_SIDEBAR_DESKTOP,
              'adsSlotMobile' => $ADS_SLOT_SIDEBAR_MOBILE,
              'blogLinks' => $asideBlogLinks,
            ])
          </div>
        </div>
      </div>

      {{-- ASIDE Desktop (fixo à direita) --}}
      <aside class="rosary-aside-desktop hidden lg:block min-w-0">
        <div class="sticky top-20">
          @include('terco.partials.aside', [
            'lang' => 'pt',
            'variant' => 'desktop',
            'setKey' => $asideSetKey,
            'adsSlotDesktop300x250' => $ADS_SLOT_SIDEBAR_DESKTOP,
            'adsSlotMobile' => $ADS_SLOT_SIDEBAR_MOBILE,
            'blogLinks' => $asideBlogLinks,
          ])
        </div>
      </aside>
    </div>
  </article>
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
    toggle.textContent = open ? 'Mostrar' : 'Ocultar';
  });
})();
</script>
@endpush
