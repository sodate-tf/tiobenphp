{{-- resources/views/terco/misterios-dolorosos.blade.php --}}
@extends('layouts.site')

@php
  $SITE_URL = 'https://www.iatioben.com.br';
  $PAGE_PATH = '/santo-terco/misterios-dolorosos';
  $CANONICAL_URL = $SITE_URL.$PAGE_PATH;

  $ADS_CLIENT = 'ca-pub-8819996017476509';
  $ADS_SLOT_BODY_TOP = '7474884427';
  $ADS_SLOT_IN_ARTICLE = '6161802751';
  $ADS_SLOT_SIDEBAR_DESKTOP = '8534838745';
  $ADS_SLOT_SIDEBAR_MOBILE = '1573844576';

  $today = now()->toDateString();

  // ✅ setKey usado pelo partial para set ativo
  $setKey = 'dolorosos';

  $mysteries = [
    [
      'id' => 'agonia',
      'title' => '1º Mistério Doloroso',
      'subtitle' => 'A Agonia de Jesus no Horto',
      'passageRef' => 'Mt 26,36–46 (cf. Lc 22,39–46)',
      'passageQuote' => '“Meu Pai, se é possível, afasta de mim este cálice… contudo, não como eu quero, mas como tu queres.”',
      'reflection' => [
        'Este mistério nos coloca diante de um ponto decisivo: quando a vontade de Deus e a nossa vontade parecem se chocar. Jesus não finge serenidade; Ele reza a partir da dor real.',
        'A agonia do Horto revela que a oração não elimina a luta interior — ela transforma a luta em entrega. Em vez de fugir, Jesus permanece; em vez de endurecer, Ele se abre ao Pai.',
        'Há momentos em que o coração quer atalhos: anestesiar a angústia, evitar o confronto, adiar decisões. Aqui aprendemos a fazer o contrário: nomear o medo diante de Deus e escolher a fidelidade.',
        'Rezar este mistério é apresentar ao Senhor o seu ‘cálice’: aquilo que você não controla, o que pesa, o que exige coragem. E pedir a graça de atravessar sem se perder de Deus.',
      ],
      'points' => [
        'Pergunta para meditar: qual é o ‘cálice’ que eu tenho evitado encarar com Deus?',
        'Fruto espiritual tradicional: conformidade com a vontade de Deus e vigilância.',
      ],
    ],
    [
      'id' => 'flagelacao',
      'title' => '2º Mistério Doloroso',
      'subtitle' => 'A Flagelação de Jesus',
      'passageRef' => 'Jo 19,1 (cf. Is 53,5)',
      'passageQuote' => '“Então Pilatos mandou flagelar Jesus.”',
      'reflection' => [
        'A Flagelação nos confronta com a injustiça e com a violência que nasce do medo e da covardia. Jesus sofre sem revidar, mostrando que a força do amor não depende de agressividade.',
        'Contemplar este mistério é reconhecer que o mal quer nos desfigurar — e que Cristo assume essa desfiguração para nos devolver dignidade.',
        'Também é um convite a rever o que nos ‘flagela’ por dentro: culpas antigas, vícios, impulsos, palavras duras, hábitos que machucam. Deus não nos humilha; Ele cura.',
        'Rezar aqui é pedir purificação: que o Senhor desfaça em nós aquilo que fere, e nos eduque para a mansidão firme, capaz de resistir sem se tornar cruel.',
      ],
      'points' => [
        'Pergunta para meditar: que feridas eu preciso entregar para que Deus cure com verdade?',
        'Fruto espiritual tradicional: pureza e mortificação dos sentidos.',
      ],
    ],
    [
      'id' => 'espinhos',
      'title' => '3º Mistério Doloroso',
      'subtitle' => 'A Coroação de Espinhos',
      'passageRef' => 'Mt 27,27–31',
      'passageQuote' => '“Puseram-lhe uma coroa de espinhos… e zombavam dele.”',
      'reflection' => [
        'A dor aqui não é só física: é a humilhação. É o ataque à identidade. O mundo tenta ridicularizar a verdade, diminuir a santidade, caricaturar a fé.',
        'Jesus aceita ser coroado ‘às avessas’ para ensinar que a realeza de Deus não se sustenta em aplausos. A dignidade de Cristo não depende da opinião dos outros.',
        'Este mistério toca também nossas inseguranças: a necessidade de aceitação, o medo de parecer fraco, o desejo de controlar a imagem. A santidade amadurece quando deixamos de viver reféns de aprovação.',
        'Rezar a Coroação é pedir liberdade interior: que nenhuma zombaria — externa ou interna — roube o que Deus diz sobre você.',
      ],
      'points' => [
        'Pergunta para meditar: onde eu busco validação a ponto de perder a verdade do Evangelho?',
        'Fruto espiritual tradicional: coragem e desprezo das vaidades.',
      ],
    ],
    [
      'id' => 'cruz',
      'title' => '4º Mistério Doloroso',
      'subtitle' => 'Jesus Carrega a Cruz',
      'passageRef' => 'Lc 23,26–32',
      'passageQuote' => '“Se alguém quer vir após mim, renuncie a si mesmo, tome a sua cruz e siga-me.”',
      'reflection' => [
        'Carregar a cruz não é romantizar sofrimento. É aprender que o amor fiel tem peso — e que o peso, quando assumido com Cristo, ganha sentido.',
        'Jesus cai, levanta, continua. O Evangelho não esconde a fraqueza; ele mostra que a perseverança é mais forte do que a perfeição.',
        'Simão de Cirene aparece como graça inesperada: Deus permite ajudas concretas. A cruz não foi feita para ser carregada com orgulho solitário.',
        'Rezar este mistério é perguntar: qual fidelidade eu preciso sustentar hoje? E também: quem eu posso ajudar a carregar um pouco do peso?',
      ],
      'points' => [
        'Pergunta para meditar: qual ‘cruz’ hoje pode ser vivida como amor — e não como revolta?',
        'Fruto espiritual tradicional: paciência nas provações.',
      ],
    ],
    [
      'id' => 'crucifixao',
      'title' => '5º Mistério Doloroso',
      'subtitle' => 'A Crucifixão e Morte de Jesus',
      'passageRef' => 'Jo 19,17–30 (cf. Lc 23,33–46)',
      'passageQuote' => '“Tudo está consumado.”',
      'reflection' => [
        'A cruz é o lugar onde o amor vai até o fim. Jesus não apenas sofre: Ele se entrega. Ele transforma a violência recebida em oferta, e abre um caminho de reconciliação.',
        '‘Tudo está consumado’ não é derrota; é plenitude. É a obra do amor levada à totalidade — mesmo quando, por fora, parece fracasso.',
        'Contemplar a Crucifixão é aprender a medir a vida por outro critério: não pelo controle, nem pelo sucesso imediato, mas pela fidelidade ao bem.',
        'Rezar este mistério é deixar a cruz iluminar suas perdas e dores: Deus não desperdiça lágrimas quando elas são colocadas em Suas mãos. A cruz é passagem.',
      ],
      'points' => [
        'Pergunta para meditar: o que eu preciso entregar a Deus para que Ele transforme em vida?',
        'Fruto espiritual tradicional: amor a Jesus e espírito de sacrifício.',
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
    ['q' => 'Em quais dias se rezam os Mistérios Dolorosos?', 'a' => 'Tradicionalmente, os Mistérios Dolorosos são rezados às terças-feiras e às sextas-feiras.'],
    ['q' => 'Quais são os cinco Mistérios Dolorosos?', 'a' => 'Agonia no Horto, Flagelação, Coroação de Espinhos, Jesus Carrega a Cruz, Crucifixão e Morte.'],
    ['q' => 'Como meditar os Mistérios Dolorosos sem desanimar?', 'a' => 'Medite olhando para o amor fiel de Cristo: anuncie o mistério, faça um breve silêncio e reze cada Ave-Maria como um ato de confiança e entrega.'],
    ['q' => 'Posso rezar os Mistérios Dolorosos em outro dia?', 'a' => 'Sim. A divisão por dias é uma tradição devocional; você pode rezar conforme sua necessidade espiritual.'],
  ];

  $articleSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $CANONICAL_URL],
    'headline' => 'Mistérios Dolorosos do Terço: passagens bíblicas e reflexões',
    'description' => 'Guia completo dos Mistérios Dolorosos do Terço com referências bíblicas, reflexões para meditação e orientações práticas para rezar com sentido.',
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
      ['@type' => 'Thing', 'name' => 'Mistérios Dolorosos'],
      ['@type' => 'Thing', 'name' => 'Paixão de Cristo'],
      ['@type' => 'Thing', 'name' => 'Terço'],
    ],
    'keywords' => [
      'mistérios dolorosos',
      'terço terça e sexta',
      'paixão de Cristo',
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
      ['@type' => 'ListItem', 'position' => 3, 'name' => 'Mistérios Dolorosos', 'item' => $CANONICAL_URL],
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
    'name' => 'Mistérios Dolorosos do Terço',
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
    'name' => 'Mistérios Dolorosos do Terço: passagens bíblicas e reflexões',
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

  // links relacionados (seu partial pode renderizar)
  $blogLinks = [
    ['title' => 'Como lidar com a angústia na oração (Horto)', 'slug' => 'como-lidar-com-a-angustia-na-oracao'],
    ['title' => 'Feridas e cura interior: o que a Flagelação ensina', 'slug' => 'flagelacao-e-cura-interior'],
    ['title' => 'Liberdade interior contra a vaidade (espinhos)', 'slug' => 'coroacao-de-espinhos-e-vaidade'],
    ['title' => 'Como carregar a cruz sem endurecer', 'slug' => 'como-carregar-a-cruz-sem-endurecer'],
    ['title' => 'O sentido do “tudo está consumado”', 'slug' => 'tudo-esta-consumado-significado'],
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
@section('title', 'Santo Terço: Mistérios Dolorosos — passagens bíblicas e reflexões')
@section('meta_description', 'Reze e medite os Mistérios Dolorosos (terça e sexta) com passagens bíblicas e reflexões para viver a Paixão de Cristo com sentido.')
@section('canonical', $CANONICAL_URL)
@section('robots', 'index,follow')

@section('hreflang')
  <link rel="alternate" hreflang="pt-BR" href="{{ $CANONICAL_URL }}"/>
  <link rel="alternate" hreflang="en" href="{{ $SITE_URL }}/en/holy-rosary/sorrowful-mysteries"/>
  <link rel="alternate" hreflang="x-default" href="{{ $CANONICAL_URL }}"/>
@endsection

@section('og_title', 'Santo Terço: Mistérios Dolorosos')
@section('og_description', 'Terça e sexta: aprofunde-se na Paixão de Cristo. Clique e reze com Bíblia e meditações em cada dezena.')
@section('og_url', $CANONICAL_URL)
@section('og_image', $SITE_URL.'/og/terco/misterios-dolorosos.png?v=1')

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
        Terça e Sexta
      </span>
    </div>

    <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl">
      Mistérios Dolorosos do Terço
    </h1>

    <p class="text-base leading-relaxed text-muted-foreground sm:text-lg">
      Os Mistérios Dolorosos contemplam a Paixão do Senhor. Aqui você encontra as
      <strong>passagens bíblicas</strong> e reflexões para meditar cada dezena com profundidade e esperança.
    </p>

    <section class="rounded-2xl border border-amber-200 bg-amber-50 p-4 shadow-sm sm:p-5" aria-label="Resposta rápida">
      <p class="text-xs font-semibold text-amber-900">Resposta rápida</p>
      <div class="mt-2 space-y-2 text-sm leading-relaxed text-foreground">
        <p>
          Os <strong>Mistérios Dolorosos</strong> são rezados tradicionalmente às
          <strong>terças</strong> e <strong>sextas</strong>. Eles ajudam a rezar a cruz sem desespero: contemplando o amor fiel de Cristo.
        </p>
        <p class="text-muted-foreground">
          Dica prática: anuncie o mistério e reze cada Ave-Maria como um ato de confiança e entrega.
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
      <nav aria-label="Sumário dos Mistérios Dolorosos" class="rounded-2xl border border-border bg-muted/40 p-4">
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
        <h2 id="o-que-sao">O que são os Mistérios Dolorosos?</h2>
        <p>
          Eles nos colocam diante do caminho da cruz: não como narrativa distante, mas como escola do amor fiel. Ao contemplar a Paixão,
          aprendemos que Deus não abandona a humanidade na dor — Ele a atravessa por dentro.
        </p>
        <p>
          Rezar esses mistérios educa o coração para a compaixão: olhar o sofrimento sem endurecer, e responder com fidelidade e caridade.
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
        <h2 id="como-rezar">Como rezar os Mistérios Dolorosos (na prática)</h2>
        <p>
          Anuncie o mistério, reze um Pai-Nosso, dez Ave-Marias e finalize com o Glória. Ao contemplar a Paixão, peça a graça de unir
          suas dores e provações ao amor fiel de Cristo.
        </p>

        <h3 id="como-meditar">Como meditar sem perder a esperança</h3>
        <ul>
          <li>Faça um breve silêncio antes de cada dezena: “Senhor, ensina-me a amar como Tu amas”.</li>
          <li>Reze as Ave-Marias com uma intenção concreta (uma pessoa que sofre, uma família, uma situação difícil).</li>
          <li>Conclua cada dezena com um ato de confiança: “Jesus, eu confio em vós”.</li>
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

    {{-- MOBILE: Aside colapsável (mesmo padrão do EN corrigido) --}}
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
