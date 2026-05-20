@php
  $posts = $posts ?? collect();
  $top = $posts->first();
  $grid = $posts->slice(1, 6);
@endphp

<section class="rounded-[28px] border {{ $theme['accentBorder'] }} {{ $theme['accentBg'] }} p-6" aria-labelledby="section-{{ $categorySlug }}">
  <div class="flex items-start justify-between gap-4">
    <div>
      <h2 id="section-{{ $categorySlug }}" class="text-xl md:text-2xl font-extrabold {{ $theme['accentText'] }}">
        {{ $theme['label'] }}
      </h2>
      <div class="h-1 w-16 rounded-full {{ $theme['accentUnderline'] }} mt-2"></div>
    </div>

    <a href="{{ url('/blog/categoria/'.$categorySlug) }}"
       class="rounded-full bg-white px-4 py-2 text-sm font-semibold border {{ $theme['accentBorder'] }} {{ $theme['accentText'] }} hover:shadow-sm">
      Ver todos
    </a>
  </div>

  <div class="mt-6 grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)] gap-6">
    <div class="min-w-0">
      @if($top)
        <article class="rounded-3xl border border-white/60 bg-white/80 p-6">
          <a href="{{ url('/blog/'.$top->slug) }}" class="block text-xl font-extrabold text-gray-900 leading-snug hover:underline">
            {{ $top->title }}
          </a>
          @if($top->meta_description)
            <p class="mt-2 text-sm text-gray-700">{{ $top->meta_description }}</p>
          @endif
        </article>
      @else
        <div class="rounded-3xl border border-white/60 bg-white/60 p-6 text-gray-700">Sem posts ainda.</div>
      @endif
    </div>

    <ul class="min-w-0 grid grid-cols-1 sm:grid-cols-2 gap-4" role="list">
      @foreach($grid as $p)
        <li>
          <a href="{{ url('/blog/'.$p->slug) }}" class="block rounded-2xl border border-gray-200 bg-white p-4 hover:shadow-sm transition">
            <div class="text-sm font-extrabold text-gray-900 leading-snug line-clamp-2">{{ $p->title }}</div>
            @if($p->meta_description)
              <div class="mt-1 text-sm text-gray-700 line-clamp-2">{{ $p->meta_description }}</div>
            @endif
          </a>
        </li>
      @endforeach
    </ul>
  </div>
</section>
