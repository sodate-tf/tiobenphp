@php
  $siteUrl = rtrim(config('app.url') ?: url('/'), '/');

  $seo = [
    'html_lang' => 'pt-BR',
    'title' => 'IA Tio Ben | Liturgia Diária, Evangelho do Dia e Reflexões Católicas',
    'description' => 'Converse com o Tio Ben, sua inteligência artificial católica. Liturgia diária, Evangelho do dia, salmos e reflexões cristãs em um só lugar.',
    'keywords' => ['IA Tio Ben','liturgia diária','evangelho do dia','salmo de hoje','reflexão católica','inteligência artificial católica'],
    'canonical' => $siteUrl . '/',
    'og_title' => 'IA Tio Ben | Evangelho e Liturgia Diária com Inteligência Artificial',
    'og_description' => 'Evangelho do dia, liturgia diária e reflexões católicas com o IA Tio Ben — fé, oração e Palavra de Deus todos os dias.',
    'og_locale' => 'pt_BR',
    'og_image' => $siteUrl . '/og?title=IA%20Tio%20Ben&description=Evangelho%20do%20dia%2C%20liturgia%20di%C3%A1ria%20e%20reflex%C3%B5es%20cat%C3%B3licas',
    'twitter_title' => 'IA Tio Ben | Liturgia Diária, Evangelho do Dia e Reflexões Católicas',
    'twitter_description' => 'Converse com o Tio Ben. Liturgia diária, Evangelho do dia, salmos e reflexões cristãs em um só lugar.',
    'twitter_image' => $siteUrl . '/og?title=IA%20Tio%20Ben&description=Evangelho%20do%20dia%2C%20liturgia%20di%C3%A1ria%20e%20reflex%C3%B5es%20cat%C3%B3licas',
  ];
@endphp

@extends('layouts.site', ['seo' => $seo])

@section('content')
  <div class="mx-auto max-w-6xl px-4">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
      <div class="md:col-span-7">
        @include('partials.home.stories-compact', ['lang' => 'pt'])
        @include('partials.home.hero', ['lang' => 'pt'])
        @include('partials.home.chat', ['lang' => 'pt'])
      </div>

      <aside class="hidden md:block md:col-span-5">
        @include('partials.home.quick-actions', ['lang' => 'pt'])
        @include('partials.home.trust-cards', ['lang' => 'pt'])
      </aside>
    </div>
  </div>
@endsection

@push('scripts')
  <script defer src="/js/home.js"></script>
  <script defer src="/js/chat.js"></script>
@endpush
