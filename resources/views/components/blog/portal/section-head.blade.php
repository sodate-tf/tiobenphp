@props([
  'id' => null,
  'label' => '',
  'accentText' => 'text-slate-900',
  'accentUnderline' => 'bg-slate-900',
  'href' => '#',
])

<div class="flex items-center justify-between">
  <div>
    <h3 @if($id) id="{{ $id }}" @endif class="text-lg font-extrabold {{ $accentText }}">
      {{ $label }}
    </h3>
    <div class="h-1 w-16 rounded-full {{ $accentUnderline }} mt-2"></div>
  </div>

  <a href="{{ $href }}" class="text-sm font-semibold {{ $accentText }} hover:underline">
    Ver todos
  </a>
</div>
