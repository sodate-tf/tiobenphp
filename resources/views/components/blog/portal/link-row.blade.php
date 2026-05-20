@props([
  'href' => '#',
  'title' => '',
])

<article class="group rounded-2xl border border-slate-200 bg-white px-4 py-3 hover:border-slate-300 hover:shadow-sm transition">
  <a href="{{ $href }}"
     class="block text-sm font-extrabold text-slate-900 leading-snug group-hover:underline line-clamp-2">
    {{ $title }}
  </a>
</article>
