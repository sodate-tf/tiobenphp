(function () {
  window.ROSARY_ENGINE = window.ROSARY_ENGINE || {};

  /**
   * buildRosarySteps({ set, mode, singleMysteryIndex, includeClosing, lang })
   * - Labels variam por idioma (pt/en)
   * - Mantém o "prayer" key (openingBundle, hailMary, etc.) igual ao dataset/engine
   */
  window.ROSARY_ENGINE.buildRosarySteps = function (args) {
    args = args || {};

    const mode = args.mode || "full";
    const singleMysteryIndex = args.singleMysteryIndex || 1;
    const includeClosing = args.includeClosing !== false;

    // idioma: prioridade -> args.lang -> window.__ROSARY_INITIAL__.lang -> pt
    const lang =
      (args.lang === "en" || args.lang === "pt")
        ? args.lang
        : ((window.__ROSARY_INITIAL__ && window.__ROSARY_INITIAL__.lang) === "en" ? "en" : "pt");

    const isEn = lang === "en";

    // ===== labels por idioma =====
    const L = isEn
      ? {
          openingBundle: "Opening Prayer (Sign of the Cross + Apostles’ Creed + Offering)",

          hm13_1: "Hail Mary 1/3 (Ask for living faith)",
          hm13_2: "Hail Mary 2/3 (Ask for firm hope)",
          hm13_3: "Hail Mary 3/3 (Ask for burning charity)",

          gloryFatimaOpening: "Glory Be + Fatima Prayer (Opening)",

          ourFatherDecade: (dec) => "Our Father (Decade " + dec + ")",
          hailMaryDecade: (b, dec) => "Hail Mary " + b + "/10 (Decade " + dec + ")",
          gloryFatimaDecade: (dec) => "Glory Be + Fatima Prayer (Decade " + dec + ")",

          spacer: "Separator",

          hailHolyQueen: "Hail Holy Queen",
          finalPrayer: "Final Prayer",
        }
      : {
          openingBundle: "Oração Inicial (Sinal da Cruz + Credo + Oferecimento)",

          hm13_1: "Ave-Maria 1/3 (Pedir Fé viva)",
          hm13_2: "Ave-Maria 2/3 (Pedir Esperança firme)",
          hm13_3: "Ave-Maria 3/3 (Pedir Caridade ardente)",

          gloryFatimaOpening: "Glória + Oração de Fátima (Abertura)",

          ourFatherDecade: (dec) => "Pai-Nosso (Dezena " + dec + ")",
          hailMaryDecade: (b, dec) => "Ave-Maria " + b + "/10 (Dezena " + dec + ")",
          gloryFatimaDecade: (dec) => "Glória + Oração de Fátima (Dezena " + dec + ")",

          spacer: "Separador",

          hailHolyQueen: "Salve Rainha",
          finalPrayer: "Oração Final",
        };

    const steps = [];
    let i = 0;

    const push = (s) => steps.push(Object.assign({ index: i++ }, s));

    // ===== ABERTURA (unificada) =====
    push({
      kind: "bead",
      prayer: "openingBundle",
      label: L.openingBundle,
      phase: "opening",
      beadStyle: "cross",
    });

    // 3 Ave-Marias / 3 Hail Marys (Fé, Esperança, Caridade)
    push({
      kind: "bead",
      prayer: "hailMary",
      label: L.hm13_1,
      phase: "opening",
    });
    push({
      kind: "bead",
      prayer: "hailMary",
      label: L.hm13_2,
      phase: "opening",
    });
    push({
      kind: "bead",
      prayer: "hailMary",
      label: L.hm13_3,
      phase: "opening",
    });

    // Glória + Fátima (Abertura)
    push({
      kind: "bead",
      prayer: "gloryFatima",
      label: L.gloryFatimaOpening,
      phase: "opening",
      beadStyle: "knot",
    });

    // ===== DEZENAS =====
    const decades = mode === "single" ? [singleMysteryIndex] : [1, 2, 3, 4, 5];

    for (const dec of decades) {
      push({
        kind: "bead",
        prayer: "ourFather",
        label: L.ourFatherDecade(dec),
        phase: "decade",
        decadeIndex: dec,
        mysteryIndex: dec,
      });

      for (let b = 1; b <= 10; b++) {
        push({
          kind: "bead",
          prayer: "hailMary",
          label: L.hailMaryDecade(b, dec),
          phase: "decade",
          decadeIndex: dec,
          beadInDecade: b,
          mysteryIndex: dec,
        });
      }

      push({
        kind: "bead",
        prayer: "gloryFatima",
        label: L.gloryFatimaDecade(dec),
        phase: "decade",
        decadeIndex: dec,
        mysteryIndex: dec,
        beadStyle: "knot",
      });

      push({
        kind: "spacer",
        prayer: "ourFather",
        label: L.spacer,
        phase: "decade",
      });
    }

    // ===== ENCERRAMENTO =====
    if (includeClosing) {
      push({
        kind: "bead",
        prayer: "hailHolyQueen",
        label: L.hailHolyQueen,
        phase: "closing",
      });
      push({
        kind: "bead",
        prayer: "finalPrayer",
        label: L.finalPrayer,
        phase: "closing",
      });
    }

    return steps;
  };
})();
