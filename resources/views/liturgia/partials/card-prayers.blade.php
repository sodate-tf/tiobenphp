@php
  $extras = is_array($o['extras'] ?? null) ? $o['extras'] : [];
@endphp

<div class="space-y-4">
  @if(!empty($o['coleta']))
    @include('liturgia.partials.card-extra', [
      'title' => 'Oração do Dia (Coleta)',
      'ref' => null,
      'html' => $o['coletaHtml'] ?? null,
      'text' => $o['coleta'] ?? null,
    ])
  @endif

  @if(!empty($o['oferendas']))
    @include('liturgia.partials.card-extra', [
      'title' => 'Oração sobre as oferendas',
      'ref' => null,
      'html' => $o['oferendasHtml'] ?? null,
      'text' => $o['oferendas'] ?? null,
    ])
  @endif

  @if(!empty($o['comunhao']))
    @include('liturgia.partials.card-extra', [
      'title' => 'Oração depois da comunhão',
      'ref' => null,
      'html' => $o['comunhaoHtml'] ?? null,
      'text' => $o['comunhao'] ?? null,
    ])
  @endif

  @foreach($extras as $idx => $x)
    @include('liturgia.partials.card-extra', [
      'title' => (string)($x['tipo'] ?? $x['titulo'] ?? ('Oração extra '.($idx+1))),
      'ref' => null,
      'html' => $x['textoHtml'] ?? null,
      'text' => $x['texto'] ?? null,
    ])
  @endforeach
</div>
