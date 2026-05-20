{{-- resources/views/terco/misterios-luminosos.blade.php --}}
@extends('layouts.site')

@php
  $SITE_URL = 'https://www.iatioben.com.br';
  $PAGE_PATH = '/santo-terco/misterios-luminosos';
  $CANONICAL_URL = $SITE_URL.$PAGE_PATH;

  // AdSense
  $ADS_CLIENT = 'ca-pub-8819996017476509';
  $ADS_SLOT_BODY_TOP = '7474884427';
  $ADS_SLOT_IN_ARTICLE = '6161802751';
  $ADS_SLOT_SIDEBAR_DESKTOP = '8534838745';
  $ADS_SLOT_SIDEBAR_MOBILE = '1573844576';

  $today = now()->toDateString();

  // ✅ setKey usado pelo partial para set ativo
  $setKey = 'luminosos';

  $mysteries = [
    [
      'id' => 'batismo',
      'title' => '1º Mistério Luminoso',
      'subtitle' => 'O Batismo de Jesus no Jordão',
      'passageRef' => 'Mt 3,13–17',
      'passageQuote' => '“Este é o meu Filho amado, em quem me comprazo.”',
      'reflection' => [
        'No Jordão, Jesus desce às águas não por necessidade própria, mas para se unir a nós. Ele santifica o caminho e revela a identidade: Filho amado.',
        'Este mistério cura uma raiz profunda de ansiedade: viver tentando ‘provar valor’. Em Deus, a identidade vem antes da performance.',
        'O batismo é chamado e envio: ser filho e, por isso, viver como tal. A graça não é apenas consolo; é transformação.',
        'Rezar aqui é pedir que o coração volte ao essencial: ‘sou amado’ — e, a partir disso, escolher o bem com firmeza.',
      ],
      'points' => [
        'Pergunta: de onde eu tiro minha identidade hoje?',
        'Fruto: fidelidade ao batismo e vida de filho.',
      ],
    ],
    [
      'id' => 'cana',
      'title' => '2º Mistério Luminoso',
      'subtitle' => 'As Bodas de Caná',
      'passageRef' => 'Jo 2,1–11',
      'passageQuote' => '“Fazei tudo o que Ele vos disser.”',
      'reflection' => [
        'Caná mostra que Deus se importa com o cotidiano: com a alegria concreta, com o que falta na mesa, com as crises discretas.',
        'Maria percebe a necessidade antes de todos; e sua frase é um caminho espiritual inteiro: fazer o que Jesus diz — mesmo quando não entendemos tudo.',
        'O primeiro sinal de Jesus transforma água em vinho: Deus não ‘remenda’ apenas; Ele eleva. A graça não é mínimo; é abundância ordenada.',
        'Rezar Caná é confiar as faltas a Deus: quando o ‘vinho’ acabou em alguma área da vida, pedir a intervenção do Senhor e a docilidade para obedecer.',
      ],
      'points' => [
        'Pergunta: onde o ‘vinho’ acabou na minha vida?',
        'Fruto: confiança e obediência.',
      ],
    ],
    [
      'id' => 'reino',
      'title' => '3º Mistério Luminoso',
      'subtitle' => 'O Anúncio do Reino de Deus e o Convite à Conversão',
      'passageRef' => 'Mc 1,14–15',
      'passageQuote' => '“Convertei-vos e crede no Evangelho.”',
      'reflection' => [
        'Aqui o Evangelho é direto: Deus está perto. Conversão não é só ‘parar de errar’; é mudar de direção, reordenar o coração e escolher a verdade.',
        'O Reino não é um slogan: é a presença de Deus que transforma relações, escolhas, prioridades e hábitos.',
        'Crer no Evangelho é confiar que o bem é possível — e que a graça é mais forte do que nossas repetidas quedas.',
        'Rezar este mistério é fazer um exame simples: o que precisa mudar hoje, concretamente? Pequenas conversões sustentadas valem mais do que promessas grandes.',
      ],
      'points' => [
        'Pergunta: qual conversão concreta Deus me pede hoje?',
        'Fruto: conversão e amor à verdade.',
      ],
    ],
    [
      'id' => 'transfiguracao',
      'title' => '4º Mistério Luminoso',
      'subtitle' => 'A Transfiguração do Senhor',
      'passageRef' => 'Lc 9,28–36',
      'passageQuote' => '“Este é o meu Filho, o Eleito: escutai-o.”',
      'reflection' => [
        'A Transfiguração é uma luz dada antes da cruz. Deus fortalece os discípulos para não desistirem quando vier a noite.',
        'Cristo revela sua glória, mas também ensina: não se constrói morada em experiências espirituais. É preciso descer do monte e viver a fidelidade no vale.',
        'Escutar Jesus é mais do que ouvir: é tomar Sua palavra como critério. A oração verdadeira nos reorienta.',
        'Rezar este mistério é pedir luz para atravessar a noite: lembrança da presença de Deus quando a fé parece sem brilho.',
      ],
      'points' => [
        'Pergunta: qual palavra de Jesus eu preciso obedecer com mais decisão?',
        'Fruto: desejo de santidade e escuta.',
      ],
    ],
    [
      'id' => 'eucaristia',
      'title' => '5º Mistério Luminoso',
      'subtitle' => 'A Instituição da Eucaristia',
      'passageRef' => 'Lc 22,14–20 (cf. 1Cor 11,23–26)',
      'passageQuote' => '“Isto é o meu corpo, que é dado por vós.”',
      'reflection' => [
        'A Eucaristia é o coração do cristianismo: Deus se dá como alimento. Não é metáfora — é presença real e comunhão.',
        'Ao instituí-la, Jesus ensina o estilo do amor: ser pão partido, vida entregue, serviço concreto.',
        'Este mistério também nos cura do individualismo: a fé se torna corpo, comunidade, adoração e missão.',
        'Rezar aqui é desejar viver eucaristicamente: receber, agradecer, adorar e se doar. E pedir que cada comunhão forme em nós um coração mais semelhante ao de Cristo.',
      ],
      'points' => [
        'Pergunta: minha vida tem sido ‘pão repartido’ ou só autopreservação?',
        'Fruto: amor à Eucaristia e caridade.',
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
    ['q' => 'Em quais dias se rezam os Mistérios Luminosos?', 'a' => 'Tradicionalmente às quintas-feiras.'],
    ['q' => 'Quais são os cinco Mistérios Luminosos?', 'a' => 'Batismo no Jordão, Bodas de Caná, Anúncio do Reino, Transfiguração e Instituição da Eucaristia.'],
    ['q' => 'Como meditar os Mistérios Luminosos?', 'a' => 'Anuncie o mistério, faça um breve silêncio e reze cada Ave-Maria contemplando a luz de Cristo na vida pública: identidade, obediência, conversão, escuta e comunhão.'],
    ['q' => 'Posso rezar os Luminosos em outro dia?', 'a' => 'Sim. A divisão por dias é uma tradição devocional.'],
  ];

  $articleSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $CANONICAL_URL],
    'headline' => 'Mistérios Luminosos do Terço: passagens bíblicas e reflexões',
    'description' => 'Guia completo dos Mistérios Luminosos do Terço com referências bíblicas, reflexões e orientações para rezar com sentido.',
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
      ['@type' => 'Thing', 'name' => 'Mistérios Luminosos'],
      ['@type' => 'Thing', 'name' => 'Vida pública de Jesus'],
      ['@type' => 'Thing', 'name' => 'Terço'],
    ],
    'keywords' => ['mistérios luminosos', 'terço quinta', 'mistérios da luz', 'como meditar o terço', 'eucaristia'],
  ];

  $breadcrumbSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
      ['@type' => 'ListItem', 'position' => 1, 'name' => 'Início', 'item' => $SITE_URL.'/'],
      ['@type' => 'ListItem', 'position' => 2, 'name' => 'Santo Terço', 'item' => $SITE_URL.'/santo-terco'],
      ['@type' => 'ListItem', 'position' => 3, 'name' => 'Mistérios Luminosos', 'item' => $CANONICAL_URL],
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
    'name' => 'Mistérios Luminosos do Terço',
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
    'name' => 'Mistérios Luminosos do Terço: passagens bíblicas e reflexões',
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
    ['title' => 'Identidade de filho: como curar a ansiedade (Batismo)', 'slug' => 'identidade-de-filho-e-ansiedade'],
    ['title' => 'Caná: quando o vinho acaba, o que fazer?', 'slug' => 'quando-o-vinho-acaba-cana'],
    ['title' => 'Conversão concreta: como mudar de vida aos poucos', 'slug' => 'conversao-concreta-aos-poucos'],
    ['title' => 'Transfiguração: luz para atravessar a noite', 'slug' => 'transfiguracao-luz-na-noite'],
    ['title' => 'Eucaristia: por que é o coração da fé?', 'slug' => 'eucaristia-coracao-da-fe'],
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
@section('title', 'Santo Terço: Mistérios Luminosos — passagens bíblicas e reflexões')
@section('meta_description', 'Reze e medite os Mistérios Luminosos (quinta-feira) com passagens bíblicas e reflexões para contemplar a vida pública de Jesus e sua luz.')
@section('canonical', $CANONICAL_URL)
@section('robots', 'index,follow')

@section('hreflang')
  <link rel="alternate" hreflang="pt-BR" href="{{ $CANONICAL_URL }}"/>
  <link rel="alternate" hreflang="en" href="{{ $SITE_URL }}/en/rosary/luminous-mysteries"/>
  <link rel="alternate" hreflang="x-default" href="{{ $CANONICAL_URL }}"/>
@endsection

@section('og_title', 'Santo Terço: Mistérios Luminosos')
@section('og_description', 'Quinta-feira: contemple a luz de Cristo. Clique e reze com Bíblia e meditações em cada dezena.')
@section('og_url', $CANONICAL_URL)
@section('og_image', $SITE_URL.'/og/terco/misterios-luminosos.png?v=1')

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
        Quinta-feira
      </span>
    </div>

    <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl">Mistérios Luminosos do Terço</h1>

    <p class="text-base leading-relaxed text-muted-foreground sm:text-lg">
      Os Mistérios Luminosos (Mistérios da Luz) contemplam a vida pública de Jesus: identidade, sinais, conversão, escuta e Eucaristia.
      Aqui você encontra <strong>passagens bíblicas</strong> e reflexões para meditar com clareza.
    </p>

    <section class="rounded-2xl border border-amber-200 bg-amber-50 p-4 shadow-sm sm:p-5" aria-label="Resposta rápida">
      <p class="text-xs font-semibold text-amber-900">Resposta rápida</p>
      <div class="mt-2 space-y-2 text-sm leading-relaxed text-foreground">
        <p>
          Os <strong>Mistérios Luminosos</strong> são rezados tradicionalmente às <strong>quintas-feiras</strong>.
          Eles ajudam a contemplar a luz de Cristo na vida cotidiana: filiação, obediência, conversão, escuta e comunhão.
        </p>
        <p class="text-muted-foreground">
          Dica prática: antes de cada dezena, escolha uma palavra-guia (filiação, confiança, conversão, escuta, comunhão).
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
      <nav aria-label="Sumário dos Mistérios Luminosos" class="rounded-2xl border border-border bg-muted/40 p-4">
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
        <h2 id="o-que-sao">O que são os Mistérios Luminosos?</h2>
        <p>
          Eles contemplam a luz de Cristo na vida pública: o Pai revela o Filho, Jesus realiza sinais, chama à conversão,
          transfigura o coração pela escuta e se entrega como alimento na Eucaristia.
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
                      <p class="mt-2 text-sm leading-relaxed text-foreground">{{ $pergunta ?: '—' }}</p>
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
        <h2 id="como-rezar">Como rezar os Mistérios Luminosos (na prática)</h2>
        <p>
          Anuncie o mistério, reze um Pai-Nosso, dez Ave-Marias e o Glória. Para os Luminosos, ajude-se com palavras-guia:
          filiação, obediência, conversão, escuta e comunhão.
        </p>

        <h3 id="como-meditar">Como meditar com clareza e decisão</h3>
        <ul>
          <li>Antes de cada dezena, peça um fruto concreto (ex.: “Senhor, dá-me docilidade”).</li>
          <li>Durante as Ave-Marias, volte à palavra-guia quando a mente dispersar.</li>
          <li>Ao final, escolha uma pequena ação prática de conversão para o dia.</li>
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

          <a href="{{ url('/santo-terco/misterios-gloriosos') }}"
             class="rounded-2xl border border-border bg-card p-4 text-sm shadow-sm transition hover:bg-muted focus:outline-none focus:ring-2 focus:ring-amber-500 sm:col-span-2">
            <p class="font-semibold text-foreground">Mistérios Gloriosos</p>
            <p class="mt-1 text-xs text-muted-foreground">Quarta e domingo • Ressurreição e esperança</p>
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
