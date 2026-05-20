@php
  $faqs = [
    [
      'q' => 'O que é a IA Tio Ben?',
      'a' => 'A IA Tio Ben é uma inteligência artificial católica criada para ajudar fiéis a compreender a Liturgia Diária, o Evangelho do Dia, a Bíblia e os ensinamentos da Igreja de forma acessível e coerente com a fé católica.'
    ],
    [
      'q' => 'Como funciona a conversa com o Tio Ben?',
      'a' => 'Você digita sua pergunta e a resposta aparece na conversa. Para melhores resultados, faça perguntas específicas e peça resumos, aplicações práticas e uma oração final.'
    ],
    [
      'q' => 'Minhas perguntas ficam salvas?',
      'a' => 'Não. A conversa é limpa ao sair ou atualizar a página. Se quiser guardar algo, copie apenas o trecho necessário.'
    ],
    [
      'q' => 'O Tio Ben substitui um padre ou diretor espiritual?',
      'a' => 'Não. É uma ferramenta de apoio à oração e estudo. Para sacramentos, confissão e direção espiritual, procure sempre um sacerdote.'
    ],
  ];

  $faqSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => array_map(fn($i) => [
      '@type' => 'Question',
      'name' => $i['q'],
      'acceptedAnswer' => [
        '@type' => 'Answer',
        'text' => $i['a'],
      ],
    ], $faqs),
  ];
@endphp

<section class="max-w-5xl mx-auto px-4 py-14 text-left">
  <script type="application/ld+json">{!! json_encode($faqSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>

  <h2 class="text-3xl font-bold text-center text-amber-900 mb-3">
    Central de Ajuda da IA Tio Ben
  </h2>

  <p class="text-center text-gray-700 mb-10 text-sm">
    Tire suas dúvidas sobre como usar o Tio Ben, a Liturgia Diária e a proposta do projeto.
  </p>

  <div class="flex flex-col gap-4" id="faqBox">
    @foreach($faqs as $idx => $item)
      <div class="bg-white rounded-xl shadow-md border border-amber-200 overflow-hidden">
        <button type="button"
                class="w-full text-left px-5 py-4 flex justify-between items-center font-semibold text-amber-900 hover:bg-amber-50 transition"
                data-faq="{{ $idx }}"
                aria-expanded="false">
          <span class="pr-4">{{ $item['q'] }}</span>
          <span class="text-xl" aria-hidden="true">+</span>
        </button>
        <div class="hidden px-5 pb-5 pt-2 text-gray-700 text-sm leading-relaxed bg-amber-50" data-faq-panel="{{ $idx }}">
          <p>{{ $item['a'] }}</p>
        </div>
      </div>
    @endforeach
  </div>

  <div class="mt-10 bg-amber-100 border border-amber-200 rounded-xl p-6 text-sm text-gray-800 leading-relaxed">
    A IA Tio Ben é um projeto de evangelização digital que une tecnologia e fé.
    Aqui você acompanha a Liturgia Diária, aprofunda o Evangelho do Dia e fortalece sua vida espiritual
    com um conteúdo organizado e coerente com a fé católica.
  </div>
</section>