<div class="space-y-4">
  <section class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-sm">
    <p class="text-[11px] font-semibold text-amber-700 uppercase tracking-wide">Antífona</p>
    <p class="mt-1 text-sm sm:text-base font-bold text-slate-900">Antífona de entrada</p>
    <div class="mt-3">
      @if(!empty($entradaHtml))
        <div data-font-body class="leading-7 text-slate-800 break-words" style="font-size: 16px;">{!! $entradaHtml !!}</div>
      @else
        <div data-font-body class="whitespace-pre-line leading-7 text-slate-800 break-words" style="font-size: 16px;">{{ $entradaText ?: '—' }}</div>
      @endif
    </div>
  </section>

  <section class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-sm">
    <p class="text-[11px] font-semibold text-amber-700 uppercase tracking-wide">Antífona</p>
    <p class="mt-1 text-sm sm:text-base font-bold text-slate-900">Antífona da comunhão</p>
    <div class="mt-3">
      @if(!empty($comunhaoHtml))
        <div data-font-body class="leading-7 text-slate-800 break-words" style="font-size: 16px;">{!! $comunhaoHtml !!}</div>
      @else
        <div data-font-body class="whitespace-pre-line leading-7 text-slate-800 break-words" style="font-size: 16px;">{{ $comunhaoText ?: '—' }}</div>
      @endif
    </div>
  </section>
</div>
