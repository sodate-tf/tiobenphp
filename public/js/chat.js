(() => {
  function ready(fn) {
    if (document.readyState === "loading") {
      document.addEventListener("DOMContentLoaded", fn);
    } else {
      fn();
    }
  }

  ready(() => {
    const root = document.getElementById("chat-root");
    if (!root) return;

    const api = root.getAttribute("data-api") || "/api/perguntar";
    const lang = root.getAttribute("data-lang") || "pt";
    const avatar = root.getAttribute("data-avatar") || "/images/logo-amp.webp";

    // ✅ IDs reais do seu Blade (primeira pergunta)
    const firstWrap = document.getElementById("questionBox");
    const firstInput = document.getElementById("questionInput");
    const firstSend = document.getElementById("askBtn");

    // Dock
    const dock = document.getElementById("chat-dock");
    const input = document.getElementById("chat-input");
    const send = document.getElementById("chat-send");

    // CSRF (Laravel) — se não existir, segue sem
    const csrf =
      document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") ||
      document.querySelector('input[name="_token"]')?.value ||
      "";

    // ===== Dock positioning: desktop bottom + mobile above bottom nav (64px)
    function positionDock() {
      if (!dock) return;
      const isMobile = window.matchMedia("(max-width: 767px)").matches;
      dock.style.bottom = isMobile ? "64px" : "0px";
    }
    positionDock();
    window.addEventListener("resize", positionDock);

    // ===== State
    let messages = []; // {role:'user'|'assistant', content:string}
    let isTyping = false;

    function esc(s) {
      return String(s || "")
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
    }

    function formatAssistant(text) {
      return esc(text).replace(/\n/g, "<br/>");
    }

    function render() {
      root.innerHTML = "";

      const wrap = document.createElement("div");
      wrap.className = "flex flex-col gap-4";

      messages.forEach((m) => {
        const row = document.createElement("div");
        row.className = `flex items-end gap-2 ${m.role === "user" ? "justify-end" : "justify-start"}`;

        if (m.role === "assistant") {
          const img = document.createElement("img");
          img.src = avatar;
          img.alt = "IA Tio Ben";
          img.className = "h-9 w-9 rounded-full";
          row.appendChild(img);
        }

        const bubble = document.createElement("div");
        bubble.className =
          "relative max-w-[78%] md:max-w-[75%] p-5 rounded-2xl bg-amber-100/90 text-sm md:text-base shadow leading-[1.9] " +
          (m.role === "user"
            ? "bg-amber-700 text-white rounded-br-none"
            : "bg-[#fffaf1] text-gray-900 rounded-bl-none border border-amber-100");

        bubble.innerHTML = `<div class="space-y-3">${
          m.role === "assistant" ? formatAssistant(m.content) : esc(m.content)
        }</div>`;

        row.appendChild(bubble);

        if (m.role === "user") {
          const img = document.createElement("img");
          img.src = "/images/avatar-user.png";
          img.alt = "User";
          img.className = "h-9 w-9 rounded-full";
          row.appendChild(img);
        }

        wrap.appendChild(row);
      });

      if (isTyping) {
        const row = document.createElement("div");
        row.className = "flex items-center gap-2";

        const img = document.createElement("img");
        img.src = avatar;
        img.alt = "IA Tio Ben";
        img.className = "h-8 w-8 rounded-full";
        row.appendChild(img);

        const bubble = document.createElement("div");
        bubble.className = "px-4 py-3 rounded-xl bg-white shadow text-sm";
        bubble.textContent = lang === "en" ? "Thinking..." : "Pesquisando...";
        row.appendChild(bubble);

        wrap.appendChild(row);
      }

      root.appendChild(wrap);

      // show/hide first card + dock
      const hasMessages = messages.length > 0;
      if (firstWrap) firstWrap.classList.toggle("hidden", hasMessages);
      if (dock) dock.classList.toggle("hidden", !hasMessages);
    }

    function syncDisabled() {
      const v1 = (firstInput?.value || "").trim();
      const v2 = (input?.value || "").trim();

      if (firstSend) firstSend.disabled = v1.length === 0 || isTyping;
      if (send) send.disabled = v2.length === 0 || isTyping;
    }

    async function ask(pergunta) {
      const history = messages.slice(-5);

      try {
        const res = await fetch(api, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            ...(csrf ? { "X-CSRF-TOKEN": csrf } : {}),
            "X-Requested-With": "XMLHttpRequest",
          },
          body: JSON.stringify({ pergunta, history, lang }),
        });

        const data = await res.json().catch(() => ({}));

        // se back retornar erro 419/500 etc
        if (!res.ok) {
          const msg =
            data?.message ||
            (lang === "en"
              ? `⚠️ Request failed (${res.status}).`
              : `⚠️ Falha na requisição (${res.status}).`);
          return msg;
        }

        return (
          data?.resposta ||
          (lang === "en"
            ? "⚠️ There was a problem. Please try again."
            : "⚠️ Houve um problema. Tente novamente.")
        );
      } catch (e) {
        return lang === "en"
          ? "⚠️ Network error. Please try again."
          : "⚠️ Erro de rede. Tente novamente.";
      }
    }

    async function submitFrom(textareaEl) {
      if (!textareaEl) return;

      const pergunta = (textareaEl.value || "").trim();
      if (!pergunta || isTyping) return;

      textareaEl.value = "";
      isTyping = true;
      syncDisabled();

      messages = [...messages, { role: "user", content: pergunta }];
      render();

      const resposta = await ask(pergunta);

      isTyping = false;
      messages = [...messages, { role: "assistant", content: resposta }];
      render();
      syncDisabled();
    }

    // listeners
    if (firstInput) {
      firstInput.addEventListener("input", syncDisabled);
      firstInput.addEventListener("keydown", (e) => {
        if (e.key === "Enter" && !e.shiftKey) {
          e.preventDefault();
          submitFrom(firstInput);
        }
      });
    }

    if (input) {
      input.addEventListener("input", syncDisabled);
      input.addEventListener("keydown", (e) => {
        if (e.key === "Enter" && !e.shiftKey) {
          e.preventDefault();
          submitFrom(input);
        }
      });
    }

    if (firstSend) firstSend.addEventListener("click", () => submitFrom(firstInput));
    if (send) send.addEventListener("click", () => submitFrom(input));

    // initial
    render();
    syncDisabled();
  });
})();
