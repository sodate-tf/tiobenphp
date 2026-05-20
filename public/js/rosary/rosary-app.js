/* public/js/rosary/rosary-app.js */
(function () {
  const root = document.getElementById("rosary-app");
  if (!root) return;

  const initial = window.__ROSARY_INITIAL__ || { lang: "pt", route: "pt", setKey: "gozosos" };

  function fallbackBox(lang) {
    const t =
      lang === "en"
        ? "Rosary interactive guide is unavailable right now."
        : "O terço interativo está indisponível no momento.";
    return `
      <div class="p-6">
        <div class="rounded-2xl border border-amber-200 bg-white p-5">
          <p class="text-gray-900 font-semibold">${t}</p>
        </div>
      </div>
    `;
  }

  // Boot resiliente: espera dataset + engine existirem (evita race condition com defer/cache)
  function bootWhenReady(attempt = 0) {
    try {
      const ds = (window.ROSARY_DATASET && window.ROSARY_DATASET[initial.lang]) || null;
      const engine = window.ROSARY_ENGINE;

      if (!ds || !engine || typeof engine.buildRosarySteps !== "function") {
        if (attempt < 25) return requestAnimationFrame(() => bootWhenReady(attempt + 1));
        root.innerHTML = fallbackBox(initial.lang);
        return;
      }

      startApp({ ds, engine });
    } catch (err) {
      console.error("[rosary-app] boot error:", err);
      root.innerHTML = fallbackBox(initial.lang);
    }
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => bootWhenReady(0), { once: true });
  } else {
    bootWhenReady(0);
  }

  function startApp({ ds, engine }) {
    const isPt = initial.lang === "pt";

    // Estado
    const state = {
      setKey: initial.setKey || ds.getDefaultMysterySetForWeekday(new Date()),
      mode: "full",
      singleMysteryIndex: 1,
      includeClosing: true,
      hasInteracted: false,
      openPicker: false,
      openManual: false,
      mobileTab: "prayer",
      current: 0,
      stickyH: 0,
    };

    function steps() {
      return engine.buildRosarySteps({
        set: state.setKey,
        mode: state.mode,
        singleMysteryIndex: state.singleMysteryIndex,
        includeClosing: state.includeClosing,
      });
    }

    function currentStep() {
      const s = steps();
      return s[state.current] || s[0];
    }

    function actionLabel(prayerKey, isLast) {
      if (isLast) return isPt ? "Finalizar" : "Finish";
      if (prayerKey === "openingBundle") return isPt ? "Começar" : "Start";
      if (prayerKey === "hailMary") return isPt ? "Rezei" : "Done";
      if (prayerKey === "gloryFatima") return isPt ? "Concluir dezena" : "Complete decade";
      if (prayerKey === "hailHolyQueen" || prayerKey === "finalPrayer") return isPt ? "Finalizar" : "Finish";
      return isPt ? "Rezei / Próxima" : "Done / Next";
    }

    function slugFromSetKeyPt(k) {
      return (
        {
          gozosos: "misterios-gozosos",
          dolorosos: "misterios-dolorosos",
          gloriosos: "misterios-gloriosos",
          luminosos: "misterios-luminosos",
        }[k] || "misterios-luminosos"
      );
    }

    function slugFromSetKeyEn(k) {
      return (
        {
          gozosos: "joyful-mysteries",
          dolorosos: "sorrowful-mysteries",
          gloriosos: "glorious-mysteries",
          luminosos: "luminous-mysteries",
        }[k] || "luminous-mysteries"
      );
    }

    function navigateToSet(k) {
      const slug = initial.route === "en" ? slugFromSetKeyEn(k) : slugFromSetKeyPt(k);
      const base = (initial.route === "en") ? "/en/rosary" : "/santo-terco";
      const target = `${base}/${slug}`;
      if (location.pathname === target) return;
      history.pushState({}, "", target);
    }

    function goNext() {
      state.hasInteracted = true;
      const s = steps();
      let n = Math.min(state.current + 1, s.length - 1);
      while (n < s.length - 1 && s[n] && s[n].kind === "spacer") n++;
      state.current = n;
      render();
    }

    function goPrev() {
      state.hasInteracted = true;
      const s = steps();
      let n = Math.max(state.current - 1, 0);
      while (n > 0 && s[n] && s[n].kind === "spacer") n--;
      state.current = n;
      render();
    }

    function reset() {
      state.current = 0;
      state.hasInteracted = false;
      state.mobileTab = "prayer";
      render();
    }

    // Sticky height
    const ro = new ResizeObserver(() => {
      const sticky = document.getElementById("rosary-sticky");
      if (!sticky) return;
      state.stickyH = Math.max(0, Math.round(sticky.getBoundingClientRect().height));
      const pad = document.getElementById("rosary-pad");
      if (pad) {
        const topPad = Math.max(12, Math.round(state.stickyH * 0.06));
        pad.style.paddingTop = `${topPad}px`;
      }
    });

    // Popstate: se você usar pushState para trocar slug, recarrega para pegar __ROSARY_INITIAL__ novo do servidor
    window.addEventListener("popstate", () => location.reload());

    function render() {
      const s = steps();
      const step = currentStep();
      const prayer = ds.PRAYERS[step.prayer] || { title: "", text: "" };
      const isFirst = state.current === 0;
      const isLast = state.current === s.length - 1;
      const nextLabel = actionLabel(step.prayer, isLast);

      root.innerHTML = templateApp({
        isPt,
        ds,
        state,
        steps: s,
        step,
        prayer,
        isFirst,
        isLast,
        nextLabel,
      });

      bindUI({ state, goNext, goPrev, reset, navigateToSet, render });

      const sticky = document.getElementById("rosary-sticky");
      if (sticky) ro.observe(sticky);
    }

    // -------------- templates --------------

    function rosaryTheme(setKey) {
      const map = {
        gozosos: {
          accentBg: "bg-rose-600",
          accentHover: "hover:bg-rose-700",
          accentText: "text-rose-700",
          beadFill: "bg-rose-600",
          beadBorder: "border-rose-200",
          softBg: "bg-rose-50",
          ring: "ring-rose-200",
          track: "bg-rose-200",
          trackShadow: "shadow-rose-200/50",
        },
        dolorosos: {
          accentBg: "bg-red-900",
          accentHover: "hover:bg-red-950",
          accentText: "text-red-900",
          beadFill: "bg-red-900",
          beadBorder: "border-red-200",
          softBg: "bg-red-50",
          ring: "ring-red-200",
          track: "bg-red-200",
          trackShadow: "shadow-red-200/50",
        },
        gloriosos: {
          accentBg: "bg-blue-700",
          accentHover: "hover:bg-blue-800",
          accentText: "text-blue-700",
          beadFill: "bg-blue-700",
          beadBorder: "border-blue-200",
          softBg: "bg-blue-50",
          ring: "ring-blue-200",
          track: "bg-blue-200",
          trackShadow: "shadow-blue-200/50",
        },
        luminosos: {
          accentBg: "bg-sky-600",
          accentHover: "hover:bg-sky-700",
          accentText: "text-sky-700",
          beadFill: "bg-sky-600",
          beadBorder: "border-sky-200",
          softBg: "bg-sky-50",
          ring: "ring-sky-200",
          track: "bg-sky-200",
          trackShadow: "shadow-sky-200/50",
        },
      };
      return map[setKey] || map.luminosos;
    }

    function joinClass(...xs) {
      return xs.filter(Boolean).join(" ");
    }

    function weekdayLabel(isPt) {
      const d = new Date();
      return d
        .toLocaleDateString(isPt ? "pt-BR" : "en-US", { weekday: "long" })
        .replace(/^\w/, (c) => c.toUpperCase());
    }

    function labelForSet(ctx) {
      const set = ctx.state.setKey;
      const labelsPt = {
        gozosos: "Mistérios Gozosos",
        dolorosos: "Mistérios Dolorosos",
        gloriosos: "Mistérios Gloriosos",
        luminosos: "Mistérios Luminosos",
      };
      const labelsEn = {
        gozosos: "Joyful Mysteries",
        dolorosos: "Sorrowful Mysteries",
        gloriosos: "Glorious Mysteries",
        luminosos: "Luminous Mysteries",
      };
      return (ctx.isPt ? labelsPt[set] : labelsEn[set]) || (ctx.isPt ? "Mistérios" : "Mysteries");
    }

    function progressLabel(ctx) {
      const s = ctx.steps;
      const i = ctx.state.current;
      const step = s[i];
      const base = (ctx.isPt ? "Passo" : "Step") + ` ${i + 1}/${s.length}`;

      if (step.phase === "opening") return `${base} • ${ctx.isPt ? "Abertura" : "Opening"}`;
      if (step.phase === "closing") return `${base} • ${ctx.isPt ? "Encerramento" : "Closing"}`;

      const dec = step.decadeIndex || 1;
      const bead = step.beadInDecade;
      if (bead) {
        return `${base} • ${ctx.isPt ? "Dezena" : "Decade"} ${dec}/5 • ${ctx.isPt ? "Ave-Maria" : "Hail Mary"} ${bead}/10`;
      }
      return `${base} • ${ctx.isPt ? "Dezena" : "Decade"} ${dec}/5`;
    }

    // -------- Timeline beads --------

    function buildBeadMap(allSteps) {
      const s = allSteps || [];
      const firstNonSpacer = s.findIndex((st) => st && st.kind !== "spacer");
      const crossIndex = firstNonSpacer >= 0 ? firstNonSpacer : 0;

      const byDecade = { 1: {}, 2: {}, 3: {}, 4: {}, 5: {} };

      for (let i = 0; i < s.length; i++) {
        const st = s[i];
        if (!st || st.kind === "spacer") continue;
        const d = st.decadeIndex;
        if (!d || d < 1 || d > 5) continue;

        if (st.prayer === "ourFather") {
          if (byDecade[d].ourFather == null) byDecade[d].ourFather = i;
        }

        if (st.prayer === "hailMary") {
          const bead = st.beadInDecade;
          if (bead >= 1 && bead <= 10) {
            byDecade[d].hailMary = byDecade[d].hailMary || {};
            if (byDecade[d].hailMary[bead] == null) byDecade[d].hailMary[bead] = i;
          }
        }

        if (st.prayer === "gloryFatima") {
          if (byDecade[d].glory == null) byDecade[d].glory = i;
        }
      }

      const beads = [];
      beads.push({ kind: "cross", stepIndex: crossIndex });

      for (let d = 1; d <= 5; d++) {
        const dec = byDecade[d];
        if (dec.ourFather != null) beads.push({ kind: "ourFather", stepIndex: dec.ourFather, decade: d });

        for (let b = 1; b <= 10; b++) {
          const idx = dec.hailMary && dec.hailMary[b];
          if (idx != null) beads.push({ kind: "hailMary", stepIndex: idx, decade: d, bead: b });
        }

        if (dec.glory != null) beads.push({ kind: "glory", stepIndex: dec.glory, decade: d });

        if (d < 5) beads.push({ kind: "knot", stepIndex: null, decade: d });
      }

      return beads;
    }

    function activeBeadIndex(beads, currentStepIndex) {
      let best = 0;
      for (let i = 0; i < beads.length; i++) {
        const b = beads[i];
        if (b.kind === "knot") continue;
        if (typeof b.stepIndex === "number" && b.stepIndex <= currentStepIndex) best = i;
      }
      return best;
    }

    function templateTimeline(ctx) {
      const theme = rosaryTheme(ctx.state.setKey);
      const beads = buildBeadMap(ctx.steps);
      const activeIdx = activeBeadIndex(beads, ctx.state.current);

      function beadClass(kind, active) {
        const base =
          "shrink-0 inline-flex items-center justify-center select-none " +
          "focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 ring-offset-white transition";

        if (kind === "knot") {
          return joinClass(
            "shrink-0 rounded-full",
            "h-2.5 w-2.5 sm:h-3 sm:w-3",
            theme.beadFill,
            "opacity-70"
          );
        }

        const activeRing = active ? `ring-2 ${theme.ring}` : "ring-0";
        const fill = active ? `${theme.beadFill} text-white border-transparent` : `bg-white text-gray-900 border ${theme.beadBorder}`;
        const hover = active ? "" : "hover:bg-amber-50";

        const size =
          {
            cross: "h-10 w-10 sm:h-11 sm:w-11",
            ourFather: "h-8 w-8 sm:h-9 sm:w-9",
            hailMary: "h-6 w-6 sm:h-7 sm:w-7",
            glory: "h-5 w-11 sm:h-6 sm:w-14 rounded-xl",
          }[kind] || "h-6 w-6 sm:h-7 sm:w-7";

        const shape = kind === "glory" ? "" : "rounded-full";

        return joinClass(base, size, shape, fill, hover, activeRing);
      }

      const crossSvg = `
        <svg viewBox="0 0 24 24" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <path d="M12 3v18" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
          <path d="M7 8h10" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
        </svg>
      `;

      function gloryGroove(active) {
        return `<span class="block h-[2px] w-[65%] rounded-full ${active ? "bg-white/75" : "bg-gray-300"}"></span>`;
      }

      return `
        <div class="rounded-2xl border border-amber-200 ${theme.softBg} p-3">
          <div class="relative">
            <div class="absolute left-2 right-2 top-1/2 -translate-y-1/2 h-[4px] ${theme.track} ${theme.trackShadow} rounded-full opacity-80"></div>
            <div class="absolute left-2 right-2 top-1/2 -translate-y-1/2 h-[1px] bg-white/70 rounded-full"></div>

            <div class="relative flex items-center gap-2 overflow-x-auto no-scrollbar py-2">
              ${beads
                .map((b, idx) => {
                  const active = idx === activeIdx;

                  if (b.kind === "knot") {
                    return `
                      <span class="mx-1 inline-flex items-center" aria-hidden="true">
                        <span class="${beadClass("knot", false)}"></span>
                      </span>
                    `;
                  }

                  const aria =
                    b.kind === "cross"
                      ? ctx.isPt
                        ? "Cruz"
                        : "Cross"
                      : b.kind === "ourFather"
                        ? ctx.isPt
                          ? "Pai-Nosso"
                          : "Our Father"
                        : b.kind === "hailMary"
                          ? ctx.isPt
                            ? "Ave-Maria"
                            : "Hail Mary"
                          : ctx.isPt
                            ? "Glória"
                            : "Glory";

                  const content =
                    b.kind === "cross"
                      ? crossSvg
                      : b.kind === "glory"
                        ? gloryGroove(active)
                        : b.kind === "ourFather"
                          ? `<span class="text-[11px] sm:text-[12px] font-extrabold">P</span>`
                          : `<span class="text-[10px] sm:text-[11px] font-extrabold">${b.bead || 0}</span>`;

                  return `
                    <button
                      data-action="goto-step"
                      data-step="${b.stepIndex}"
                      type="button"
                      class="${beadClass(b.kind, active)}"
                      aria-label="${escapeHtml(aria)}">
                      ${content}
                    </button>
                  `;
                })
                .join("")}
            </div>
          </div>

          <div class="mt-2 text-[11px] text-gray-700 flex items-center justify-between gap-3">
            <span class="truncate">${progressLabel(ctx)}</span>
            <span class="truncate ${theme.accentText} font-semibold">${labelForSet(ctx)}</span>
          </div>
        </div>
      `;
    }

    function templateMainPanels(ctx) {
      const tab = ctx.state.mobileTab;
      const prayer = ctx.prayer;

      return `
        <p class="sr-only" aria-live="polite">
          ${ctx.isPt ? "Etapa atual" : "Current step"}: ${escapeHtml(ctx.step.label)}. ${escapeHtml(progressLabel(ctx))}.
        </p>

        <div class="lg:hidden mt-4">
          ${
            tab === "prayer"
              ? `
            <section class="rounded-2xl border border-amber-200 bg-white shadow-sm overflow-hidden">
              <div class="p-4 bg-[#fffaf1] border-b border-amber-100">
                <p class="text-xs font-semibold text-amber-800">${ctx.isPt ? "Oração do momento" : "Current prayer"}</p>
                <h2 class="mt-1 text-lg font-extrabold text-gray-900">${escapeHtml(ctx.step.label)}</h2>
              </div>
              <div class="p-4 font-reading" style="line-height:1.9">
                <div class="rounded-2xl border border-amber-200 bg-[#fffaf1] p-4">
                  <p class="whitespace-pre-line text-gray-950">${escapeHtml(prayer.text)}</p>
                </div>
              </div>
            </section>
            `
              : `
            <section class="rounded-2xl border border-amber-200 bg-white shadow-sm overflow-hidden">
              <div class="p-4 bg-[#fffaf1] border-b border-amber-100">
                <p class="text-xs font-semibold text-amber-800">${ctx.isPt ? "Meditação" : "Meditation"}</p>
                <h3 class="mt-1 text-lg font-extrabold text-gray-900">${ctx.isPt ? "Prepare o coração" : "Prepare your heart"}</h3>
              </div>
              <div class="p-4 font-reading" style="line-height:1.9">
                <div class="rounded-2xl border border-amber-200 bg-[#fffaf1] p-4">
                  <p class="text-gray-900">${
                    ctx.isPt
                      ? "Avance para entrar nas dezenas e meditar os mistérios."
                      : "Continue to enter the decades and meditate on the mysteries."
                  }</p>
                </div>
                ${templateFinalSuggestionCTA(ctx)}
              </div>
            </section>
            `
          }
        </div>

        <div class="hidden lg:grid lg:grid-cols-2 lg:gap-4 mt-6">
          <section class="rounded-2xl border border-amber-200 bg-white shadow-sm overflow-hidden">
            <div class="p-5 bg-[#fffaf1] border-b border-amber-100">
              <p class="text-xs font-semibold text-amber-800">${ctx.isPt ? "Oração do momento" : "Current prayer"}</p>
              <h2 class="mt-1 text-xl font-extrabold text-gray-900">${escapeHtml(ctx.step.label)}</h2>
            </div>
            <div class="p-5 font-reading" style="line-height:1.9">
              <p class="whitespace-pre-line text-gray-950">${escapeHtml(prayer.text)}</p>
            </div>
          </section>

          <section class="rounded-2xl border border-amber-200 bg-white shadow-sm overflow-hidden">
            <div class="p-5 bg-[#fffaf1] border-b border-amber-100">
              <p class="text-xs font-semibold text-amber-800">${ctx.isPt ? "Meditação" : "Meditation"}</p>
              <h3 class="mt-1 text-xl font-extrabold text-gray-900">${ctx.isPt ? "Prepare o coração" : "Prepare your heart"}</h3>
            </div>
            <div class="p-5 font-reading" style="line-height:1.9">
              <p class="text-gray-900">${
                ctx.isPt
                  ? "Avance para entrar nas dezenas e meditar os mistérios."
                  : "Continue to enter the decades and meditate on the mysteries."
              }</p>
              ${templateFinalSuggestionCTA(ctx)}
            </div>
          </section>
        </div>
      `;
    }

    function templateFinalSuggestionCTA(ctx) {
      const suggestion = ctx.ds.getFinalSuggestionBySet ? ctx.ds.getFinalSuggestionBySet(ctx.state.setKey) : "";
      const href = ctx.isPt ? "/blog" : "/en/blog";
      return `
        <div class="mt-4 rounded-2xl border border-amber-200 bg-white p-4">
          <p class="text-sm font-semibold text-gray-900">${ctx.isPt ? "Aprofunde com o Tio Ben" : "Go deeper with Tio Ben"}</p>
          <p class="mt-2 text-sm text-gray-800" style="line-height:1.75">
            ${ctx.isPt ? "Sugestão do Tio Ben para buscar no blog:" : "Suggested topic to look up on the blog:"}
            <span class="font-semibold"> ${escapeHtml(String(suggestion || ""))}</span>.
          </p>
          <a href="${href}" class="inline-flex mt-3 rounded-xl bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">
            ${ctx.isPt ? "Ver no blog" : "Open the blog"}
          </a>
        </div>
      `;
    }

    function templateAdSlot(slot, height) {
      return `
        <div class="rounded-2xl border border-amber-200 bg-white p-3" style="min-height:${height}px">
          <div class="text-xs text-gray-500">AdSense slot ${escapeHtml(slot)}</div>
        </div>
      `;
    }

    function templateBottomSheet(ctx) {
      const theme = rosaryTheme(ctx.state.setKey);

      return `
       <div
              class="
                fixed z-40
                left-1/2 -translate-x-1/2
                w-[calc(100%-1.5rem)] sm:w-[calc(100%-2rem)]
                max-w-md md:max-w-lg lg:max-w-xl
              "
              style="
                bottom: 16px;
                padding-bottom: calc(env(safe-area-inset-bottom, 0px) + 12px);
              "
            >
          <div class="px-3">
            <div class="rounded-2xl border border-amber-200 bg-white shadow-xl overflow-hidden" data-sheet="root">
              <button data-action="sheet-toggle" class="w-full flex flex-col items-center py-2 bg-[#fffaf1] border-b border-amber-100" type="button">
                <div class="w-12 h-1.5 bg-amber-300 rounded-full mb-1"></div>
                <div class="text-[11px] text-gray-700">${ctx.isPt ? "Toque para expandir" : "Tap to expand"}</div>
              </button>

              <div class="p-4">
                <div class="flex items-start justify-between gap-3">
                  <div class="min-w-0">
                    <h2 class="text-base sm:text-lg font-extrabold text-gray-900 truncate">${escapeHtml(ctx.prayer.title)}</h2>
                    <p class="text-xs sm:text-sm text-gray-700 mt-1">${escapeHtml(progressLabel(ctx))}</p>
                  </div>

                  <div class="flex shrink-0 items-center gap-2">
                    <button data-action="open-picker" class="px-3 py-2 rounded-xl bg-gray-100 text-gray-900 text-sm border border-gray-200 hover:bg-gray-50" type="button">
                      ${ctx.isPt ? "Mais" : "More"}
                    </button>

                    <button data-action="prev" class="px-3 py-2 rounded-xl bg-gray-100 text-gray-900 text-sm border border-gray-200 hover:bg-gray-50 disabled:opacity-50" ${ctx.isFirst ? "disabled" : ""} type="button">
                      ${ctx.isPt ? "Voltar" : "Back"}
                    </button>

                    <button data-action="next"
                      class="px-3 py-2 rounded-xl ${theme.accentBg} ${theme.accentHover} text-white font-semibold text-sm disabled:opacity-50"
                      ${ctx.isLast ? "disabled" : ""} type="button">
                      ${escapeHtml(ctx.nextLabel || (ctx.isPt ? "Rezei / Próxima" : "Done / Next"))}
                    </button>
                  </div>
                </div>

                <div class="hidden" data-sheet="expanded"></div>
              </div>
            </div>
          </div>
        </div>
      `;
    }

    // ✅ ÚNICO picker modal
    function templatePickerModal(ctx) {
      const theme = rosaryTheme(ctx.state.setKey);
      if (!ctx.state.openPicker) return "";

      const labels = ctx.isPt
        ? [
            { k: "gozosos", l: "Gozosos" },
            { k: "dolorosos", l: "Dolorosos" },
            { k: "gloriosos", l: "Gloriosos" },
            { k: "luminosos", l: "Luminosos" },
          ]
        : [
            { k: "gozosos", l: "Joyful" },
            { k: "dolorosos", l: "Sorrowful" },
            { k: "gloriosos", l: "Glorious" },
            { k: "luminosos", l: "Luminous" },
          ];

      return `
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
          <div class="absolute inset-0 bg-black/40 backdrop-blur-[1px]" data-action="close-picker"></div>

          <div class="relative w-full max-w-md rounded-3xl border border-amber-200 bg-white shadow-2xl overflow-hidden">
            <div class="p-4 border-b border-amber-100 bg-[#fffaf1]">
              <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                  <h3 class="text-base sm:text-lg font-extrabold text-gray-900">
                    ${ctx.isPt ? "Escolher mistérios" : "Choose mysteries"}
                  </h3>
                  <p class="mt-1 text-xs sm:text-sm text-gray-700">
                    ${ctx.isPt ? "Selecione o conjunto e o modo de oração." : "Pick a set and prayer mode."}
                  </p>
                </div>
                <button data-action="close-picker" type="button"
                  class="h-10 w-10 rounded-full border border-gray-200 bg-white text-gray-900 shadow-sm hover:bg-gray-50"
                  aria-label="Close">×</button>
              </div>
            </div>

            <div class="p-4 space-y-4 max-h-[70vh] overflow-y-auto">
              <section class="rounded-2xl border border-amber-200 bg-white p-3">
                <p class="text-sm font-semibold text-gray-900">${ctx.isPt ? "Conjunto" : "Set"}</p>
                <div class="mt-3 grid grid-cols-2 gap-2">
                  ${labels
                    .map((s) => {
                      const active = ctx.state.setKey === s.k;
                      return `
                        <button data-action="set-setKey" data-set="${s.k}" type="button"
                          class="${
                            active
                              ? joinClass(theme.accentBg, "text-white")
                              : "bg-[#fffaf1] text-gray-900 border border-amber-200 hover:bg-amber-50"
                          } rounded-2xl px-3 py-2 text-sm font-semibold transition">
                          ${s.l}
                        </button>
                      `;
                    })
                    .join("")}
                </div>
              </section>

              <section class="rounded-2xl border border-amber-200 bg-white p-3">
                <p class="text-sm font-semibold text-gray-900">${ctx.isPt ? "Modo" : "Mode"}</p>
                <div class="mt-3 grid gap-2">
                  <button data-action="set-mode" data-mode="full" type="button"
                    class="${
                      ctx.state.mode === "full"
                        ? joinClass(theme.accentBg, "text-white")
                        : "bg-[#fffaf1] text-gray-900 border border-amber-200 hover:bg-amber-50"
                    } rounded-2xl px-3 py-2 text-sm font-semibold transition text-left">
                    ${ctx.isPt ? "Terço completo (5 dezenas)" : "Full Rosary (5 decades)"}
                  </button>

                  <button data-action="set-mode" data-mode="single" type="button"
                    class="${
                      ctx.state.mode === "single"
                        ? joinClass(theme.accentBg, "text-white")
                        : "bg-[#fffaf1] text-gray-900 border border-amber-200 hover:bg-amber-50"
                    } rounded-2xl px-3 py-2 text-sm font-semibold transition text-left">
                    ${ctx.isPt ? "Apenas 1 mistério (1 dezena)" : "Single mystery (1 decade)"}
                  </button>
                </div>

                ${
                  ctx.state.mode === "single"
                    ? `
                  <div class="mt-4">
                    <p class="text-sm font-semibold text-gray-900">${ctx.isPt ? "Qual mistério?" : "Which mystery?"}</p>
                    <div class="mt-3 grid grid-cols-5 gap-2">
                      ${[1, 2, 3, 4, 5]
                        .map((n) => {
                          const active = ctx.state.singleMysteryIndex === n;
                          return `
                            <button data-action="set-singleIndex" data-index="${n}" type="button"
                              class="${
                                active
                                  ? joinClass(theme.accentBg, "text-white")
                                  : "bg-[#fffaf1] text-gray-900 border border-amber-200 hover:bg-amber-50"
                              } rounded-2xl py-2 text-sm font-extrabold transition">
                              ${n}
                            </button>
                          `;
                        })
                        .join("")}
                    </div>
                  </div>
                `
                    : ``
                }
              </section>
            </div>

            <div class="p-4 border-t border-amber-100 bg-white flex justify-end">
              <button data-action="close-picker" type="button"
                class="rounded-2xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm hover:bg-gray-50">
                ${ctx.isPt ? "Concluir" : "Done"}
              </button>
            </div>
          </div>
        </div>
      `;
    }

    function templatePrayerManual(ctx) {
      if (!ctx.state.openManual) return "";
      const ORDER = ["openingBundle", "ourFather", "hailMary", "gloryFatima", "hailHolyQueen", "finalPrayer"];

      return `
        <div class="fixed inset-0 z-50" role="dialog" aria-modal="true">
          <div class="absolute inset-0 bg-black/40 backdrop-blur-[1px]" data-action="close-manual"></div>

          <div class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[calc(100%-2rem)] max-w-3xl bg-white rounded-3xl shadow-2xl border border-amber-200 overflow-hidden max-h-[85vh] flex flex-col">
            <div class="p-4 sm:p-6 border-b border-amber-100 bg-white">
              <div class="flex items-start justify-between gap-3">
                <div>
                  <h3 class="text-lg sm:text-xl font-extrabold text-gray-900">${ctx.isPt ? "Manual das orações" : "Prayer guide"}</h3>
                  <p class="mt-1 text-sm text-gray-700">${ctx.isPt ? "Orações tradicionais para acompanhar o Santo Terço." : "Traditional prayers commonly used in the Rosary."}</p>
                </div>
                <button data-action="close-manual" type="button" class="h-10 w-10 rounded-full border border-gray-200 bg-white text-gray-900 shadow-sm hover:bg-gray-50" aria-label="Close">×</button>
              </div>
            </div>

            <div class="p-4 sm:p-6 overflow-y-auto overscroll-contain space-y-4 bg-white">
              ${ORDER.map((k) => {
                const p = ctx.ds.PRAYERS && ctx.ds.PRAYERS[k];
                if (!p) return "";
                return `
                  <section class="rounded-2xl border border-amber-200 bg-[#fffaf1] p-4 sm:p-5 font-reading" style="line-height:1.9">
                    <h4 class="text-base font-extrabold text-amber-800">${escapeHtml(p.title)}</h4>
                    <p class="mt-3 whitespace-pre-line text-gray-900">${escapeHtml(p.text)}</p>
                  </section>
                `;
              }).join("")}
            </div>

            <div class="p-4 sm:p-6 border-t border-amber-100 bg-white flex justify-end">
              <button data-action="close-manual" class="rounded-xl bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-amber-700" type="button">
                ${ctx.isPt ? "Voltar ao terço" : "Back to Rosary"}
              </button>
            </div>
          </div>
        </div>
      `;
    }

    function templateApp(ctx) {
      const theme = rosaryTheme(ctx.state.setKey);

      return `
        <div class="w-full">
          <div id="rosary-sticky" class="sticky top-0 z-40">
            <div class="pt-2">
              <section class="w-full rounded-2xl border border-amber-200 bg-white shadow-xl overflow-hidden">
                <div class="p-4 sm:p-5 bg-[#fffaf1] border-b border-amber-100">
                  <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                      <p class="text-xs font-semibold text-amber-800">${ctx.isPt ? "Hoje" : "Today"}</p>
                      <h1 class="mt-1 font-reading text-2xl sm:text-3xl font-extrabold text-gray-900 truncate">
                        ${weekdayLabel(ctx.isPt)}
                      </h1>

                      <div class="mt-2 flex flex-wrap gap-2">
                        <span class="inline-flex items-center rounded-full border border-amber-200 bg-white px-3 py-1.5 text-[11px] font-semibold ${theme.accentText}">
                          ${labelForSet(ctx)}
                        </span>

                        <a href="${ctx.isPt ? "/liturgia-diaria" : "/en/daily-mass-readings"}"
                          class="inline-flex items-center rounded-full border border-amber-200 bg-white px-3 py-1.5 text-[11px] font-semibold text-gray-900 hover:bg-amber-50">
                          ${ctx.isPt ? "Liturgia" : "Daily Mass Readings"}
                        </a>

                        <a href="${ctx.isPt ? "/santo-terco" : "/en/santo-terco"}"
                          class="inline-flex items-center rounded-full border border-amber-200 bg-white px-3 py-1.5 text-[11px] font-semibold text-gray-900 hover:bg-amber-50">
                          ${ctx.isPt ? "Hub do Terço" : "Rosary Hub"}
                        </a>
                      </div>
                    </div>

                    <div class="flex shrink-0 items-center gap-2">
                      <button data-action="open-picker" class="rounded-xl border border-amber-200 bg-white px-3 py-2 text-xs font-semibold text-gray-900 hover:bg-amber-50" type="button">
                        ${ctx.isPt ? "Mistérios" : "Mysteries"}
                      </button>
                      <button data-action="open-manual" class="rounded-xl border border-amber-200 bg-white px-3 py-2 text-xs font-semibold text-gray-900 hover:bg-amber-50" type="button">
                        ${ctx.isPt ? "Orações" : "Prayers"}
                      </button>
                    </div>
                  </div>
                </div>

                <div class="p-3 sm:p-5">
                  ${templateTimeline(ctx)}

                  <div class="mt-3">
                    <div class="rounded-2xl border border-amber-200 bg-white p-2 shadow-sm">
                      <div class="flex gap-2">
                        <button data-action="tab-prayer" type="button"
                          class="${
                            ctx.state.mobileTab === "prayer"
                              ? joinClass("flex-1 rounded-xl px-3 py-2 text-sm font-extrabold text-white", theme.accentBg, theme.accentHover)
                              : "flex-1 rounded-xl px-3 py-2 text-sm font-extrabold bg-[#fffaf1] text-gray-900 border border-amber-200"
                          }">
                          ${ctx.isPt ? "Oração" : "Prayer"}
                        </button>
                        <button data-action="tab-reflection" type="button"
                          class="${
                            ctx.state.mobileTab === "reflection"
                              ? joinClass("flex-1 rounded-xl px-3 py-2 text-sm font-extrabold text-white", theme.accentBg, theme.accentHover)
                              : "flex-1 rounded-xl px-3 py-2 text-sm font-extrabold bg-[#fffaf1] text-gray-900 border border-amber-200"
                          }">
                          ${ctx.isPt ? "Meditação" : "Meditation"}
                        </button>
                      </div>

                      <div class="mt-2 flex items-center justify-between gap-3 text-[11px] text-gray-700">
                        <span class="truncate">${escapeHtml(progressLabel(ctx))}</span>
                        <button data-action="reset" type="button"
                          class="rounded-xl border border-gray-200 bg-white px-3 py-2 text-[11px] font-semibold text-gray-900 hover:bg-gray-50">
                          ${ctx.isPt ? "Reiniciar" : "Reset"}
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </section>

              <div class="h-3 bg-gradient-to-b from-amber-400/0 to-amber-400"></div>
            </div>
          </div>

          <div id="rosary-pad" class="w-full pb-28">
            ${templateMainPanels(ctx)}
            <div class="mt-4">${templateAdSlot("2156366376", 140)}</div>
            <div class="mt-4">${templateAdSlot("2672028232", 140)}</div>
          </div>

          ${templateBottomSheet(ctx)}
          ${templatePickerModal(ctx)}
          ${templatePrayerManual(ctx)}
        </div>
      `;
    }

    function bindUI(api) {
      const { state, render, goNext, goPrev, reset, navigateToSet } = api;

      root.querySelectorAll("[data-action]").forEach((el) => {
        el.addEventListener("click", (ev) => {
          ev.preventDefault();

          const a = el.getAttribute("data-action");
          if (!a) return;

          if (a === "next") return goNext();
          if (a === "prev") return goPrev();
          if (a === "reset") return reset();

          if (a === "tab-prayer") {
            state.mobileTab = "prayer";
            return render();
          }
          if (a === "tab-reflection") {
            state.mobileTab = "reflection";
            return render();
          }

          if (a === "open-picker") {
            state.openPicker = true;
            return render();
          }
          if (a === "close-picker") {
            state.openPicker = false;
            return render();
          }

          if (a === "open-manual") {
            state.openManual = true;
            return render();
          }
          if (a === "close-manual") {
            state.openManual = false;
            return render();
          }

          if (a === "goto-step") {
            const idx = parseInt(el.getAttribute("data-step") || "0", 10);
            state.hasInteracted = true;
            state.current = Math.max(0, idx);
            return render();
          }

          if (a === "set-setKey") {
            const k = el.getAttribute("data-set");
            if (!k) return;

            state.setKey = k;
            state.current = 0;
            state.hasInteracted = false;
            state.mobileTab = "prayer";

            navigateToSet(k);

            state.openPicker = false;
            return render();
          }

          if (a === "set-mode") {
            const m = el.getAttribute("data-mode");
            state.mode = m === "single" ? "single" : "full";
            state.current = 0;
            state.hasInteracted = false;
            return render();
          }

          if (a === "set-singleIndex") {
            const n = parseInt(el.getAttribute("data-index") || "1", 10);
            state.singleMysteryIndex = Math.min(5, Math.max(1, n));
            state.current = 0;
            state.hasInteracted = false;
            state.openPicker = false;
            return render();
          }

          if (a === "sheet-toggle") return;
        });
      });

      // ESC: registra uma vez globalmente, mas fecha respeitando o state atual
      if (!window.__ROSARY_ESC_BOUND__) {
        window.__ROSARY_ESC_BOUND__ = true;

        document.addEventListener("keydown", (e) => {
          if (e.key !== "Escape") return;

          const hadAny = state.openPicker || state.openManual;
          if (!hadAny) return;

          state.openPicker = false;
          state.openManual = false;
          render();
        });
      }
    }

    function escapeHtml(s) {
      return String(s ?? "")
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
    }

    // GO!
    render();
  }
})();
