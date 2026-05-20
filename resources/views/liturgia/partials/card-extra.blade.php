<section class="mb-4 rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-sm">
  <p class="text-[11px] font-semibold text-amber-700 uppercase tracking-wide">Extra</p>
  <p class="mt-1 text-sm sm:text-base font-bold text-slate-900">{{ $title }}</p>
  @if(!empty($ref))
    <p class="mt-1 text-xs sm:text-sm text-slate-600">{{ $ref }}</p>
  @endif
  <div class="mt-3">
    @if(!empty($html))
      <div data-font-body class="leading-7 text-slate-800 break-words [&>p]:mt-3 [&>p:first-child]:mt-0"
           style="font-size: 16px;">
        {!! $html !!}
      </div>
    @else
      <div data-font-body class="whitespace-pre-line leading-7 text-slate-800 break-words" style="font-size: 16px;">
        {{ $text ?: '—' }}
      </div>
    @endif
  </div>
</section>
