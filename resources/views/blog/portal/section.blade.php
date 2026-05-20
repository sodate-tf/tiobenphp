{{-- resources/views/blog/portal/section.blade.php --}}

@php
  $top  = $posts[0] ?? null;
  $grid = array_slice($posts, 1, 6);

  // id semântico da seção (igual ao React: section-${categorySlug})
  $sectionId = 'section-' . $categorySlug;

  // URL da categoria
  $categoryUrl = url('/blog/categoria/' . $categorySlug);
@endphp

<section
  class="rounded-[28px] border {{ $theme['accentBorder'] ?? '' }} {{ $theme['accentBg'] ?? '' }} p-6"
  aria-labelledby="{{ $sectionId }}"
>
  <div class="flex items-start justify-between gap-4">
    <div>
      {{-- Heading de seção --}}
      <h2
        id="{{ $sectionId }}"
        class="text-xl md:text-2xl font-extrabold {{ $theme['accentText'] ?? '' }}"
      >
        {{ $theme['label'] ?? 'Seção' }}
      </h2>

      <div class="h-1 w-16 rounded-full {{ $theme['accentUnderline'] ?? '' }} mt-2"></div>
    </div>

    <a
      href="{{ $categoryUrl }}"
      aria-label="Ver todos os posts de {{ $theme['label'] ?? 'categoria' }}"
      class="rounded-full bg-white px-4 py-2 text-sm font-semibold border {{ $theme['accentBorder'] ?? '' }} {{ $theme['accentText'] ?? '' }} hover:shadow-sm"
    >
      Ver todos
    </a>
  </div>

  <div class="mt-6 grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)] gap-6">
    <div class="min-w-0">
      @if($top)
        @include('blog.portal.portal-card', [
          'post' => $top,
          'siteUrl' => $siteUrl,
          'theme' => $theme,
          'size' => 'lg',
          'hideCover' => false,
        ])
      @else
        <div class="rounded-3xl border border-white/60 bg-white/60 p-6 text-gray-700">
          Sem posts ainda.
        </div>
      @endif
    </div>

    {{-- Lista semântica --}}
    <ul class="min-w-0 grid grid-cols-1 sm:grid-cols-2 gap-4" role="list">
      @foreach($grid as $p)
        <li>
          @include('blog.portal.portal-card', [
            'post' => $p,
            'siteUrl' => $siteUrl,
            'theme' => $theme,
            'size' => 'sm',
            'hideCover' => false,
          ])
        </li>
      @endforeach
    </ul>
  </div>
</section>
