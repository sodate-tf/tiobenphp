{{-- resources/views/terco/gloriosos.blade.php --}}
@extends('layouts.site')

@php
  $SITE_URL = 'https://www.iatioben.com.br';
  $PAGE_PATH = '/santo-terco/misterios-gloriosos';
  $CANONICAL_URL = $SITE_URL.$PAGE_PATH;

  $ADS_CLIENT = 'ca-pub-8819996017476509';
  $ADS_SLOT_BODY_TOP = '7474884427';
  $ADS_SLOT_IN_ARTICLE = '6161802751';
  $ADS_SLOT_SIDEBAR_DESKTOP = '8534838745';
  $ADS_SLOT_SIDEBAR_MOBILE = '1573844576';

  $today = now()->toDateString();

  // ✅ setKey usado pelo partial para set ativo
  $setKey = 'gloriosos';

  $mysteries = [
    [
      'id' => 'ressurreicao',
      'title' => '1º Mistério Glorioso',
      'subtitle' => 'A Ressurreição de Jesus',
      'passageRef' => 'Lc 24,1–12 (cf. Jo 20,1–18)',
      'passageQuote' => '“Por que procurais entre os mortos aquele que está vivo?”',
      'reflection' => [
        "A Ressurreição não é um ‘final feliz’ apenas: é o começo de uma nova criação. Deus não devolve Jesus ao passado; Ele inaugura um futuro.",
        "O túmulo vazio ensina que a esperança cristã não é otimismo: é certeza fundada na ação de Deus, mesmo quando a realidade parece fechada.",
        "Ressuscitar, para nós, começa por dentro: quando a graça reabre portas que a tristeza fechou, quando o perdão interrompe ciclos, quando o coração volta a crer.",
        "Rezar este mistério é pedir que Deus ressuscite em nós aquilo que morreu por desânimo, medo ou pecado. O Senhor não apenas consola: Ele recria.",
      ],
      'points' => [
        'Pergunta: onde eu preciso voltar a esperar?',
        'Fruto: fé viva e alegria espiritual.',
      ],
    ],
    [
      'id' => 'ascensao',
      'title' => '2º Mistério Glorioso',
      'subtitle' => 'A Ascensão do Senhor',
      'passageRef' => 'At 1,6–11',
      'passageQuote' => '“Este Jesus… virá do mesmo modo como o vistes partir.”',
      'reflection' => [
        "A Ascensão não é ausência: é presença de outro modo. Jesus sobe para o Pai e, ao mesmo tempo, abre para nós o caminho do céu.",
        "Os discípulos são chamados a não ficar olhando para cima indefinidamente. A fé não é fuga do mundo, mas missão no mundo — com o coração voltado para Deus.",
        "A Ascensão cura um vício espiritual comum: querer segurar Deus do nosso jeito. Cristo nos educa a confiar, caminhar e testemunhar.",
        "Rezar este mistério é alinhar prioridades: viver com os pés no chão e o coração no alto — sem perder a esperança do encontro definitivo.",
      ],
      'points' => [
        'Pergunta: o que me distrai da missão concreta de hoje?',
        'Fruto: esperança e desejo do céu.',
      ],
    ],
    [
      'id' => 'pentecostes',
      'title' => '3º Mistério Glorioso',
      'subtitle' => 'A Vinda do Espírito Santo',
      'passageRef' => 'At 2,1–13',
      'passageQuote' => '“Todos ficaram cheios do Espírito Santo.”',
      'reflection' => [
        "Pentecostes é a cura do medo. O Espírito não muda apenas circunstâncias; Ele muda pessoas: dá coragem, clareza, constância e fogo interior.",
        "O mesmo grupo que antes se escondia agora anuncia. A fé não é temperamento; é graça recebida e correspondida.",
        "O Espírito também unifica: onde há Babel, Ele cria comunhão. Onde há confusão, Ele dá discernimento.",
        "Rezar este mistério é pedir dons concretos: sabedoria para decidir, fortaleza para perseverar, caridade para amar, e humildade para obedecer a Deus.",
      ],
      'points' => [
        'Pergunta: qual dom eu preciso pedir hoje com insistência?',
        'Fruto: zelo apostólico e coragem.',
      ],
    ],
    [
      'id' => 'assuncao',
      'title' => '4º Mistério Glorioso',
      'subtitle' => 'A Assunção de Maria',
      'passageRef' => 'Ap 12,1 (tradição da Igreja)',
      'passageQuote' => '“Apareceu no céu um grande sinal: uma Mulher revestida de sol.”',
      'reflection' => [
        "A Assunção revela o destino prometido: Deus não salva ‘pela metade’. Ele quer o ser humano inteiro, e a vida inteira transfigurada.",
        "Em Maria, a Igreja contempla aquilo que espera para si: não a fuga do corpo, mas a vitória da graça sobre a morte.",
        "Este mistério alimenta uma esperança concreta: a história não termina em desgaste. Em Deus, a fidelidade tem futuro.",
        "Rezar a Assunção é aprender a olhar a vida com horizonte eterno — e a viver o presente com mais pureza, coragem e confiança.",
      ],
      'points' => [
        'Pergunta: estou vivendo com horizonte eterno ou só reagindo ao imediato?',
        'Fruto: esperança e pureza.',
      ],
    ],
    [
      'id' => 'coroacao',
      'title' => '5º Mistério Glorioso',
      'subtitle' => 'A Coroação de Maria no Céu',
      'passageRef' => 'Ap 12,1 (símbolo) / tradição',
      'passageQuote' => '“Uma Mulher… e uma coroa de doze estrelas.”',
      'reflection' => [
        "A Coroação de Maria não é ‘competição’ de grandezas: é a exaltação da humildade. Deus coroa quem aprende a servir.",
        "Maria é imagem da Igreja glorificada: aquilo que Deus começou na fé, Ele leva à plenitude. A história da salvação tem coroamento.",
        "Este mistério educa nosso desejo: não buscar glória humana, mas a glória de Deus — que é amar e permanecer fiel.",
        "Rezar este mistério é pedir que nossa vida termine bem: com fidelidade, perseverança e amor até o fim.",
      ],
      'points' => [
        "Pergunta: que ‘glória’ eu tenho buscado — e o que Deus quer purificar em mim?",
        'Fruto: confiança filial e perseverança.',
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
    ['q' => 'Em quais dias se rezam os Mistérios Gloriosos?', 'a' => 'Tradicionalmente às quartas-feiras e aos domingos.'],
    ['q' => 'Quais são os cinco Mistérios Gloriosos?', 'a' => 'Ressurreição, Ascensão, Pentecostes, Assunção de Maria e Coroação de Maria.'],
    ['q' => 'Como meditar os Mistérios Gloriosos?', 'a' => 'Anuncie o mistério, faça um breve silêncio e reze cada Ave-Maria como um ato de esperança. Contemple a vitória de Deus e peça a graça de viver com horizonte eterno.'],
    ['q' => 'Posso rezar em outro dia?', 'a' => 'Sim. A divisão por dias é uma tradição devocional.'],
  ];

  $articleSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $CANONICAL_URL],
    'headline' => 'Mistérios Gloriosos do Terço: passagens bíblicas e reflexões',
    'description' => 'Guia completo dos Mistérios Gloriosos do Terço com referências bíblicas, reflexões e orientações para rezar com sentido.',
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
      ['@type' => 'Thing', 'name' => 'Mistérios Gloriosos'],
      ['@type' => 'Thing', 'name' => 'Ressurreição'],
      ['@type' => 'Thing', 'name' => 'Pentecostes'],
      ['@type' => 'Thing', 'name' => 'Terço'],
    ],
    'keywords' => [
      'mistérios gloriosos',
      'terço quarta e domingo',
      'ressurreição',
      'pentecostes',
      'como meditar o terço',
    ],
  ];

  $breadcrumbSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
      ['@type' => 'ListItem', 'position' => 1, 'name' => 'Início', 'item' => $SITE_URL.'/'],
      ['@type' => 'ListItem', 'position' => 2, 'name' => 'Santo Terço', 'item' => $SITE_URL.'/santo-terco'],
      ['@type' => 'ListItem', 'position' => 3, 'name' => 'Mistérios Gloriosos', 'item' => $CANONICAL_URL],
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
    'name' => 'Mistérios Gloriosos do Terço',
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
    'name' => 'Mistérios Gloriosos do Terço: passagens bíblicas e reflexões',
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
    ['title' => "O que significa ‘ressuscitar’ por dentro?", 'slug' => 'ressuscitar-por-dentro'],
    ['title' => "Ascensão: Deus ausente ou presente de outro modo?", 'slug' => 'ascensao-presenca-de-outro-modo'],
    ['title' => "Dons do Espírito Santo: como pedir e viver", 'slug' => 'dons-do-espirito-santo-como-pedir'],
    ['title' => "Assunção de Maria: o que a Igreja ensina?", 'slug' => 'assuncao-de-maria-explicacao'],
    ['title' => "Coroação de Maria: sentido e espiritualidade", 'slug' => 'coroacao-de-maria-sentido'],
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
@section('title', 'Santo Terço: Mistérios Gloriosos — passagens bíblicas e reflexões')
@section('meta_description', 'Reze e medite os Mistérios Gloriosos (quarta e domingo) com passagens bíblicas e reflexões para contemplar a Ressurreição e a glória do Céu.')
@section('canonical', $CANONICAL_URL)
@section('robots', 'index,follow')

@section('hreflang')
  <link rel="alternate" hreflang="pt-BR" href="{{ $CANONICAL_URL }}"/>
  <link rel="alternate" hreflang="en" href="{{ $SITE_URL }}/en/rosary/glorious-mysteries"/>
  <link rel="alternate" hreflang="x-default" href="{{ $CANONICAL_URL }}"/>
@endsection

@section('og_title', 'Santo Terço: Mistérios Gloriosos')
@section('og_description', 'Quarta e domingo: contemple a Ressurreição e a esperança cristã. Clique e reze com Bíblia e meditações em cada dezena.')
@section('og_url', $CANONICAL_URL)
@section('og_image', $SITE_URL.'/og/terco/misterios-gloriosos.png?v=1')

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
        Quarta e Domingo
      </span>
    </div>

    <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl">
      Mistérios Gloriosos do Terço
    </h1>

    <p class="text-base leading-relaxed text-muted-foreground sm:text-lg">
      Os Mistérios Gloriosos contemplam a vitória de Deus: Ressurreição, Ascensão, Pentecostes, Assunção e Coroação de Maria.
      Aqui você encontra <strong>passagens bíblicas</strong> e reflexões para rezar com esperança.
    </p>

    <section class="rounded-2xl border border-amber-200 bg-amber-50 p-4 shadow-sm sm:p-5" aria-label="Resposta rápida">
      <p class="text-xs font-semibold text-amber-900">Resposta rápida</p>
      <div class="mt-2 space-y-2 text-sm leading-relaxed text-foreground">
        <p>
          Os <strong>Mistérios Gloriosos</strong> são rezados tradicionalmente às <strong>quartas</strong> e aos <strong>domingos</strong>.
          Eles fortalecem a esperança e lembram que a história termina em Deus.
        </p>
        <p class="text-muted-foreground">
          Dica prática: reze cada dezena como um ato de esperança concreta — “Senhor, faze nova todas as coisas”.
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
      <nav aria-label="Sumário dos Mistérios Gloriosos" class="rounded-2xl border border-border bg-muted/40 p-4">
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
        <h2 id="o-que-sao">O que são os Mistérios Gloriosos?</h2>
        <p>
          Eles nos fazem contemplar que a última palavra não é o pecado nem a morte, mas a vida em Deus. A Ressurreição abre um futuro; a Ascensão
          orienta a missão; Pentecostes acende o coração; e em Maria vemos o destino prometido.
        </p>
        <p>
          Rezar estes mistérios educa a esperança: Deus age na história e, ao mesmo tempo, trabalha silenciosamente dentro de nós.
          O que parece fechado pode ser reaberto pela graça.
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
        <h2 id="como-rezar">Como rezar os Mistérios Gloriosos (na prática)</h2>
        <p>
          Anuncie o mistério, reze um Pai-Nosso, dez Ave-Marias e finalize com o Glória. Ao rezar, peça a graça de viver com esperança concreta:
          a vitória de Deus não é teoria; ela se traduz em perseverança e caridade.
        </p>

        <h3 id="como-meditar">Como meditar com esperança</h3>
        <ul>
          <li>Antes de cada dezena, repita interiormente: “Senhor, renova minha esperança”.</li>
          <li>Reze cada Ave-Maria ligando a cena ao seu dia: a Ressurreição para recomeçar, Pentecostes para ter coragem, etc.</li>
          <li>Ao final, agradeça por um sinal de vida que Deus já te deu (mesmo pequeno).</li>
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
          <a href="{{ url('/santo-terco/misterios-gozosos') }}"
             class="rounded-2xl border border-border bg-card p-4 text-sm shadow-sm transition hover:bg-muted focus:outline-none focus:ring-2 focus:ring-amber-500">
            <p class="font-semibold text-foreground">Mistérios Gozosos</p>
            <p class="mt-1 text-xs text-muted-foreground">Segunda e sábado • Encarnação e alegria</p>
          </a>

          <a href="{{ url('/santo-terco/misterios-dolorosos') }}"
             class="rounded-2xl border border-border bg-card p-4 text-sm shadow-sm transition hover:bg-muted focus:outline-none focus:ring-2 focus:ring-amber-500">
            <p class="font-semibold text-foreground">Mistérios Dolorosos</p>
            <p class="mt-1 text-xs text-muted-foreground">Terça e sexta • Paixão e entrega</p>
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
