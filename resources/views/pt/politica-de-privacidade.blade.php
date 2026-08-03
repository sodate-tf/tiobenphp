@php
$siteUrl = rtrim(config('app.url') ?: url('/'), '/');
$seo = [
'html_lang' => 'pt-BR',
'title' => 'Política de Privacidade | IA Tio Ben',
'description' => 'Política de Privacidade do IA Tio Ben: informações sobre coleta, armazenamento, uso e proteção dos dados processados pelo serviço.',
'canonical' => $siteUrl . '/politica-de-privacidade',
'og_title' => 'Política de Privacidade | IA Tio Ben',
'og_description' => 'Conheça como tratamos as informações processadas pelo IA Tio Ben.',
'og_locale' => 'pt_BR',
'og_image' => $siteUrl . '/og?title=IA%20Tio%20Ben&description=Politica%20de%20Privacidade',
];
@endphp

@extends('layouts.site', ['seo' => $seo])

@section('content')

<section class="max-w-6xl mx-auto px-4 py-12">
  <h1 class="text-3xl md:text-4xl font-bold text-center text-amber-900 mb-10">
    Política de Privacidade
  </h1>

  <div class="border border-amber-300 bg-amber-50 shadow-lg rounded-2xl">
    <div class="p-6 space-y-4">

```
  <p class="text-amber-900">
    <strong>Site:</strong> iatioben.com.br <br />
    <strong>Aplicativo:</strong> IA Tio Ben – Fé Católica <br />
    <strong>Última atualização:</strong> Junho de 2026
  </p>

  <h2 class="text-2xl font-semibold text-amber-800">1. Compromisso com a Privacidade</h2>

  <p class="text-amber-900">
    O IA Tio Ben foi desenvolvido para auxiliar católicos em sua caminhada de fé por meio da Liturgia Diária, Santo Terço, conteúdos de formação e respostas geradas por inteligência artificial fundamentadas na doutrina católica.
  </p>

  <p class="text-amber-900">
    Respeitamos a privacidade dos usuários e buscamos coletar apenas as informações estritamente necessárias para o funcionamento do serviço.
  </p>

  <h2 class="text-2xl font-semibold text-amber-800">2. Informações Processadas</h2>

  <h3 class="text-xl font-semibold text-amber-800">2.1 Perguntas e Interações</h3>

  <p class="text-amber-900">
    Quando o usuário utiliza o assistente IA Tio Ben, podem ser processadas:
  </p>

  <ul class="list-disc list-inside text-amber-900 space-y-1">
    <li>Perguntas enviadas ao sistema</li>
    <li>Mensagens digitadas pelo usuário</li>
    <li>Respostas geradas pela inteligência artificial</li>
  </ul>

  <h3 class="text-xl font-semibold text-amber-800">2.2 Dados Técnicos</h3>

  <p class="text-amber-900">
    Para garantir o funcionamento adequado da plataforma, podem ser registrados:
  </p>

  <ul class="list-disc list-inside text-amber-900 space-y-1">
    <li>Tipo de navegador</li>
    <li>Sistema operacional</li>
    <li>Data e horário de acesso</li>
    <li>Informações técnicas necessárias para manutenção e segurança</li>
  </ul>

  <h2 class="text-2xl font-semibold text-amber-800">3. Informações que Não Coletamos</h2>

  <p class="text-amber-900">
    O IA Tio Ben não exige cadastro e não solicita:
  </p>

  <ul class="list-disc list-inside text-amber-900 space-y-1">
    <li>Nome completo</li>
    <li>CPF</li>
    <li>Endereço residencial</li>
    <li>Telefone</li>
    <li>Dados bancários</li>
    <li>Fotografias pessoais</li>
    <li>Localização precisa do dispositivo</li>
  </ul>

  <p class="text-amber-900 font-semibold">
    Não realizamos identificação individual dos usuários.
  </p>

  <h2 class="text-2xl font-semibold text-amber-800">4. Armazenamento das Perguntas e Respostas</h2>

  <p class="text-amber-900">
    As perguntas enviadas ao assistente e as respostas geradas podem ser armazenadas em banco de dados para:
  </p>

  <ul class="list-disc list-inside text-amber-900 space-y-1">
    <li>Melhoria contínua do sistema</li>
    <li>Correção de falhas técnicas</li>
    <li>Análises estatísticas</li>
    <li>Evolução dos recursos da plataforma</li>
  </ul>

  <p class="text-amber-900">
    Esses registros não são vinculados à identidade dos usuários.
  </p>

  <p class="text-amber-900 font-semibold">
    Não possuímos meios de identificar qual pessoa realizou determinada pergunta.
  </p>

  <h2 class="text-2xl font-semibold text-amber-800">5. Uso de Inteligência Artificial</h2>

  <p class="text-amber-900">
    O IA Tio Ben utiliza serviços de inteligência artificial fornecidos pela OpenAI para geração de respostas.
  </p>

  <p class="text-amber-900">
    Ao enviar uma pergunta, o conteúdo informado poderá ser processado pelos sistemas necessários para gerar a resposta solicitada.
  </p>

  <p class="text-amber-900 font-semibold">
    Recomendamos que os usuários não compartilhem informações pessoais sensíveis durante a utilização do serviço.
  </p>

  <h2 class="text-2xl font-semibold text-amber-800">6. Compartilhamento de Informações</h2>

  <p class="text-amber-900">
    Não vendemos, alugamos ou comercializamos informações dos usuários.
  </p>

  <p class="text-amber-900">
    Informações processadas poderão ser compartilhadas apenas quando necessário para:
  </p>

  <ul class="list-disc list-inside text-amber-900 space-y-1">
    <li>Funcionamento da inteligência artificial</li>
    <li>Infraestrutura de hospedagem e segurança</li>
    <li>Cumprimento de obrigações legais</li>
    <li>Prevenção de abusos ou atividades ilícitas</li>
  </ul>

  <h2 class="text-2xl font-semibold text-amber-800">7. Segurança das Informações</h2>

  <p class="text-amber-900">
    Adotamos medidas técnicas adequadas para proteger as informações processadas.
  </p>

  <ul class="list-disc list-inside text-amber-900 space-y-1">
    <li>Conexões protegidas por HTTPS</li>
    <li>Monitoramento de segurança dos servidores</li>
    <li>Controle de acesso às informações armazenadas</li>
  </ul>

  <p class="text-amber-900">
    Apesar dos esforços empregados, nenhum sistema é totalmente imune a riscos de segurança.
  </p>

  <h2 class="text-2xl font-semibold text-amber-800">8. Retenção dos Dados</h2>

  <p class="text-amber-900">
    Perguntas e respostas podem ser armazenadas por prazo indeterminado para melhoria da plataforma e análises estatísticas.
  </p>

  <p class="text-amber-900">
    Como os registros não estão associados à identidade dos usuários, não é possível localizar ou excluir perguntas específicas mediante solicitação individual.
  </p>

  <h2 class="text-2xl font-semibold text-amber-800">9. Direitos dos Usuários</h2>

  <p class="text-amber-900">
    Os usuários podem solicitar esclarecimentos sobre esta Política de Privacidade por meio dos canais oficiais disponibilizados pelo IA Tio Ben.
  </p>

  <p class="text-amber-900">
    Em razão da ausência de identificação individual dos usuários, determinadas solicitações relacionadas à localização ou exclusão de registros podem não ser tecnicamente possíveis.
  </p>

  <h2 class="text-2xl font-semibold text-amber-800">10. Alterações desta Política</h2>

  <p class="text-amber-900">
    Esta Política de Privacidade poderá ser atualizada periodicamente para refletir melhorias na plataforma, mudanças legais ou ajustes operacionais.
  </p>

  <p class="text-amber-900">
    A versão mais recente estará sempre disponível nesta página.
  </p>

  <h2 class="text-2xl font-semibold text-amber-800">11. Contato</h2>

  <p class="text-amber-900">
    Para dúvidas relacionadas à privacidade ou ao funcionamento da plataforma, utilize os canais de contato disponíveis no site oficial.
  </p>

  <h2 class="text-2xl font-semibold text-amber-800">12. Aceitação</h2>

  <p class="text-amber-900">
    Ao utilizar o site ou o aplicativo IA Tio Ben – Fé Católica, o usuário declara estar ciente e concordar com os termos desta Política de Privacidade.
  </p>

  <p class="text-amber-900 mt-6 italic">
    “Conhecereis a verdade, e a verdade vos libertará.” (João 8,32)
  </p>

</div>
```

  </div>
</section>
@endsection
