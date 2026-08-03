<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\View\View;

class LiturgyGuideHubController extends Controller
{
    public function show(string $guide): View
    {
        $year = (int) Carbon::now('America/Sao_Paulo')->year;
        $guides = [
            'liturgia-diaria' => [
                'title' => 'Liturgia Diária: Evangelho, Salmo e Leituras da Missa',
                'description' => 'Entenda como acompanhar a Liturgia Diária, o Evangelho do dia, o Salmo e as leituras da Missa.',
                'heading' => 'Como acompanhar a Liturgia Diária',
                'intro' => 'A Liturgia Diária organiza as leituras da Missa para ajudar você a rezar com a Igreja em cada dia.',
            ],
            'evangelho-do-dia' => [
                'title' => 'Evangelho do Dia: Como Ler, Rezar e Meditar',
                'description' => 'Aprenda a ler e meditar o Evangelho do dia com um caminho simples de oração e vida cristã.',
                'heading' => 'Como rezar com o Evangelho do dia',
                'intro' => 'O Evangelho do dia é uma porta concreta para ouvir Jesus e levar a Palavra para a vida cotidiana.',
            ],
            'salmo-do-dia' => [
                'title' => 'Salmo do Dia: Como Rezar o Salmo Responsorial',
                'description' => 'Entenda o Salmo do dia e aprenda a rezar o Salmo Responsorial presente nas leituras da Missa.',
                'heading' => 'Como rezar o Salmo do dia',
                'intro' => 'O Salmo Responsorial responde a Palavra proclamada e ajuda a transformar a leitura em oração.',
            ],
            'calendario-liturgico' => [
                'title' => "Calendário Litúrgico {$year}: Tempos, Cores e Ciclo A/B/C",
                'description' => "Veja o Calendário Litúrgico {$year}, os tempos da Igreja, as cores e o ciclo de leituras.",
                'heading' => "Calendário Litúrgico {$year}",
                'intro' => 'O calendário da Igreja acompanha os mistérios de Cristo e orienta as leituras celebradas ao longo do ano.',
            ],
        ];

        abort_unless(isset($guides[$guide]), 404);

        return view('liturgia.guides.show', ['guide' => $guides[$guide], 'year' => $year, 'slug' => $guide]);
    }
}
