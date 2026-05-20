@php
  $siteUrl = rtrim(config('app.url') ?: url('/'), '/');
  $pageUrl = $siteUrl . '/en';
  $logoUrl = $siteUrl . '/images/logo-amp.webp'; // ajuste se seu logo oficial for outro
  $ogImageUrl = $siteUrl . '/og?title=IA%20Tio%20Ben&description=Daily%20Catholic%20Mass%20readings%2C%20Gospel%20reflection%20and%20prayer';
@endphp

@extends('layouts.site')

{{-- Branding: NÃO traduzir "Tio Ben" --}}
@section('title', 'IA Tio Ben | Daily Catholic Mass Readings, Gospel Reflection & Prayer')
@section('meta_description', 'Ask IA Tio Ben your questions about the Catholic faith. Daily Mass readings, the Gospel, prayer, and practical spiritual guidance—all in one place.')
@section('meta_keywords', 'today\'s Mass readings, Catholic daily readings, Gospel of the day, Catholic prayer, Catholic Bible study, Catholic reflections, Catholic AI')

{{-- ✅ Robots por ambiente --}}
@section('robots', app()->environment('production')
  ? 'index,follow,max-image-preview:large'
  : 'noindex,nofollow,noarchive')

@section('canonical', $pageUrl)
@section('og_title', 'IA Tio Ben | Daily Mass Readings and Catholic Guidance')
@section('og_description', 'Today’s Catholic Mass readings, Gospel reflections, and prayer support—faithfully aligned with Catholic teaching.')
@section('og_url', $pageUrl)
@section('og_locale', 'en_US')
@section('og_image', $ogImageUrl)

@section('tw_title', 'IA Tio Ben | Daily Catholic Mass Readings & Gospel Reflection')
@section('tw_description', 'Ask IA Tio Ben about the Catholic faith. Daily readings, the Gospel, prayer, and practical spiritual guidance.')
@section('tw_image', $ogImageUrl)

{{-- ✅ JSON-LD mínimo na Home (EN) --}}
@push('head')
<script type="application/ld+json">
{!! json_encode([
  '@context' => 'https://schema.org',
  '@graph' => [
    [
      '@type' => 'Organization',
      '@id' => $siteUrl . '/#organization',
      'name' => 'IA Tio Ben',
      'url' => $siteUrl . '/',
      'logo' => [
        '@type' => 'ImageObject',
        'url' => $logoUrl,
      ],
    ],
    [
      '@type' => 'WebSite',
      '@id' => $siteUrl . '/#website',
      'url' => $siteUrl . '/',
      'name' => 'IA Tio Ben',
      'inLanguage' => 'en',
      'publisher' => ['@id' => $siteUrl . '/#organization'],
    ],
    [
      '@type' => 'WebPage',
      '@id' => $pageUrl . '#webpage',
      'url' => $pageUrl,
      'name' => 'IA Tio Ben | Daily Catholic Mass Readings, Gospel Reflection & Prayer',
      'description' => 'Ask IA Tio Ben your questions about the Catholic faith. Daily Mass readings, the Gospel, prayer, and practical spiritual guidance—all in one place.',
      'inLanguage' => 'en',
      'isPartOf' => ['@id' => $siteUrl . '/#website'],
      'about' => ['@id' => $siteUrl . '/#organization'],
      'primaryImageOfPage' => [
        '@type' => 'ImageObject',
        'url' => $ogImageUrl,
      ],
    ],
  ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')
<div class="bg-gradient-to-b from-amber-200 to-amber-400 min-h-screen">
  <section class="mx-auto w-full max-w-5xl px-5 py-8">
    <div class="text-center">
      <div class="flex justify-center">
        <img src="/images/tio-ben-transparente.webp" alt="IA Tio Ben" class="h-[200px] w-[200px]" />
      </div>

      <h1 class="mt-6 text-2xl md:text-4xl font-extrabold text-amber-900 tracking-tight">
        IA Tio Ben: Daily Catholic Mass Readings, Gospel Reflection & Prayer
      </h1>

      <p class="mt-3 text-gray-800 text-base md:text-lg leading-relaxed">
        Ask about the Catholic faith, the Mass readings, the Gospel, prayer, and everyday spiritual life.
        You can also follow the Daily Liturgy and formation content built for real-life discipleship.
      </p>

      <div class="mt-6 flex flex-wrap items-center justify-center gap-2">
        <a href="/en/daily-mass-readings"
           class="inline-flex items-center justify-center rounded-xl bg-amber-800 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-900 transition">
          Open Daily Readings
        </a>

        <a href="/en/rosary"
           class="inline-flex items-center justify-center rounded-xl border border-amber-900/20 bg-white/80 px-4 py-2 text-sm font-semibold text-amber-900 hover:bg-white transition">
          Pray the Rosary
        </a>

        <a href="/en/blog"
           class="inline-flex items-center justify-center rounded-xl border border-amber-900/20 bg-white/80 px-4 py-2 text-sm font-semibold text-amber-900 hover:bg-white transition">
          Read the Blog
        </a>

        <a href="/en/practical-catholic-prayer"
           class="inline-flex items-center justify-center rounded-xl border border-amber-900/20 bg-white/80 px-4 py-2 text-sm font-semibold text-amber-900 hover:bg-white transition">
          Practical Catholic Prayer
        </a>

        <a href="/en/practical-sacramental-life"
           class="inline-flex items-center justify-center rounded-xl border border-amber-900/20 bg-white/80 px-4 py-2 text-sm font-semibold text-amber-900 hover:bg-white transition">
          Practical Sacramental Life
        </a>

        <a href="/en/catholic-faith-questions"
           class="inline-flex items-center justify-center rounded-xl border border-amber-900/20 bg-white/80 px-4 py-2 text-sm font-semibold text-amber-900 hover:bg-white transition">
          Catholic Faith Questions
        </a>
      </div>
    </div>
  </section>

  {{-- ✅ chat padronizado (primeira pergunta + dock) --}}
  @include('partials.home.chat', ['lang' => 'en'])
</div>
@endsection

@push('scripts')
  <script defer src="/js/chat.js?v=4"></script>
@endpush
