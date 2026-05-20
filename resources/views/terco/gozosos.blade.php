{{-- resources/views/terco/gozosos.blade.php --}}
@extends('layouts.site')

@php
  $SITE_URL = 'https://www.iatioben.com.br';
  $PAGE_PATH = '/santo-terco/misterios-gozosos';
  $CANONICAL_URL = $SITE_URL.$PAGE_PATH;

  // AdSense
  $ADS_CLIENT = 'ca-pub-8819996017476509';
  $ADS_SLOT_BODY_TOP = '3041346283';
  $ADS_SLOT_IN_ARTICLE = '2672028232';
  $ADS_SLOT_SIDEBAR_DESKTOP = '3041346283';
  $ADS_SLOT_SIDEBAR_MOBILE = '3041346283';

  $today = now()->toDateString();

  // ✅ setKey usado pelo partial para set ativo
  $setKey = 'gozosos';

  $mysteries = [
    [
      'id' => 'anunciacao',
      'title' => '1º Mistério Gozoso',
      'subtitle' => 'A Anunciação do Senhor',
      'passageRef' => 'Lc 1,26–38',
      'passageQuote' => '“Eis aqui a serva do Senhor; faça-se em mim segundo a tua palavra.”',
      'reflection' => [
        'A alegria do Evangelho não começa quando tudo está resolvido. Ela começa quando Deus entra no cotidiano e pede espaço no coração.',
        'Maria não recebe um mapa, mas um chamado. E a primeira reação é humana: ela se inquieta, pergunta, pondera. A fé não é anestesia; é confiança que se constrói em diálogo com Deus.',
        'O “faça-se” de Maria não é passividade. É entrega ativa: ela se coloca inteira à disposição do plano do Senhor. E, quando a vida pede respostas rápidas, este mistério nos lembra que a verdadeira maturidade espiritual nasce de uma decisão interior: confiar.',
        'Rezar este mistério é colocar diante de Deus aquilo que você ainda não entende: uma espera, um medo, uma mudança. E pedir a graça de responder com humildade e coragem, sem endurecer o coração.',
      ],
      'points' => [
        'Pergunta para meditar: o que Deus está me convidando a acolher hoje?',
        'Fruto espiritual tradicional: humildade e docilidade à vontade de Deus.',
      ],
    ],
    [
      'id' => 'visitacao',
      'title' => '2º Mistério Gozoso',
      'subtitle' => 'A Visitação de Maria a Isabel',
      'passageRef' => 'Lc 1,39–56',
      'passageQuote' => '“Logo que Isabel ouviu a saudação de Maria, a criança pulou em seu ventre.”',
      'reflection' => [
        'A fé que é só ideia costuma ficar parada. A fé verdadeira se move: ela visita, serve, fortalece.',
        'Maria, recém-chamada por Deus, não se fecha no próprio mistério. Ela caminha ao encontro de alguém que precisa. Há algo profundamente evangélico nessa prontidão: Deus não nos dá dons para nos isolar, mas para amar melhor.',
        'A Visitação também nos ensina que a presença de Cristo em nós transforma o ambiente: onde Maria chega, há alegria, há vida que salta, há bênção que se reconhece.',
        'Rezar este mistério é pedir um coração disponível: capaz de sair de si, de enxergar necessidades reais e de levar consolo sem espetáculo. Às vezes, o maior apostolado é uma visita fiel e discreta.',
      ],
      'points' => [
        'Pergunta para meditar: quem precisa da minha presença hoje — mesmo que simples?',
        'Fruto espiritual tradicional: caridade concreta e serviço.',
      ],
    ],
    [
      'id' => 'nascimento',
      'title' => '3º Mistério Gozoso',
      'subtitle' => 'O Nascimento de Jesus',
      'passageRef' => 'Lc 2,1–20',
      'passageQuote' => '“Hoje, na cidade de Davi, nasceu para vós um Salvador.”',
      'reflection' => [
        'Deus escolhe nascer pequeno. Ele não entra pela imposição, mas pela proximidade. O presépio revela um estilo: o Senhor se aproxima da nossa realidade sem pedir perfeição prévia.',
        'Belém é o anúncio silencioso de que Deus não se envergonha da pobreza humana: das limitações, dos improvisos, das noites difíceis. Pelo contrário, Ele faz morada ali.',
        'Neste mistério, a alegria cristã não é euforia — é esperança. É saber que, mesmo quando as condições parecem desfavoráveis, Deus está presente e agindo com ternura.',
        'Rezar o Nascimento é aprender a acolher Jesus do jeito certo: com simplicidade. Se o coração estiver cansado, comece pequeno. Uma oração breve, um silêncio sincero, um recomeço.',
      ],
      'points' => [
        'Pergunta para meditar: onde eu preciso acolher Deus com mais simplicidade?',
        'Fruto espiritual tradicional: desprendimento e amor à humildade.',
      ],
    ],
    [
      'id' => 'apresentacao',
      'title' => '4º Mistério Gozoso',
      'subtitle' => 'A Apresentação do Senhor no Templo',
      'passageRef' => 'Lc 2,22–40 (cf. 2,22–35)',
      'passageQuote' => '“Meus olhos viram a tua salvação.”',
      'reflection' => [
        'No Templo, Maria e José apresentam Jesus ao Pai. Este gesto é uma catequese viva: tudo o que recebemos é dom confiado, não posse absoluta.',
        'Simeão reconhece a salvação onde muitos veriam apenas um bebê. A fé enxerga além do óbvio: ela percebe o agir de Deus mesmo quando ainda é pequeno, escondido, silencioso.',
        'Mas há também profecia de dor: a luz que salva também contradiz o mundo. A alegria cristã amadurece quando aprende a permanecer fiel sem romantizar a cruz.',
        'Rezar este mistério é colocar no altar aquilo que você ama: família, projetos, futuro. Não para perder, mas para ordenar. Quando Deus é o primeiro, o amor se torna mais puro e mais livre.',
      ],
      'points' => [
        'Pergunta para meditar: o que eu preciso apresentar a Deus hoje, com confiança?',
        'Fruto espiritual tradicional: pureza de intenção e entrega.',
      ],
    ],
    [
      'id' => 'templo',
      'title' => '5º Mistério Gozoso',
      'subtitle' => 'A Perda e o Encontro do Menino Jesus no Templo',
      'passageRef' => 'Lc 2,41–52',
      'passageQuote' => '“Não sabíeis que devo ocupar-me das coisas de meu Pai?”',
      'reflection' => [
        'Este mistério toca uma experiência humana delicada: procurar Deus quando parece que Ele se afastou. Maria e José conhecem a angústia de não encontrar Jesus onde esperavam.',
        'O Evangelho mostra que a busca é parte do caminho. Há fases em que a fé é luminosa; em outras, é fidelidade. E é justamente aí que se purifica o amor: quando seguimos procurando, mesmo sem consolações.',
        'Quando Jesus é reencontrado no Templo, Ele revela sua identidade e missão. Não é indiferença com a dor dos pais: é um convite a olhar mais alto, a não reduzir Deus aos nossos esquemas.',
        'Rezar este mistério é aprender a perseverar: voltar ao essencial (a casa do Pai), retomar a oração com humildade, buscar direção segura. Se hoje você se sente em aridez, não conclua que Deus foi embora. Continue procurando — com paciência e firmeza.',
      ],
      'points' => [
        'Pergunta para meditar: em que lugar eu preciso voltar ao essencial?',
        'Fruto espiritual tradicional: verdadeira sabedoria e perseverança.',
      ],
    ],
  ];

  $splitPoint = function (?string $raw): string {
    if (!$raw) return '';
    $pos = mb_strpos($raw, ':');
    if ($pos === false) return trim($raw);
    return trim(mb_substr($raw, $pos + 1));
  };

  $faq = [
    ['q' => 'Em quais dias se rezam os Mistérios Gozosos?', 'a' => 'Tradicionalmente, os Mistérios Gozosos são rezados às segundas-feiras e aos sábados.'],
    ['q' => 'Quais são os cinco Mistérios Gozosos?', 'a' => 'Anunciação, Visitação, Nascimento de Jesus, Apresentação no Templo e Encontro do Menino Jesus no Templo.'],
    ['q' => 'Como meditar os Mistérios Gozosos sem rezar no automático?', 'a' => 'Antes de cada dezena, anuncie o mistério, faça 10–20 segundos de silêncio e reze as Ave-Marias ligando cada conta à cena contemplada; se distrair, retorne com serenidade.'],
    ['q' => 'Posso rezar os Mistérios Gozosos em outro dia da semana?', 'a' => 'Sim. A divisão por dias é uma orientação devocional; você pode escolher o mistério conforme a necessidade espiritual do momento.'],
  ];

  $articleSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $CANONICAL_URL],
    'headline' => 'Mistérios Gozosos do Terço: passagens bíblicas e reflexões',
    'description' => 'Guia completo dos Mistérios Gozosos do Terço com referências bíblicas, reflexões para meditação e orientações práticas para rezar com sentido.',
    'inLanguage' => 'pt-BR',
    'author' => ['@type' => 'Organization', 'name' => 'IA Tio Ben', 'url' => $SITE_URL],
    'publisher' => [
      '@type' => 'Organization',
      'name' => 'IA Tio Ben',
      'url' => $SITE_URL,
      'logo' => ['@type' => 'ImageObject', 'url' => $SITE_URL.'/images/ben-transparente.png'],
    ],
    'datePublished' => $today,
    'dateModified' => $today,
    'image' => $SITE_URL.'/images/santo-do-dia-ia-tio-ben.png',
    'about' => [
      ['@type' => 'Thing', 'name' => 'Mistérios Gozosos'],
      ['@type' => 'Thing', 'name' => 'Rosário'],
      ['@type' => 'Thing', 'name' => 'Terço'],
      ['@type' => 'Thing', 'name' => 'Devoção mariana'],
    ],
    'keywords' => [
      'mistérios gozosos',
      'mistérios do terço',
      'mistérios do rosário',
      'segunda e sábado',
      'como meditar o terço',
      'passagens bíblicas do terço',
    ],
  ];

  $breadcrumbSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
      ['@type' => 'ListItem', 'position' => 1, 'name' => 'Início', 'item' => $SITE_URL.'/'],
      ['@type' => 'ListItem', 'position' => 2, 'name' => 'Santo Terço', 'item' => $SITE_URL.'/santo-terco'],
      ['@type' => 'ListItem', 'position' => 3, 'name' => 'Mistérios Gozosos', 'item' => $CANONICAL_URL],
    ],
  ];

  $faqSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'inLanguage' => 'pt-BR',
    'mainEntity' => array_map(fn($x) => [
      '@type' => 'Question',
      'name' => $x['q'],
      'acceptedAnswer' => ['@type' => 'Answer', 'text' => $x['a']],
    ], $faq),
  ];

  $itemListSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'ItemList',
    'name' => 'Mistérios Gozosos do Terço',
    'itemListElement' => array_map(function($m, $idx) use ($CANONICAL_URL) {
      return [
        '@type' => 'ListItem',
        'position' => $idx + 1,
        'name' => $m['title'].' — '.$m['subtitle'],
        'url' => $CANONICAL_URL.'#'.$m['id'],
        'description' => $m['passageRef'].'.',
      ];
    }, $mysteries, array_keys($mysteries)),
  ];

  $webPageSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    '@id' => $CANONICAL_URL,
    'url' => $CANONICAL_URL,
    'name' => 'Mistérios Gozosos do Terço: passagens bíblicas e reflexões',
    'inLanguage' => 'pt-BR',
    'isPartOf' => ['@type' => 'WebSite', 'name' => 'IA Tio Ben', 'url' => $SITE_URL],
    'mainEntity' => [$itemListSchema, $faqSchema],
  ];

  $readingProse =
    'prose prose-amber max-w-none '.
    'prose-p:leading-[1.9] prose-li:leading-[1.85] '.
    'prose-p:my-4 prose-li:my-2 '.
    'prose-h2:mt-10 prose-h2:mb-4 prose-h3:mt-8 prose-h3:mb-3 '.
    'text-[17px] sm:text-[18px] lg:text-[18.5px] '.
    'text-foreground break-words hyphens-auto';

  $blogLinks = [
    ['title' => 'O sentido do “faça-se” de Maria (Anunciação)', 'slug' => 'sentido-do-faca-se-de-maria'],
    ['title' => 'O Magnificat: alegria que nasce da gratidão (Visitação)', 'slug' => 'magnificat-explicacao'],
    ['title' => 'Natal e simplicidade: como acolher Jesus no cotidiano', 'slug' => 'natal-e-simplicidade'],
    ['title' => 'Simeão e Ana: esperar com fidelidade (Apresentação)', 'slug' => 'simeao-e-ana'],
    ['title' => 'Quando parece que perdi Deus: o que fazer? (Templo)', 'slug' => 'quando-parece-que-perdi-deus'],
  ];

  // ✅ formato aceito pelo aside: {href, title, desc}
  $blogLinksAside = array_map(function($x) {
    return [
      'href' => url('/blog/'.$x['slug']),
      'title' => $x['title'],
      'desc' => null,
    ];
  }, $blogLinks);
@endphp

@section('html_lang', 'pt-BR')
@section('title', 'Santo Terço: Mistérios Gozosos — passagens bíblicas e reflexões')
@section('meta_description', 'Reze e medite os Mistérios Gozosos (segunda e sábado) com passagens bíblicas e reflexões para contemplar a infância de Jesus com Maria.')
@section('canonical', $CANONICAL_URL)
@section('robots', 'index,follow')

@section('hreflang')
  <link rel="alternate" hreflang="pt-BR" href="{{ $CANONICAL_URL }}"/>
  <link rel="alternate" hreflang="en" href="{{ $SITE_URL }}/en/rosary/joyful-mysteries"/>
  <link rel="alternate" hreflang="x-default" href="{{ $CANONICAL_URL }}"/>
@endsection

@section('og_title', 'Santo Terço: Mistérios Gozosos')
@section('og_description', 'Segunda e sábado: contemple a alegria do Evangelho. Clique e reze com Bíblia e meditações em cada dezena.')
@section('og_url', $CANONICAL_URL)
@section('og_image', $SITE_URL.'/og/terco/misterios-gozosos.png?v=1')

@push('head')
  {{-- JSON-LD --}}
  <script type="application/ld+json">{!! json_encode($webPageSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
  <script type="application/ld+json">{!! json_encode($articleSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
  <script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
  <script type="application/ld+json">{!! json_encode($faqSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
  <script type="application/ld+json">{!! json_encode($itemListSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>

  {{-- Adsense loader (1x por página) --}}
  <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ $ADS_CLIENT }}" crossorigin="anonymous"></script>

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
<article class="mx-auto w-full max-w-6xl px-3 pb-16 pt-6 sm:px-6 lg:px-8 text-foreground mt-10" itemscope itemtype="https://schema.org/Article">
  <div id="topo" class="h-0 w-0 scroll-mt-24"></div>

  <header class="space-y-4 mt-3">
    <div class="flex flex-wrap items-center gap-2">
      <span class="inline-flex items-center rounded-full border border-border bg-muted px-3 py-1 text-xs font-semibold text-foreground">
        Santo Terço
      </span>
      <span class="inline-flex items-center rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-900">
        Segunda e Sábado
      </span>
    </div>

    <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl">
      Mistérios Gozosos do Terço
    </h1>

    <p class="text-base leading-relaxed text-muted-foreground sm:text-lg">
      Aqui você encontra os 5 Mistérios Gozosos com <strong>passagens bíblicas</strong> e reflexões para rezar com sentido:
      Anunciação, Visitação, Nascimento, Apresentação e Encontro no Templo.
    </p>

    <section class="rounded-2xl border border-amber-200 bg-amber-50 p-4 shadow-sm sm:p-5" aria-label="Resposta rápida">
      <p class="text-xs font-semibold text-amber-900">Resposta rápida</p>
      <div class="mt-2 space-y-2 text-sm leading-relaxed text-foreground">
        <p>
          Os <strong>Mistérios Gozosos</strong> são rezados tradicionalmente às <strong>segundas</strong> e aos <strong>sábados</strong>.
          Eles ensinam a alegria da fé que começa no simples: acolher, servir e perseverar.
        </p>
        <p class="text-muted-foreground">
          Dica prática: antes de cada dezena, anuncie o mistério e faça 10–20 segundos de silêncio; depois, reze cada Ave-Maria mantendo a cena no coração.
        </p>
      </div>

      <div class="mt-4 flex flex-wrap gap-2">
        <a href="{{ url('/santo-terco') }}"
           class="inline-flex items-center justify-center rounded-xl bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500">
          Rezar agora (guiado)
        </a>
        <a href="{{ url('/santo-terco/como-rezar-o-terco') }}"
           class="inline-flex items-center justify-center rounded-xl border border-amber-200 bg-white px-4 py-2 text-sm font-semibold text-amber-900 shadow-sm hover:bg-amber-100 focus:outline-none focus:ring-2 focus:ring-amber-500">
          Como rezar o terço
        </a>
      </div>
    </section>
  </header>

  <div class="rosary-grid gap-6 items-start mt-6">
    {{-- MAIN --}}
    <section class="min-w-0 rounded-2xl border border-border bg-card p-4 shadow-sm sm:p-6 lg:p-8">
      <nav aria-label="Sumário dos Mistérios Gozosos" class="rounded-2xl border border-border bg-muted/40 p-4">
        <p class="text-sm font-semibold text-foreground">Sumário</p>
        <div class="mt-3 grid gap-2 sm:grid-cols-2">
          @foreach($mysteries as $m)
            <a href="#{{ $m['id'] }}"
               class="rounded-xl border border-border bg-card px-3 py-2 text-sm text-muted-foreground shadow-sm transition hover:bg-muted hover:text-foreground">
              {{ $m['title'] }}: {{ $m['subtitle'] }}
            </a>
          @endforeach
        </div>
      </nav>

      {{-- Adsense responsive (topo do body) --}}
      <div class="mt-4 rounded-2xl border border-border bg-card p-3 shadow-sm">
        <p class="px-1 pb-2 text-[11px] font-semibold text-muted-foreground">Publicidade</p>
        <div class="w-full overflow-hidden rounded-xl bg-muted/30" style="min-height:160px">
          <ins class="adsbygoogle"
               style="display:block;min-height:160px"
               data-ad-client="{{ $ADS_CLIENT }}"
               data-ad-slot="{{ $ADS_SLOT_BODY_TOP }}"
               data-ad-format="auto"
               data-full-width-responsive="true"></ins>
        </div>
        @push('scripts')
          <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
        @endpush
      </div>

      <div class="{{ $readingProse }} mt-7">
        <h2 id="o-que-sao">O que são os Mistérios Gozosos?</h2>
        <p>
          Os Mistérios Gozosos nos colocam diante das primeiras páginas do Evangelho: o sim de Maria, a caridade que se põe a caminho,
          o nascimento do Salvador, a oferta no Templo e a busca perseverante quando parece que Jesus “sumiu” do nosso horizonte.
        </p>
        <p>
          Eles são um caminho de oração especialmente fecundo para recomeços, gratidão e confiança: alegria que não depende de euforia,
          mas de fidelidade e presença de Deus no cotidiano.
        </p>
      </div>

      <div class="mt-8 space-y-7">
        @foreach($mysteries as $idx => $m)
          @php
            $pergunta = $splitPoint($m['points'][0] ?? '');
            $fruto = $splitPoint($m['points'][1] ?? '');
            $anchor = $m['reflection'][1] ?? null;
          @endphp

          <div class="space-y-4 mt-3">
            <section id="{{ $m['id'] }}"
                     class="scroll-mt-24 rounded-2xl border border-amber-200 bg-amber-50/40 p-4 shadow-sm sm:p-6">
              <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                <div>
                  <p class="text-xs font-semibold text-amber-900">{{ $m['title'] }}</p>
                  <h3 class="mt-1 text-2xl font-bold text-foreground">{{ $m['subtitle'] }}</h3>
                </div>
                <span class="inline-flex w-fit items-center rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-900">
                  {{ $m['passageRef'] }}
                </span>
              </div>

              <div class="mt-5 rounded-2xl border border-amber-200 bg-white p-5">
                <p class="text-xs font-semibold text-amber-900">Passagem bíblica</p>
                <p class="mt-3 text-[15px] leading-relaxed text-foreground sm:text-[16px]">
                  “{{ $m['passageQuote'] }}”
                </p>
                <p class="mt-2 text-xs text-muted-foreground">{{ $m['passageRef'] }}</p>
              </div>

              <div class="mt-6">
                <p class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Meditação</p>

                <div class="mt-3 max-w-[65ch]">
                  <div class="space-y-4 text-[15px] leading-relaxed text-foreground sm:text-[16px]">
                    @foreach($m['reflection'] as $p)
                      <p class="m-0">{{ $p }}</p>
                    @endforeach
                  </div>

                  @if($anchor)
                    <div class="my-5 rounded-xl border border-amber-200 bg-white/70 px-4 py-3 text-sm italic text-amber-900">
                      {{ $anchor }}
                    </div>
                  @endif

                  <div class="mt-5 space-y-3">
                    <div class="rounded-xl border border-amber-200 bg-white px-4 py-3">
                      <p class="text-xs font-semibold uppercase tracking-wide text-amber-800">Pergunta para meditar</p>
                      <p class="mt-2 text-sm leading-relaxed text-foreground">
                        {{ $pergunta ?: '—' }}
                      </p>
                    </div>

                    <div class="rounded-xl border border-border bg-muted/40 px-4 py-3">
                      <p class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Fruto espiritual</p>
                      <p class="mt-1 text-sm text-foreground">{{ $fruto ?: '—' }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <div class="mt-6 flex flex-wrap gap-2">
                <a href="{{ url('/santo-terco') }}"
                   class="inline-flex items-center justify-center rounded-xl bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500">
                  Rezar agora (mistério do dia)
                </a>
                <a href="#topo"
                   class="inline-flex items-center justify-center rounded-xl border border-border bg-card px-4 py-2 text-sm font-semibold text-foreground shadow-sm hover:bg-muted focus:outline-none focus:ring-2 focus:ring-amber-500">
                  Voltar ao topo
                </a>
              </div>
            </section>

            @if($idx === 2)
              {{-- Adsense in-article fluid --}}
              <div class="pt-1 rounded-2xl border border-border bg-card p-3 shadow-sm">
                <p class="px-1 pb-2 text-[11px] font-semibold text-muted-foreground">Publicidade</p>
                <div class="w-full overflow-hidden rounded-xl bg-muted/30 py-2">
                  <ins class="adsbygoogle"
                       style="display:block;text-align:center"
                       data-ad-layout="in-article"
                       data-ad-format="fluid"
                       data-ad-client="{{ $ADS_CLIENT }}"
                       data-ad-slot="{{ $ADS_SLOT_IN_ARTICLE }}"></ins>
                </div>
                @push('scripts')
                  <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
                @endpush
              </div>
            @endif
          </div>
        @endforeach
      </div>

      <div class="{{ $readingProse }} mt-12">
        <h2 id="como-rezar">Como rezar os Mistérios Gozosos (na prática)</h2>
        <p>
          Anuncie o mistério, reze um Pai-Nosso, dez Ave-Marias e o Glória. A cada dezena, peça a graça de viver a alegria cristã como confiança
          concreta: acolher Deus, servir com prontidão e perseverar nas buscas do coração.
        </p>

        <h3 id="como-meditar">Como meditar sem rezar no automático</h3>
        <ul>
          <li>Antes de cada dezena, fique 10–20 segundos em silêncio e “coloque a cena diante de Deus”.</li>
          <li>Escolha uma frase curta (ex.: “faça-se”, “servir”, “simplicidade”, “entrega”, “perseverança”) e retorne a ela quando se distrair.</li>
          <li>Finalize agradecendo por um sinal de graça no seu dia, mesmo pequeno.</li>
        </ul>
      </div>

      <div class="mt-12">
        <h2 class="text-2xl font-bold text-foreground" id="faq">Dúvidas frequentes</h2>

        <div class="mt-4 space-y-3">
          @foreach($faq as $qa)
            <details class="rounded-xl border border-amber-200 bg-card p-4">
              <summary class="cursor-pointer text-sm font-semibold text-foreground">{{ $qa['q'] }}</summary>
              <p class="mt-3 text-sm text-muted-foreground">{{ $qa['a'] }}</p>
            </details>
          @endforeach
        </div>
      </div>

      <div class="mt-12 rounded-2xl border border-border bg-muted/40 p-5">
        <p class="text-sm font-semibold text-foreground">Continuar no Santo Terço</p>
        <p class="mt-1 text-sm text-muted-foreground">Escolha também os outros tipos de mistério:</p>

        <div class="mt-4 grid gap-3 sm:grid-cols-2">
          <a href="{{ url('/santo-terco/misterios-dolorosos') }}"
             class="rounded-2xl border border-border bg-card p-4 text-sm shadow-sm transition hover:bg-muted focus:outline-none focus:ring-2 focus:ring-amber-500">
            <p class="font-semibold text-foreground">Mistérios Dolorosos</p>
            <p class="mt-1 text-xs text-muted-foreground">Terça e sexta • Paixão e entrega</p>
          </a>

          <a href="{{ url('/santo-terco/misterios-gloriosos') }}"
             class="rounded-2xl border border-border bg-card p-4 text-sm shadow-sm transition hover:bg-muted focus:outline-none focus:ring-2 focus:ring-amber-500">
            <p class="font-semibold text-foreground">Mistérios Gloriosos</p>
            <p class="mt-1 text-xs text-muted-foreground">Quarta e domingo • Ressurreição e esperança</p>
          </a>

          <a href="{{ url('/santo-terco/misterios-luminosos') }}"
             class="rounded-2xl border border-border bg-card p-4 text-sm shadow-sm transition hover:bg-muted focus:outline-none focus:ring-2 focus:ring-amber-500 sm:col-span-2">
            <p class="font-semibold text-foreground">Mistérios Luminosos</p>
            <p class="mt-1 text-xs text-muted-foreground">Quinta • Vida pública de Jesus</p>
          </a>
        </div>

        <div class="mt-4">
          <a href="{{ url('/santo-terco') }}"
             class="inline-flex items-center justify-center rounded-xl bg-amber-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500">
            Voltar para o Santo Terço (rezar guiado)
          </a>
        </div>
      </div>
    </section>

    {{-- MOBILE: Aside colapsável --}}
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
            'setKey' => $setKey,

            // ads
            'adsSlotDesktop300x250' => $ADS_SLOT_SIDEBAR_DESKTOP,
            'adsSlotDesktop' => $ADS_SLOT_SIDEBAR_DESKTOP,
            'adsSlotMobile' => $ADS_SLOT_SIDEBAR_MOBILE,

            // blog links
            'blogLinks' => $blogLinksAside,
          ])
        </div>
      </div>
    </div>

    {{-- DESKTOP --}}
    <aside class="rosary-aside-desktop hidden lg:block min-w-0">
      <div class="sticky top-20">
        @include('terco.partials.aside', [
          'lang' => 'pt',
          'variant' => 'desktop',
          'setKey' => $setKey,

          // ads
          'adsSlotDesktop300x250' => $ADS_SLOT_SIDEBAR_DESKTOP,
          'adsSlotDesktop' => $ADS_SLOT_SIDEBAR_DESKTOP,
          'adsSlotMobile' => $ADS_SLOT_SIDEBAR_MOBILE,

          // blog links
          'blogLinks' => $blogLinksAside,
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
