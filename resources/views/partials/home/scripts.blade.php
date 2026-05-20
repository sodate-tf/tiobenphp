<script defer src="/js/chat.js?v=3"></script>
<script>
(() => {
  // ===== Stories: seen state =====
  const SEEN_KEY = 'tio_ben_seen_stories_v1';

  const buttons = Array.from(document.querySelectorAll('[data-story-href]'));
  const stories = buttons.map(btn => ({
    href: btn.getAttribute('data-story-href'),
    title: btn.getAttribute('data-story-title') || 'Story',
    btn,
  })).filter(s => !!s.href);

  function loadSeen() {
    try { return JSON.parse(localStorage.getItem(SEEN_KEY) || '{}') || {}; }
    catch { return {}; }
  }
  function saveSeen(map) {
    try { localStorage.setItem(SEEN_KEY, JSON.stringify(map)); } catch {}
  }
  function markSeen(href) {
    const seen = loadSeen();
    seen[href] = Date.now();
    saveSeen(seen);
    applySeenUI();
  }
  function applySeenUI() {
    const seen = loadSeen();
    stories.forEach(s => {
      const isSeen = !!seen[s.href];
      const ring = s.btn.querySelector('.storyRing');
      const progress = s.btn.querySelector('.storyProgress');
      if (ring) ring.classList.toggle('opacity-50', isSeen);
      if (progress) {
        progress.classList.toggle('w-full', isSeen);
        progress.classList.toggle('bg-amber-950/30', isSeen);
        progress.classList.toggle('w-2/5', !isSeen);
        progress.classList.toggle('bg-amber-900/70', !isSeen);
      }
    });
  }
  applySeenUI();

  // ===== Modal Story Player =====
  const modal = document.getElementById('storyModal');
  const closeBtn = document.getElementById('storyClose');
  const nextBtn = document.getElementById('storyNext');
  const prevBtn = document.getElementById('storyPrev');
  const ampAnchor = document.getElementById('ampAnchor');
  const openLink = document.getElementById('storyOpenLink');

  let activeIndex = 0;

  function openStory(idx) {
    activeIndex = idx;
    const href = stories[activeIndex]?.href;
    if (!href) return;

    ampAnchor.setAttribute('href', href);
    openLink.setAttribute('href', href);

    modal.classList.remove('hidden');
    markSeen(href);
  }
  function closeStory() { modal.classList.add('hidden'); }
  function nextStory() { openStory((activeIndex + 1) % stories.length); }
  function prevStory() { openStory((activeIndex - 1 + stories.length) % stories.length); }

  buttons.forEach((btn, idx) => btn.addEventListener('click', () => openStory(idx)));
  closeBtn?.addEventListener('click', closeStory);
  nextBtn?.addEventListener('click', nextStory);
  prevBtn?.addEventListener('click', prevStory);

  modal?.addEventListener('mousedown', (e) => { if (e.target === modal) closeStory(); });
  window.addEventListener('keydown', (e) => {
    if (!modal || modal.classList.contains('hidden')) return;
    if (e.key === 'Escape') closeStory();
    if (e.key === 'ArrowRight') nextStory();
    if (e.key === 'ArrowLeft') prevStory();
  });

  // ===== FAQ accordion =====
  const faqButtons = document.querySelectorAll('[data-faq]');
  faqButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-faq');
      const panel = document.querySelector(`[data-faq-panel="${id}"]`);
      if (!panel) return;

      const expanded = btn.getAttribute('aria-expanded') === 'true';
      btn.setAttribute('aria-expanded', expanded ? 'false' : 'true');
      const icon = btn.querySelector('span[aria-hidden="true"]');
      if (icon) icon.textContent = expanded ? '+' : '−';
      panel.classList.toggle('hidden', expanded);
    });
  });

  // ===== AdSense push when visible (avoid double push) =====
  const adIns = document.querySelector('#adSlotHome ins.adsbygoogle');
  if (adIns) {
    let pushed = false;

    const pushAd = () => {
      if (pushed) return;
      try {
        (window.adsbygoogle = window.adsbygoogle || []).push({});
        pushed = true;
      } catch {}
    };

    const obs = new IntersectionObserver(([entry]) => {
      if (!entry.isIntersecting) return;
      pushAd();
      obs.disconnect();
    }, { threshold: 0.25 });

    obs.observe(adIns);
  }

  // ===== Chat (POST /api/perguntar) =====
  const questionBox = document.getElementById('questionBox');
  const questionInput = document.getElementById('questionInput');
  const askBtn = document.getElementById('askBtn');
  const sendBtn = document.getElementById('sendBtn');

  function syncSendDisabled() {
    const v = (questionInput?.value || '').trim();
    if (sendBtn) sendBtn.disabled = v.length === 0;
  }

  questionInput?.addEventListener('input', syncSendDisabled);
  syncSendDisabled();

  async function ask(pergunta, history) {
    try {
      const res = await fetch('/api/perguntar', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ pergunta, history}),
      });
      const data = await res.json();
      return data?.resposta || '⚠️ There was a problem. Please try again.';
    } catch {
      return '⚠️ There was a problem. Please try again.';
    }
  }

  // MVP: manter conversas apenas em memória
  let messages = [];

  async function handleQuestion(fromInitial) {
    const box = fromInitial ? questionBox : questionInput;
    if (!box) return;

    const pergunta = (box.value || '').trim();
    if (!pergunta) return;

    box.value = '';
    syncSendDisabled();

    const history = [...messages, { role: 'user', content: pergunta }];
    messages = history;

    const resposta = await ask(pergunta, history);
    messages = [...messages, { role: 'assistant', content: resposta }];

    // MVP (por enquanto): exibe em alert; depois faço as bolhas igual seu React.
    alert(resposta);
  }

  askBtn?.addEventListener('click', () => handleQuestion(true));
  sendBtn?.addEventListener('click', () => handleQuestion(false));
  questionInput?.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      handleQuestion(false);
    }
  });
})();
</script>