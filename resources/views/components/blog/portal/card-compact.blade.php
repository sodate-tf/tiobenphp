@props([
  'kicker' => null,
  'href' => '#',
  'title' => '',
  'desc' => null,
  'date' => null,
])

<article class="rounded-[28px] border border-slate-200 bg-white p-4 shadow-sm">
  @if($kicker)
    <div class="text-[11px] uppercase tracking-[0.16em] text-slate-500">{{ $kicker }}</div>
  @endif

  <a href="{{ $href }}" class="mt-2 block text-base font-extrabold text-slate-900 leading-snug hover:underline">
    {{ $title }}
  </a>

  @if(!empty($desc))
    <p class="mt-2 text-sm text-slate-700 line-clamp-2">{{ $desc }}</p>
  @endif

  @if(!empty($date))
    <div class="mt-3 text-xs text-slate-500">{{ $date }}</div>
  @endif
</article>
