@php
  use App\Support\LiturgiaEnGlossary;

  $lang = $lang ?? 'pt';
  $kind = $kind ?? 'reading'; // reading | gospel

  $label = $label ?? ($kind === 'gospel' ? 'Gospel' : 'Reading');
  $ref = $ref ?? null;

  $subtitle = $subtitle ?? null;
  if ($lang === 'en') {
    $subtitle = LiturgiaEnGlossary::translateSubtitleIfPt($subtitle, $kind);
  }

  $html = $html ?? null;
  $text = $text ?? null;

  $isGospel = ($kind === 'gospel');
@endphp

<section class="bg-transparent">
  <header class="mb-6">
    <p class="text-xs font-bold uppercase tracking-wide {{ $isGospel ? 'text-rose-700' : 'text-amber-700' }}">
      {{ $label }}
    </p>

    @if(!empty($ref))
      <p class="mt-1 text-xl sm:text-2xl font-extrabold text-slate-900 leading-tight">
        {{ $ref }}
      </p>
    @endif

    @if(!empty($subtitle))
      <p class="mt-1 text-base sm:text-lg text-slate-600 leading-snug">
        {{ $subtitle }}
      </p>
    @endif
  </header>

  <div class="mt-4">
    <div
      data-font-body
      class="lit-bible text-slate-900 break-words
             [&>p]:mt-5 [&>p:first-child]:mt-0
             [&>p]:leading-relaxed
             [&>sup]:text-amber-600 [&>sup]:font-semibold
             [&>sup]:ml-0.5"
      style="font-size: 18px; line-height: 1.9;"
    >
      @if(!empty($html))
        {!! $html !!}
      @else
        {!! nl2br(e((string)$text)) !!}
      @endif
    </div>
  </div>

  {{-- Aclamações finais --}}
  <div class="mt-8">
    @if($lang === 'en')
      @if($isGospel)
        <p class="font-semibold text-lg text-rose-900">{{ LiturgiaEnGlossary::acclamation('word_of_the_gospel') }}</p>
        <p class="text-lg text-slate-800">{{ LiturgiaEnGlossary::acclamation('praise_to_you_lord_jesus_christ') }}</p>
      @else
        <p class="font-semibold text-lg text-slate-900">{{ LiturgiaEnGlossary::acclamation('word_of_the_lord') }}</p>
        <p class="text-lg text-slate-800">{{ LiturgiaEnGlossary::acclamation('thanks_be_to_god') }}</p>
      @endif
    @else
      {{-- PT fallback --}}
      @if($isGospel)
        <p class="font-semibold text-lg text-rose-900">Palavra da Salvação.</p>
        <p class="text-lg text-slate-800">— Glória a vós, Senhor.</p>
      @else
        <p class="font-semibold text-lg text-slate-900">Palavra do Senhor.</p>
        <p class="text-lg text-slate-800">— Graças a Deus.</p>
      @endif
    @endif
  </div>
</section>
