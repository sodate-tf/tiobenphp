@props([
  'href' => '#',
  'title' => '',
])

<a href="{{ $href }}"
   class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-900 hover:shadow-sm hover:border-slate-300 transition line-clamp-2">
  {{ $title }}
</a>
