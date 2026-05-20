@php
  // Espera receber: $storyLiturgia (para default)
@endphp

<div id="storyModal" class="hidden fixed inset-0 z-[9999] bg-black/70" role="dialog" aria-modal="true" aria-label="Web Stories player">
  <div class="mx-auto flex h-[100dvh] w-full max-w-[420px] flex-col px-4 py-4">
    <div class="mb-3 flex items-center justify-between">
      <div class="flex items-center gap-2">
        <button id="storyPrev" type="button" class="h-9 w-9 rounded-full bg-white/15 text-white hover:bg-white/25 transition" aria-label="Previous story" title="Previous (←)">‹</button>
        <button id="storyNext" type="button" class="h-9 w-9 rounded-full bg-white/15 text-white hover:bg-white/25 transition" aria-label="Next story" title="Next (→)">›</button>
        <button id="storyClose" type="button" class="h-9 w-9 rounded-full bg-white/15 text-white hover:bg-white/25 transition" aria-label="Close" title="Close (Esc)">✕</button>
      </div>
    </div>

    <div class="flex-1 flex items-center justify-center">
      <div class="w-full max-w-[390px] aspect-[9/16] overflow-hidden rounded-[22px] bg-black shadow-2xl ring-1 ring-white/10">
        <amp-story-player id="ampPlayer" style="width:100%;height:100%;">
          <a id="ampAnchor" href="{{ $storyLiturgia }}"></a>
        </amp-story-player>
      </div>
    </div>

    <div class="mt-3 flex items-center justify-between gap-2">
      <a id="storyOpenLink"
         href="{{ $storyLiturgia }}"
         class="flex-1 inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-extrabold text-black hover:bg-white/90 transition">
        Open story
      </a>
      <a href="/web-stories"
         class="inline-flex items-center justify-center rounded-xl bg-white/15 px-4 py-2 text-sm font-semibold text-white hover:bg-white/25 transition">
        Browse list
      </a>
    </div>
  </div>
</div>