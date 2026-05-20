@props([
  'href' => '#',
  'title' => '',
  'desc' => null,
  'date' => null,
  'accentBorder' => 'border-slate-200',
])

<article class="rounded-[28px] border {{ $accentBorder }} bg-white p-5 shadow-sm">
  <a href="{{ $href }}" class="block text-lg font-extrabold text-slate-900 leading-snug hover:underline">
    {{ $title }}
  </a>

  @if(!empty($desc))
    <p class="mt-2 text-sm text-slate-700 line-clamp-2">{{ $desc }}</p>
  @endif

  @if(!empty($date))
    <div class="mt-3 text-xs text-slate-500">{{ $date }}</div>
  @endif
</article>
