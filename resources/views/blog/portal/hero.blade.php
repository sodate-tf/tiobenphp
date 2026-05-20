{{-- resources/views/blog/portal/hero.blade.php --}}
@php
  if (!$featured) return;

  $heroTheme = [
    'label' => 'Destaque',
    'accentText' => 'text-gray-900',
    'accentBg' => 'bg-gray-50',
    'accentBorder' => 'border-gray-200',
    'ogTint' => 'slate',
  ];
@endphp

<section class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_340px] gap-6">
  <div class="min-w-0">
    @include('blog.portal.card', ['post' => $featured, 'siteUrl' => $siteUrl, 'theme' => $heroTheme, 'size' => 'lg'])

    <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-3">
      @foreach(collect($secondary)->slice(0,3) as $p)
        <a href="{{ url('/blog/' . $p->slug) }}"
           class="rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-900 hover:shadow-sm transition">
          {{ $p->title }}
        </a>
      @endforeach
    </div>
  </div>

  <div class="min-w-0 space-y-4">
    @foreach(collect($secondary)->slice(3,3) as $p)
      <div class="rounded-3xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="text-xs text-gray-500">Em alta</div>
        <a href="{{ url('/blog/' . $p->slug) }}"
           class="mt-2 block text-base font-extrabold text-gray-900 leading-snug hover:underline">
          {{ $p->title }}
        </a>
        @if(!empty($p->meta_description))
          <p class="mt-2 text-sm text-gray-700 line-clamp-2">{{ $p->meta_description }}</p>
        @endif
      </div>
    @endforeach
  </div>
</section>
