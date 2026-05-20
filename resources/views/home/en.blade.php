@php
  $siteUrl = rtrim(config('app.url') ?: url('/'), '/');

  $seo = [
    'html_lang' => 'en-US',
    'title' => 'IA Tio Ben | Daily Catholic Readings, Gospel Reflection & Prayer',
    'description' => 'Ask IA Tio Ben about the Catholic faith. Daily Mass readings, Gospel reflection, prayer, and practical spiritual guidance—built for everyday discipleship.',
    'keywords' => ['daily Mass readings','Gospel of the day','Catholic prayer','Catholic Bible study','Catholic reflections','Catholic AI'],
    'canonical' => $siteUrl . '/en',
    'og_title' => 'IA Tio Ben | Daily Catholic Readings and Gospel Guidance',
    'og_description' => 'Today’s Mass readings, Gospel reflection, and prayer support—faithfully aligned with Catholic teaching.',
    'og_locale' => 'en_US',
    'og_image' => $siteUrl . '/og?title=IA%20Tio%20Ben&description=Daily%20Catholic%20readings%2C%20Gospel%20reflection%20and%20prayer',
    'twitter_title' => 'IA Tio Ben | Daily Catholic Readings & Gospel Reflection',
    'twitter_description' => 'Ask IA Tio Ben about the Catholic faith. Daily readings, the Gospel, prayer, and practical spiritual guidance.',
    'twitter_image' => $siteUrl . '/og?title=IA%20Tio%20Ben&description=Daily%20Catholic%20readings%2C%20Gospel%20reflection%20and%20prayer',
  ];
@endphp

@extends('layouts.site', ['seo' => $seo])

@section('content')
  <div class="mx-auto max-w-6xl px-4">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
      <div class="md:col-span-7">
        @include('partials.home.stories-compact', ['lang' => 'en'])
        @include('partials.home.hero', ['lang' => 'en'])
        @include('partials.home.chat', ['lang' => 'en'])
      </div>

      <aside class="hidden md:block md:col-span-5">
        @include('partials.home.quick-actions', ['lang' => 'en'])
        @include('partials.home.trust-cards', ['lang' => 'en'])
      </aside>
    </div>
  </div>
@endsection

@push('scripts')
  <script defer src="/js/home.js"></script>
  <script defer src="/js/chat.js"></script>
@endpush
