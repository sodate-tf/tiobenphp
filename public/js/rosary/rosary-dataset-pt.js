(function () {
  window.ROSARY_DATASET = window.ROSARY_DATASET || {};

  const PRAYERS = {
    openingBundle: {
      title: "Oração Inicial (Sinal da Cruz + Credo + Oferecimento)",
      text:
`Sinal da Cruz
Em nome do Pai, do Filho e do Espírito Santo. Amém.

Creio (Símbolo dos Apóstolos)
Creio em Deus Pai todo-poderoso, Criador do céu e da terra; e em Jesus Cristo, seu único Filho, nosso Senhor; que foi concebido pelo poder do Espírito Santo; nasceu da Virgem Maria; padeceu sob Pôncio Pilatos, foi crucificado, morto e sepultado; desceu à mansão dos mortos; ressuscitou ao terceiro dia; subiu aos céus; está sentado à direita de Deus Pai todo-poderoso, donde há de vir a julgar os vivos e os mortos. Creio no Espírito Santo; na santa Igreja Católica; na comunhão dos santos; na remissão dos pecados; na ressurreição da carne; na vida eterna. Amém.

Oferecimento do Terço
Divino Jesus, nós Vos oferecemos este terço que vamos rezar, meditando nos mistérios da Vossa Redenção. Concedei-nos, por intercessão da Virgem Maria, Mãe de Deus e nossa Mãe, as virtudes que nos são necessárias para bem rezá-lo e a graça de ganharmos as indulgências desta santa devoção. Amém.`
    },

    ourFather: {
      title: "Pai-Nosso",
      text:
`Pai nosso que estais nos céus,
santificado seja o vosso nome;
venha a nós o vosso reino;
seja feita a vossa vontade,
assim na terra como no céu.
O pão nosso de cada dia nos dai hoje;
perdoai-nos as nossas ofensas,
assim como nós perdoamos a quem nos tem ofendido;
e não nos deixeis cair em tentação,
mas livrai-nos do mal. Amém.`
    },

    hailMary: {
      title: "Ave-Maria",
      text:
`Ave Maria, cheia de graça, o Senhor é convosco;
bendita sois vós entre as mulheres
e bendito é o fruto do vosso ventre, Jesus.
Santa Maria, Mãe de Deus,
rogai por nós, pecadores,
agora e na hora da nossa morte. Amém.`
    },

    gloryFatima: {
      title: "Glória + Oração de Fátima",
      text:
`Glória ao Pai, ao Filho e ao Espírito Santo.
Como era no princípio, agora e sempre. Amém.

Ó meu Jesus, perdoai-nos, livrai-nos do fogo do inferno,
levai as almas todas para o céu
e socorrei principalmente as que mais precisarem.`
    },

    hailHolyQueen: {
      title: "Salve Rainha",
      text:
`Salve, Rainha, Mãe de misericórdia, vida, doçura e esperança nossa, salve!
A vós bradamos, os degredados filhos de Eva;
a vós suspiramos, gemendo e chorando neste vale de lágrimas.
Eia, pois, Advogada nossa, esses vossos olhos misericordiosos a nós volvei;
e, depois deste desterro, mostrai-nos Jesus, bendito fruto do vosso ventre.
Ó clemente, ó piedosa, ó doce sempre Virgem Maria.`
    },

    finalPrayer: {
      title: "Oração Final",
      text:
`Rogai por nós, Santa Mãe de Deus,
para que sejamos dignos das promessas de Cristo.

Oremos:
Ó Deus, cujo Filho Unigênito, por sua vida, morte e ressurreição, nos mereceu os prêmios da salvação eterna,
concedei-nos, nós vos pedimos, que, meditando estes mistérios do Santíssimo Rosário da Bem-aventurada Virgem Maria,
imitemos o que eles contêm e alcancemos o que prometem. Por Cristo Nosso Senhor. Amém.`
    },
  };

  const OPENING_HAIL_MARY_MEDITATIONS = {
    faith: {
      title: "1ª Ave-Maria — Pela Fé (crer e confiar)",
      short: "Peça uma fé viva: confiar em Deus quando você ainda não enxerga o caminho inteiro.",
      long:
`Nesta primeira Ave-Maria, peça a graça de crer como Maria creu: uma fé que acolhe a vontade de Deus antes de compreender tudo.
Fé não é apenas “acreditar que Deus existe”, mas entregar a Ele as chaves do coração e das decisões. É confiar na Palavra quando o sentimento oscila, quando a ansiedade pressiona e quando a resposta parece demorar.

Enquanto reza, traga à memória uma área concreta onde você precisa voltar a confiar: uma escolha difícil, um medo recorrente, uma tentação, um relacionamento ou uma fase de vida.
Depois, entregue isso em uma frase simples: “Senhor, eu creio. Aumenta a minha fé”.

Para contemplar:
- Em que eu tenho tentado controlar tudo sozinho?
- Onde Deus está me pedindo um “sim” concreto hoje?

Pedido de graça:
“Jesus, torna a minha fé obediente e constante.”

Propósito:
Hoje, dê um passo real de fé: retome a oração, faça um pedido de perdão, renuncie algo que te afasta de Deus, ou cumpra um dever com amor.`,
      scriptures: [{ ref: "Lc 1,38" }, { ref: "Mc 9,24" }, { ref: "Hb 11,1" }],
    },

    hope: {
      title: "2ª Ave-Maria — Pela Esperança (perseverar e recomeçar)",
      short: "Peça esperança firme: esperar em Deus sem desanimar, mesmo quando a realidade pesa.",
      long:
`Nesta segunda Ave-Maria, peça esperança: a virtude que sustenta a alma quando o presente parece contradizer as promessas de Deus.
Esperança não é otimismo ingênuo. É a certeza de que Deus não falha — e que Ele conduz até quando você não entende.

A esperança cura o desânimo interior que diz: “não vai mudar”, “não adianta”, “eu não consigo”.
Enquanto reza, apresente a Deus o que te cansa: uma preocupação constante, um problema de família, um pecado que se repete, uma ferida que não fecha.
Peça a graça de perseverar: continuar rezando, fazendo o bem e mantendo a dignidade, sem endurecer o coração.

Para contemplar:
- Eu estou desistindo por dentro?
- Eu transformei uma dor em sentença definitiva?

Pedido de graça:
“Senhor, fortalece a minha esperança e me dá constância.”

Propósito:
Hoje, recomece em algo pequeno e concreto: uma rotina de oração, um gesto de reconciliação, um passo de responsabilidade que você vinha adiando.`,
      scriptures: [{ ref: "Rm 5,3-5" }, { ref: "Sl 27(26),14" }, { ref: "Lm 3,21-23" }],
    },

    charity: {
      title: "3ª Ave-Maria — Pela Caridade (amar como Cristo)",
      short: "Peça caridade ardente: amar com paciência, verdade, perdão e doação concreta.",
      long:
`Nesta terceira Ave-Maria, peça caridade: o amor que vem de Deus e muda a forma como você trata as pessoas.
Caridade não é só “ser educado”. É amar de verdade: perdoar, servir, suportar com paciência, corrigir com mansidão, desejar o bem real do outro — mesmo quando custa.

A caridade cura o egoísmo (tudo girar em torno de mim) e também cura o ressentimento (viver reagindo ao mal que recebi).
Enquanto reza, escolha alguém para colocar no coração: uma pessoa querida, alguém difícil, alguém que precisa de ajuda, ou até alguém que você evita.
Peça a graça de amar com obras: “Senhor, ensina-me a amar sem medir”.

Para contemplar:
- Eu faço o bem com alegria ou com cobrança?
- Existe alguém que eu preciso perdoar de forma concreta?

Pedido de graça:
“Jesus, dá-me um coração manso e disponível.”

Propósito:
Hoje, pratique um ato simples de caridade: uma mensagem, uma ajuda concreta, uma oração por quem você evita, um gesto de serviço dentro de casa.`,
      scriptures: [{ ref: "1Cor 13,4-7" }, { ref: "Jo 13,34-35" }, { ref: "Cl 3,12-14" }],
    },
  };

  const MYSTERIES = /* COPIADO 1:1 (mesmo conteúdo) */ {
    gozosos: {
      label: "Mistérios Gozosos (Segunda e Sábado)",
      themeHint: "alegria, humildade, acolhimento, obediência, vida escondida",
      items: [
        {
          index: 1,
          title: "A Anunciação do Senhor",
          shortReflection: "Contemple o “sim” de Maria: a fé que acolhe a vontade de Deus antes de entender tudo.",
          longReflection: "Diante do anúncio do Anjo, Maria não se fecha no medo: ela escuta, pergunta com humildade e confia. Este mistério ensina que a vontade de Deus não é um peso, mas um caminho de vida. A resposta de Maria forma um coração disponível: quando Deus chama, a fé responde.",
          intention: "Peça a graça de dizer “sim” a Deus com fé e prontidão, sem adiar conversões necessárias.",
          scriptures: [{ ref: "Lc 1,26-38" }, { ref: "Is 7,14" }],
        },
        {
          index: 2,
          title: "A Visitação de Maria a Isabel",
          shortReflection: "A caridade tem pressa: Maria leva Cristo e a alegria de Deus visita uma casa.",
          longReflection: "Maria não guarda o dom para si. Ela se põe a caminho e faz brotar alegria: João exulta, Isabel se enche do Espírito. Este mistério educa para uma fé que se torna serviço. Quem carrega Cristo dentro de si leva paz, vida e esperança para os outros.",
          intention: "Peça um coração atento às necessidades dos outros e coragem para servir com alegria.",
          scriptures: [{ ref: "Lc 1,39-56" }, { ref: "Rm 12,10-13" }],
        },
        {
          index: 3,
          title: "O Nascimento de Jesus",
          shortReflection: "Deus escolhe a simplicidade: a manjedoura revela um amor que se aproxima sem impor.",
          longReflection: "Cristo nasce pobre para que ninguém tenha medo de chegar perto. O Salvador entra na história pelo caminho da humildade. Este mistério cura a busca de aparência e nos ensina a reconhecer Deus no cotidiano, no simples e no real. O amor de Deus se torna visível.",
          intention: "Peça humildade e espírito de pobreza para reconhecer Deus nas coisas simples e servi-Lo com alegria.",
          scriptures: [{ ref: "Lc 2,1-20" }, { ref: "Jo 1,14" }],
        },
        {
          index: 4,
          title: "A Apresentação do Menino Jesus no Templo",
          shortReflection: "Entregar a Deus o que é de Deus: a vida como oferta e pertença.",
          longReflection: "Maria e José apresentam Jesus no Templo: gesto de obediência e consagração. Simeão reconhece a salvação e anuncia que o amor verdadeiro também atravessa dor. Este mistério ensina a fidelidade perseverante: Deus cumpre promessas e sustenta a caminhada.",
          intention: "Peça fidelidade e constância na oração, na Igreja e na missão, mesmo quando houver provação.",
          scriptures: [{ ref: "Lc 2,22-35" }, { ref: "Sl 116(115),12-14" }],
        },
        {
          index: 5,
          title: "A Perda e o Encontro de Jesus no Templo",
          shortReflection: "Quando Jesus parece “distante”, procure no lugar certo: na casa do Pai, na Palavra e na oração.",
          longReflection: "Maria sente a dor da ausência e aprende a buscar com perseverança. Encontrar Jesus no Templo ensina que fé não depende de sensações. Este mistério forma um coração firme: quando o silêncio vem, não desistimos; continuamos procurando até reencontrar o Senhor onde Ele se deixa achar.",
          intention: "Peça perseverança quando a fé atravessar silêncio e confiança para retornar ao essencial.",
          scriptures: [{ ref: "Lc 2,41-52" }, { ref: "Pr 3,5-6" }],
        },
      ],
    },

    dolorosos: {
      label: "Mistérios Dolorosos (Terça e Sexta)",
      themeHint: "compaixão, conversão, entrega, paciência, amor redentor",
      items: [
        {
          index: 1,
          title: "A Agonia de Jesus no Horto",
          shortReflection: "Cristo enfrenta o medo em oração: a obediência amorosa vence a fuga.",
          longReflection: "No Getsêmani, Jesus sente o peso da dor e do pecado do mundo, mas não foge: Ele ora e se entrega. A oração não remove a cruz, mas transforma o coração para carregá-la com amor. Este mistério ensina a confiar quando o sofrimento aperta e a dizer: “Seja feita a tua vontade”.",
          intention: "Peça força para rezar nas noites difíceis e coragem para confiar quando a vontade de Deus custar.",
          scriptures: [{ ref: "Mt 26,36-46" }, { ref: "Hb 5,7-9" }],
        },
        {
          index: 2,
          title: "A Flagelação de Jesus",
          shortReflection: "O amor suporta a violência para curar nossas feridas: Cristo se oferece por nós.",
          longReflection: "A flagelação revela a gravidade do pecado e a profundidade da misericórdia. Jesus não devolve ódio; Ele redime. Este mistério educa para a penitência sincera e para a reparação: abandonar o que destrói e buscar a cura em Deus, com humildade e perseverança.",
          intention: "Peça libertação de vícios e desordens; ofereça reparação pelos pecados pessoais e do mundo.",
          scriptures: [{ ref: "Jo 19,1" }, { ref: "Is 53,4-5" }],
        },
        {
          index: 3,
          title: "A Coroação de Espinhos",
          shortReflection: "O mundo zomba, mas Cristo reina com mansidão: a verdadeira glória é amar até o fim.",
          longReflection: "Humilhado e ridicularizado, Jesus permanece manso. Este mistério cura a vaidade e a necessidade de aprovação. Ele nos ensina a não revidar e a não construir identidade sobre aplausos. Em Cristo, descobrimos que a força do amor é maior que a violência do desprezo.",
          intention: "Peça humildade, cura da vaidade e graça para não responder com agressividade quando for ferido.",
          scriptures: [{ ref: "Mt 27,27-31" }, { ref: "Fl 2,5-11" }],
        },
        {
          index: 4,
          title: "Jesus Carrega a Cruz",
          shortReflection: "A cruz não é derrota: é caminho de amor, com quedas e recomeços.",
          longReflection: "No caminho do Calvário, Cristo mostra que salvar é carregar. Ele aceita ajuda, cai e se levanta. Este mistério ensina a unir dores a Jesus, a acolher auxílio quando necessário e a continuar apesar das falhas. A cruz, abraçada com amor, transforma sofrimento em oferta.",
          intention: "Peça paciência e constância para suportar contrariedades e recomeçar sem desanimar.",
          scriptures: [{ ref: "Lc 23,26-32" }, { ref: "Mt 16,24-25" }],
        },
        {
          index: 5,
          title: "A Crucificação e Morte de Jesus",
          shortReflection: "No alto da cruz, Deus nos ama sem medida: nasce a nova vida no perdão.",
          longReflection: "Na cruz, Jesus perdoa, entrega o Espírito e confia Maria à Igreja. O amor redentor transforma a história. Este mistério é escola de misericórdia: perdoar, recomeçar, amar quando custa. A cruz revela o valor de cada pessoa e o poder da graça para salvar.",
          intention: "Peça perdão e reconciliação; ofereça sua vida pela conversão, pela paz e pela salvação das almas.",
          scriptures: [{ ref: "Lc 23,33-46" }, { ref: "Jo 19,25-30" }],
        },
      ],
    },

    gloriosos: {
      label: "Mistérios Gloriosos (Quarta e Domingo)",
      themeHint: "esperança, vitória sobre o pecado, vida nova, Espírito Santo",
      items: [
        {
          index: 1,
          title: "A Ressurreição de Jesus",
          shortReflection: "A vida vence: a esperança nasce do encontro com o Ressuscitado.",
          longReflection: "A Ressurreição é o fundamento da fé: Cristo derrota o pecado e a morte. Este mistério cura o desespero e reacende a esperança: Deus tem a última palavra. Viver como ressuscitado é abandonar o que mata por dentro e escolher a vida, a verdade e a caridade.",
          intention: "Peça alegria pascal e fé firme para não ser dominado pelo medo e pelo desânimo.",
          scriptures: [{ ref: "Mt 28,1-10" }, { ref: "1Cor 15,3-8" }],
        },
        {
          index: 2,
          title: "A Ascensão do Senhor",
          shortReflection: "Cristo abre o caminho do céu e nos envia: o discípulo vive em missão.",
          longReflection: "Jesus sobe aos céus e não nos abandona: Ele inaugura nosso destino e confia a missão à Igreja. Este mistério educa para olhar o alto sem fugir do mundo. A fé madura transforma-se em testemunho: viver o Evangelho com coragem, fidelidade e humildade.",
          intention: "Peça espírito missionário e coragem para testemunhar a fé com coerência no cotidiano.",
          scriptures: [{ ref: "At 1,6-11" }, { ref: "Mt 28,18-20" }],
        },
        {
          index: 3,
          title: "A Vinda do Espírito Santo (Pentecostes)",
          shortReflection: "O Espírito transforma medo em coragem e faz a Igreja nascer em saída.",
          longReflection: "Em Pentecostes, o Espírito Santo enche a Igreja e dá dons para servir. Este mistério ensina docilidade: escutar, discernir e agir com caridade. Onde o Espírito age, nasce unidade, coragem e perseverança. Peça que Ele governe suas palavras, decisões e relações.",
          intention: "Peça docilidade ao Espírito Santo e os dons necessários para sua vocação, sua família e sua missão.",
          scriptures: [{ ref: "At 2,1-13" }, { ref: "Gl 5,22-25" }],
        },
        {
          index: 4,
          title: "A Assunção de Maria",
          shortReflection: "Maria é sinal de esperança: Deus cumpre promessas e eleva os humildes.",
          longReflection: "Na Assunção, contemplamos o destino preparado por Deus para seus filhos: a vida plena. Maria antecipa na própria pessoa a vitória final da graça. Este mistério purifica tristezas e fortalece a esperança: a história não termina no sofrimento, mas na glória de Deus.",
          intention: "Peça esperança, pureza e a graça de viver com o coração no céu, sem abandonar a fidelidade no dia a dia.",
          scriptures: [{ ref: "Ap 12,1-6" }, { ref: "Lc 1,46-55" }],
        },
        {
          index: 5,
          title: "A Coroação de Maria como Rainha",
          shortReflection: "A humildade é exaltada: Maria reina servindo e intercedendo por nós.",
          longReflection: "A Coroação revela a lógica do Reino: quem se faz pequeno é elevado. Maria, serva fiel, é reconhecida como Mãe e Rainha, não para substituir Cristo, mas para conduzir até Ele. Este mistério forma perseverança: confiar, rezar e caminhar com a Igreja.",
          intention: "Peça confiança na intercessão de Maria, perseverança na oração e amor fiel à Igreja.",
          scriptures: [{ ref: "Ap 12,1" }, { ref: "Jo 2,1-11" }],
        },
      ],
    },

    luminosos: {
      label: "Mistérios Luminosos (Quinta)",
      themeHint: "luz, revelação, discipulado, Eucaristia, conversão",
      items: [
        {
          index: 1,
          title: "O Batismo de Jesus no Jordão",
          shortReflection: "O Pai revela o Filho e o Espírito desce: recordar o batismo é lembrar quem você é.",
          longReflection: "No Jordão, Jesus se solidariza conosco e inaugura sua missão. O Pai declara: “Este é meu Filho amado”. Este mistério catequiza a identidade cristã: pelo batismo, somos chamados à santidade. Recorde suas promessas: renunciar ao pecado e viver como filho.",
          intention: "Peça fidelidade às promessas do batismo e conversão diária para viver com coerência.",
          scriptures: [{ ref: "Mt 3,13-17" }, { ref: "Rm 6,3-4" }],
        },
        {
          index: 2,
          title: "As Bodas de Caná",
          shortReflection: "Maria intercede e Jesus transforma água em vinho: Deus renova o amor e a esperança.",
          longReflection: "Em Caná, Maria percebe a necessidade e apresenta a Jesus. E a palavra decisiva é: “Fazei tudo o que Ele vos disser”. Este mistério ensina obediência prática e confiança na intercessão de Maria. Deus muda a qualidade da vida quando O deixamos agir.",
          intention: "Peça renovação da fé na sua casa e obediência concreta à Palavra de Jesus.",
          scriptures: [{ ref: "Jo 2,1-11" }, { ref: "Sl 34(33),9" }],
        },
        {
          index: 3,
          title: "O Anúncio do Reino e o Convite à Conversão",
          shortReflection: "O Reino está próximo: o Evangelho pede mudança real de vida e misericórdia.",
          longReflection: "Jesus anuncia o Reino e cura. Ele não só informa: transforma. Este mistério ensina conversão concreta, combate ao pecado e retorno à misericórdia. Onde Cristo reina, nasce paz e retidão. Peça coragem para abandonar o que te afasta de Deus.",
          intention: "Peça arrependimento sincero e força para mudar hábitos e escolhas que te afastam de Deus.",
          scriptures: [{ ref: "Mc 1,14-15" }, { ref: "Lc 15,1-7" }],
        },
        {
          index: 4,
          title: "A Transfiguração do Senhor",
          shortReflection: "A luz sustenta a fé: escutar Jesus é o caminho para atravessar a cruz.",
          longReflection: "Na Transfiguração, a glória aparece para fortalecer o coração dos discípulos. O Pai ordena: “Escutai-O”. Este mistério ensina que a oração nos transfigura por dentro, e que a luz de Deus não elimina a cruz, mas dá sentido para perseverar com fidelidade.",
          intention: "Peça fé luminosa, escuta profunda e perseverança na oração para ser transformado por Deus.",
          scriptures: [{ ref: "Lc 9,28-36" }, { ref: "2Pd 1,16-18" }],
        },
        {
          index: 5,
          title: "A Instituição da Eucaristia",
          shortReflection: "Jesus se faz alimento: a Eucaristia é presença real e centro da vida cristã.",
          longReflection: "No Cenáculo, Cristo se entrega em corpo e sangue: amor até o fim. Este mistério catequiza a vida eucarística: adorar, comungar com reverência, buscar reconciliação e tornar-se “pão” para os outros. Quem vive da Eucaristia aprende a amar com constância.",
          intention: "Peça amor à Eucaristia, reverência e sede de santidade para viver uma fé mais profunda.",
          scriptures: [{ ref: "Lc 22,14-20" }, { ref: "Jo 6,51-58" }],
        },
      ],
    },
  };

  function getDefaultMysterySetForWeekday(d) {
    const day = d.getDay();
    if (day === 1 || day === 6) return "gozosos";
    if (day === 2 || day === 5) return "dolorosos";
    if (day === 3 || day === 0) return "gloriosos";
    return "luminosos";
  }

  function getFinalSuggestionBySet(setKey) {
    switch (setKey) {
      case "gozosos":
        return "Pesquise: “Como viver o ‘sim’ de Maria no dia a dia” e “virtudes da vida escondida em Nazaré”.";
      case "dolorosos":
        return "Pesquise: “Como unir sofrimento à cruz de Cristo” e “o perdão cristão na prática”.";
      case "gloriosos":
        return "Pesquise: “Ressurreição e esperança cristã” e “dons do Espírito Santo no cotidiano”.";
      default:
        return "Pesquise: “mistérios luminosos e vida eucarística” e “o que é conversão segundo o Evangelho”.";
    }
  }

  window.ROSARY_DATASET.pt = {
    PRAYERS,
    MYSTERIES,
    OPENING_HAIL_MARY_MEDITATIONS,
    getDefaultMysterySetForWeekday,
    getFinalSuggestionBySet,
  };
})();
