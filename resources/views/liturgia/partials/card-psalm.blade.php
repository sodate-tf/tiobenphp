@php
  $lang = $lang ?? 'pt';
  $ref = $ref ?? null;
  $refrao = $refrao ?? null;
  $html = $html ?? null;
  $text = $text ?? null;
@endphp

<section class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-sm">
  <div class="flex flex-col gap-1">
    <p class="text-[11px] font-semibold text-amber-700 uppercase tracking-wide">
      {{ $lang === 'en' ? 'Responsorial Psalm' : 'Salmo Responsorial' }}
    </p>
    @if(!empty($ref))
      <p class="text-sm sm:text-base font-bold text-slate-900">{{ $ref }}</p>
    @endif
  </div>

  @if(!empty($refrao))
    <div class="mt-3 rounded-2xl border border-amber-100 bg-amber-50 p-4">
      <p class="text-[11px] font-semibold text-amber-800 uppercase tracking-wide">
        {{ $lang === 'en' ? 'Response' : 'Refrão' }}
      </p>
      <p class="mt-1 text-base sm:text-lg font-extrabold text-amber-900 leading-snug">
        {{ $refrao }}
      </p>
    </div>
  @endif

  <div class="mt-4">
    <div data-font-body class="leading-7 text-slate-800 break-words [&>p]:mt-3 [&>p:first-child]:mt-0"
         style="font-size: 16px;">
      @if(!empty($html))
        {!! $html !!}
      @else
        {!! nl2br(e((string)$text)) !!}
      @endif
    </div>
  </div>
</section>
