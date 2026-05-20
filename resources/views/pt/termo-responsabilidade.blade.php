@php
  $siteUrl = rtrim(config('app.url') ?: url('/'), '/');
  $seo = [
    'html_lang' => 'pt-BR',
    'title' => 'Termo de Responsabilidade | IA Tio Ben',
    'description' => 'Termo de responsabilidade do IA Tio Ben: natureza do serviço, limitações e orientações para uso responsável.',
    'canonical' => $siteUrl . '/termo-de-responsabilidade',
    'og_title' => 'Termo de Responsabilidade | IA Tio Ben',
    'og_description' => 'Natureza do serviço, limitações e orientações para uso responsável.',
    'og_locale' => 'pt_BR',
    'og_image' => $siteUrl . '/og?title=IA%20Tio%20Ben&description=Termo%20de%20Responsabilidade',
  ];
@endphp

@extends('layouts.site', ['seo' => $seo])

@section('content')
<section class="max-w-6xl mx-auto px-4 py-12">
  <h1 class="text-3xl md:text-4xl font-bold text-center text-amber-900 mb-10">
    Termo de Responsabilidade
  </h1>

  <div class="border border-amber-300 bg-amber-50 shadow-lg rounded-2xl">
    <div class="p-6 space-y-4">
      <p class="text-amber-900">
        <strong>Site:</strong> iatioben.com.br <br />
        <strong>Última atualização:</strong> Agosto de 2025
      </p>

      <h2 class="text-2xl font-semibold text-amber-800">1. Natureza do Serviço</h2>
      <p class="text-amber-900">
        O iatioben.com.br é um serviço de orientação espiritual baseado em inteligência artificial que oferece respostas fundamentadas exclusivamente na doutrina católica oficial, incluindo a Bíblia Sagrada, o Catecismo da Igreja Católica, documentos pontifícios e a Tradição apostólica.
      </p>
      <p class="text-amber-900 font-semibold">
        IMPORTANTE: Este serviço não substitui o acompanhamento presencial de um sacerdote, catequista qualificado, diretor espiritual ou profissionais da área da saúde. As respostas fornecidas têm caráter exclusivamente informativo e educativo sobre a fé católica.
      </p>

      <h2 class="text-2xl font-semibold text-amber-800">2. Limitações do Serviço</h2>
      <h3 class="text-xl font-semibold text-amber-800">2.1 Natureza Tecnológica</h3>
      <ul class="list-disc list-inside text-amber-900 space-y-1">
        <li>O “Tio Ben” é um sistema de inteligência artificial e pode apresentar limitações ou erros interpretativos</li>
        <li>Respostas são geradas automaticamente com base em programação e bases de dados doutrinários</li>
        <li>Em caso de dúvidas complexas ou contradições, sempre consulte um sacerdote ou catequista presencial</li>
      </ul>

      <h3 class="text-xl font-semibold text-amber-800">2.2 Alcance das Orientações</h3>
      <ul class="list-disc list-inside text-amber-900 space-y-1">
        <li>As respostas não constituem aconselhamento profissional médico, psicológico, jurídico ou financeiro</li>
        <li>Questões de foro íntimo, sacramental ou que exijam acompanhamento personalizado devem ser direcionadas a um sacerdote</li>
        <li>O serviço não realiza atendimento de emergência ou situações de risco iminente</li>
      </ul>

      <h2 class="text-2xl font-semibold text-amber-800">3. Situações que Exigem Ajuda Profissional</h2>
      <h3 class="text-xl font-semibold text-amber-800">3.1 Emergências</h3>
      <p class="text-amber-900">Em casos de:</p>
      <ul class="list-disc list-inside text-amber-900 space-y-1">
        <li>Pensamentos suicidas ou autolesão</li>
        <li>Violência doméstica ou abuso</li>
        <li>Crises psiquiátricas ou psicológicas graves</li>
        <li>Situações de risco à vida ou segurança</li>
      </ul>
      <p class="text-amber-900 font-semibold">PROCURE IMEDIATAMENTE:</p>
      <ul class="list-disc list-inside text-amber-900 space-y-1">
        <li>Centro de Valorização da Vida (CVV): 188</li>
        <li>SAMU: 192</li>
        <li>Polícia Militar: 190</li>
        <li>Bombeiros: 193</li>
        <li>Disque Direitos Humanos: 100</li>
      </ul>

      <h3 class="text-xl font-semibold text-amber-800">3.2 Acompanhamento Especializado</h3>
      <p class="text-amber-900">
        Para questões envolvendo saúde mental, relacionamentos complexos, vícios, luto patológico ou traumas, procure:
      </p>
      <ul class="list-disc list-inside text-amber-900 space-y-1">
        <li>Profissionais de saúde mental (psicólogos, psiquiatras)</li>
        <li>Seu pároco ou diretor espiritual</li>
        <li>Catequistas e agentes pastorais da sua comunidade</li>
        <li>Grupos de apoio especializados</li>
      </ul>

      <h2 class="text-2xl font-semibold text-amber-800">4. Uso Responsável</h2>
      <h3 class="text-xl font-semibold text-amber-800">4.1 Compromisso do Usuário</h3>
      <p class="text-amber-900">Ao utilizar este serviço, você declara:</p>
      <ul class="list-disc list-inside text-amber-900 space-y-1">
        <li>Ter mais de 18 anos ou contar com supervisão de responsável legal</li>
        <li>Compreender as limitações do serviço automatizado</li>
        <li>Não utilizar as respostas como única fonte para decisões importantes</li>
        <li>Buscar confirmação em fontes oficiais da Igreja quando necessário</li>
      </ul>

      <h3 class="text-xl font-semibold text-amber-800">4.2 Conteúdo das Perguntas</h3>
      <ul class="list-disc list-inside text-amber-900 space-y-1">
        <li>Mantenha o respeito e a dignidade cristã nas interações</li>
        <li>Evite conteúdo ofensivo, blasfemo ou contrário à moral católica</li>
        <li>Não compartilhe informações pessoais sensíveis desnecessariamente</li>
        <li>Lembre-se de que este não é um espaço para confissão sacramental</li>
      </ul>

      <h2 class="text-2xl font-semibold text-amber-800">5. Privacidade e Dados</h2>
      <h3 class="text-xl font-semibold text-amber-800">5.1 Coleta de Informações</h3>
      <ul class="list-disc list-inside text-amber-900 space-y-1">
        <li><strong>NÃO coletamos dados pessoais</strong> que permitam identificar usuários</li>
        <li><strong>NÃO armazenamos</strong> informações como nome, e-mail, telefone ou endereço</li>
        <li><strong>NÃO conseguimos identificar</strong> quem fez determinada pergunta</li>
        <li><strong>NÃO realizamos</strong> rastreamento individual de usuários</li>
      </ul>

      <h3 class="text-xl font-semibold text-amber-800">5.2 Informações Armazenadas</h3>
      <ul class="list-disc list-inside text-amber-900 space-y-1">
        <li>Armazenamos exclusivamente:</li>
        <li>Conteúdo das perguntas feitas ao sistema</li>
        <li>Respostas fornecidas pelo “Tio Ben”</li>
        <li>Data e horário das interações</li>
        <li>Dados técnicos não identificáveis (navegador, sistema operacional)</li>
      </ul>

      <h3 class="text-xl font-semibold text-amber-800">5.3 Finalidade dos Dados</h3>
      <ul class="list-disc list-inside text-amber-900 space-y-1">
        <li>Os dados armazenados são utilizados apenas para:</li>
        <li>Melhoramento do sistema de respostas</li>
        <li>Análise estatística de funcionamento</li>
        <li>Manutenção técnica da plataforma</li>
        <li>Estudos não identificáveis sobre dúvidas frequentes na fé católica</li>
      </ul>

      <h3 class="text-xl font-semibold text-amber-800">5.4 Compartilhamento</h3>
      <ul class="list-disc list-inside text-amber-900 space-y-1">
        <li>NUNCA compartilhamos dados com terceiros para fins comerciais</li>
        <li>NUNCA vendemos ou cedemos informações de usuários</li>
        <li>Dados podem ser acessados apenas pela equipe técnica responsável pelo sistema</li>
      </ul>

      <h2 class="text-2xl font-semibold text-amber-800">6. Fundamentação Doutrinária</h2>
      <h3 class="text-xl font-semibold text-amber-800">6.1 Fontes Oficiais</h3>
      <ul class="list-disc list-inside text-amber-900 space-y-1">
        <li>Nossas respostas baseiam-se exclusivamente em:</li>
        <li>Sagrada Escritura (Bíblia Católica)</li>
        <li>Catecismo da Igreja Católica</li>
        <li>Documentos dos Concílios Ecumênicos</li>
        <li>Encíclicas, Exortações e demais documentos pontifícios</li>
        <li>Tradição apostólica reconhecida pela Igreja</li>
      </ul>

      <h3 class="text-xl font-semibold text-amber-800">6.2 Autoridade Magisterial</h3>
      <ul class="list-disc list-inside text-amber-900 space-y-1">
        <li>Reconhecemos a autoridade suprema do Papa e dos bispos em comunhão com ele</li>
        <li>Em caso de conflito interpretativo, prevalece sempre o Magistério oficial da Igreja</li>
        <li>Recomendamos sempre consultar fontes primárias e autoridades eclesiásticas locais</li>
      </ul>

      <h2 class="text-2xl font-semibold text-amber-800">7. Isenção de Responsabilidade</h2>
      <h3 class="text-xl font-semibold text-amber-800">7.1 Decisões Pessoais</h3>
      <ul class="list-disc list-inside text-amber-900 space-y-1">
        <li>O usuário é inteiramente responsável por suas decisões e ações</li>
        <li>As orientações fornecidas não substituem o discernimento pessoal e a oração</li>
        <li>Decisões importantes devem sempre contar com acompanhamento presencial qualificado</li>
      </ul>

      <h3 class="text-xl font-semibold text-amber-800">7.2 Consequências</h3>
      <ul class="list-disc list-inside text-amber-900 space-y-1">
        <li>O iatioben.com.br não se responsabiliza por consequências de decisões tomadas com base exclusivamente nas respostas fornecidas</li>
        <li>Não assumimos responsabilidade por interpretações inadequadas ou uso indevido das orientações</li>
        <li>Cada usuário deve exercer prudência cristã no discernimento das orientações recebidas</li>
      </ul>

      <h2 class="text-2xl font-semibold text-amber-800">8. Alterações nos Termos</h2>
      <ul class="list-disc list-inside text-amber-900 space-y-1">
        <li>Este termo pode ser atualizado periodicamente para melhor servir aos usuários</li>
        <li>Alterações significativas serão comunicadas na página principal do site</li>
        <li>O uso continuado após alterações implica na aceitação dos novos termos</li>
      </ul>

      <h2 class="text-2xl font-semibold text-amber-800">9. Contato e Dúvidas</h2>
      <p class="text-amber-900">
        Para esclarecimentos sobre este termo ou sobre o funcionamento do serviço, consulte as informações de contato disponíveis no site.
      </p>

      <h2 class="text-2xl font-semibold text-amber-800">10. Disposições Finais</h2>
      <p class="text-amber-900">
        Este termo de responsabilidade é regido pelas leis brasileiras. Ao utilizar o iatioben.com.br, você declara ter lido, compreendido e aceito integralmente estas condições.
      </p>

      <p class="text-amber-900 italic">
        “Tudo o que fizerdes, fazei-o em nome do Senhor Jesus, dando graças a Deus Pai por meio dele.” (Colossenses 3:17)
      </p>

      <p class="text-amber-900 mt-6 italic">
        *Que Nossa Senhora interceda por todos que buscam crescer na fé através desta ferramenta, e que o Espírito Santo conduza sempre nossos corações à verdade plena em Jesus Cristo.*
      </p>
    </div>
  </div>
</section>
@endsection
