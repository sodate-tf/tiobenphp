@php
  $faqs = [
    [
      'q' => 'What is IA Tio Ben?',
      'a' => 'IA Tio Ben is a Catholic-focused AI experience built to support daily prayer: today’s Mass readings, clear Gospel takeaways, and practical answers grounded in Catholic teaching.'
    ],
    [
      'q' => 'How should I use the chat for better answers?',
      'a' => 'Ask focused questions and request a structure: (1) a short summary, (2) a practical takeaway for today, and (3) a closing prayer. This format tends to produce the most usable responses.'
    ],
    [
      'q' => 'Do you store my questions or personal information?',
      'a' => 'No. The conversation is cleared when you refresh or leave the page. If you want to keep something, copy only what you need (for example: a short reflection or prayer).'
    ],
    [
      'q' => 'Does this replace a priest or spiritual direction?',
      'a' => 'No. It’s a tool for study and prayer support. For confession, pastoral guidance, and spiritual direction, you should always speak with a priest or trusted parish leader.'
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
    Help Center
  </h2>

  <p class="text-center text-gray-700 mb-10 text-sm">
    Quick answers about the chat, daily readings, and how to use IA Tio Ben for everyday prayer.
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
    IA Tio Ben is designed to make Catholic prayer simpler: one place for the day’s readings, a clear Gospel
    takeaway, and structured answers you can actually use—before Mass, in a small group, or during personal reflection.
  </div>
</section>