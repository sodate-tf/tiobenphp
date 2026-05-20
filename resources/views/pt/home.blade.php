@php
  $siteUrl = rtrim(config('app.url') ?: 'https://www.iatioben.com.br', '/');
  $pageUrl = $siteUrl . '/';
  $logoUrl = $siteUrl . '/images/logo-amp.webp'; // ajuste se seu logo oficial for outro
  $ogImageUrl = $siteUrl . '/og?title=IA%20Tio%20Ben&description=Evangelho%20do%20dia%2C%20liturgia%20di%C3%A1ria%20e%20reflex%C3%B5es%20cat%C3%B3licas';
@endphp

@extends('layouts.site')

{{-- Branding: NÃO traduzir "Tio Ben" --}}
@section('title', 'IA Tio Ben | Liturgia Diária, Evangelho do Dia e Reflexões Católicas')
@section('meta_description', 'Converse com o IA Tio Ben sobre a fé católica. Liturgia diária, Evangelho do dia, oração e orientação espiritual prática — tudo em um só lugar.')
@section('meta_keywords', 'IA Tio Ben, liturgia diária, evangelho do dia, salmo de hoje, reflexão católica, oração católica, Bíblia católica')

{{-- ✅ Robots por ambiente --}}
@section('robots', app()->environment('production')
  ? 'index,follow,max-image-preview:large'
  : 'noindex,nofollow,noarchive')

@section('canonical', $pageUrl)
@section('og_title', 'IA Tio Ben | Evangelho e Liturgia Diária com Inteligência Artificial')
@section('og_description', 'Liturgia diária, Evangelho do dia e apoio para oração — fiel ao ensinamento católico.')
@section('og_url', $pageUrl)
@section('og_locale', 'pt_BR')
@section('og_image', $ogImageUrl)

@section('tw_title', 'IA Tio Ben | Liturgia Diária, Evangelho do Dia e Reflexões Católicas')
@section('tw_description', 'Converse com o IA Tio Ben. Liturgia diária, Evangelho do dia, oração e orientação espiritual prática.')
@section('tw_image', $ogImageUrl)

{{-- ✅ JSON-LD mínimo na Home (PT) --}}
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
      'inLanguage' => 'pt-BR',
      'publisher' => ['@id' => $siteUrl . '/#organization'],
    ],
    [
      '@type' => 'WebPage',
      '@id' => $pageUrl . '#webpage',
      'url' => $pageUrl,
      'name' => 'IA Tio Ben | Liturgia Diária, Evangelho do Dia e Reflexões Católicas',
      'description' => 'Converse com o IA Tio Ben sobre a fé católica. Liturgia diária, Evangelho do dia, oração e orientação espiritual prática — tudo em um só lugar.',
      'inLanguage' => 'pt-BR',
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
        IA Tio Ben: Liturgia Diária, Evangelho do Dia & Oração
      </h1>

      <p class="mt-3 text-gray-800 text-base md:text-lg leading-relaxed">
        Pergunte sobre a fé católica, as leituras da Missa, o Evangelho do dia, oração e vida espiritual no cotidiano.
        Acompanhe também a Liturgia Diária e conteúdos de formação para viver a fé de forma prática.
      </p>

      <div class="mt-6 flex flex-wrap items-center justify-center gap-2">
        <a href="/liturgia-diaria"
           class="inline-flex items-center justify-center rounded-xl bg-amber-800 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-900 transition">
          Abrir Liturgia de Hoje
        </a>

        <a href="/santo-terco"
           class="inline-flex items-center justify-center rounded-xl border border-amber-900/20 bg-white/80 px-4 py-2 text-sm font-semibold text-amber-900 hover:bg-white transition">
          Rezar o Terço
        </a>

        <a href="/blog"
           class="inline-flex items-center justify-center rounded-xl border border-amber-900/20 bg-white/80 px-4 py-2 text-sm font-semibold text-amber-900 hover:bg-white transition">
          Ler o Blog
        </a>

        <a href="/oracao-catolica-pratica"
           class="inline-flex items-center justify-center rounded-xl border border-amber-900/20 bg-white/80 px-4 py-2 text-sm font-semibold text-amber-900 hover:bg-white transition">
          Oração Católica Prática
        </a>
      </div>
    </div>
  </section>

  {{-- ✅ chat padronizado (primeira pergunta + dock) --}}
  @include('partials.home.chat', ['lang' => 'pt'])
</div>
@endsection

@push('scripts')
  <script defer src="/js/chat.js?v=4"></script>
@endpush
