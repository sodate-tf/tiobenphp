{{-- resources/views/partials/analytics-ads.blade.php --}}
@php
  $adsenseClient = $adsenseClient ?? 'ca-pub-8819996017476509';
  $ga4Id = $ga4Id ?? 'G-17GKJ4F1Q8';
@endphp

<script>
(function(){
  'use strict';

  // =====================================================
  // AdSense helper
  // O script global do AdSense deve estar no <head>
  // pelo layouts/site.blade.php.
  //
  // Este helper NÃO atrasa o AdSense.
  // Ele apenas mantém compatibilidade com blocos antigos:
  // <ins class="adsbygoogle" data-ads-init="1" ...></ins>
  // =====================================================

  var ADS_CLIENT = @json($adsenseClient);

  function markAdsReady(){
    window.__IA_TIOBEN_ADS_READY__ = true;

    try {
      window.dispatchEvent(new Event('ia-tioben-ads-ready'));
    } catch(e) {}
  }

  function waitForAdsense(cb){
    cb = cb || function(){};

    if (window.adsbygoogle) {
      markAdsReady();
      cb();
      return;
    }

    var tries = 0;
    var maxTries = 80; // até ~20s, sem travar página

    var timer = window.setInterval(function(){
      tries++;

      if (window.adsbygoogle) {
        window.clearInterval(timer);
        markAdsReady();
        cb();
        return;
      }

      if (tries >= maxTries) {
        window.clearInterval(timer);
      }
    }, 250);
  }

  function canPush(ins){
    if (!ins) return false;

    // Evita duplicidade.
    if (ins.__iaTiobenPushed) return false;

    // O Google adiciona esse atributo quando o bloco já foi processado.
    if (ins.getAttribute('data-adsbygoogle-status')) return false;

    var rect = ins.getBoundingClientRect();

    // Evita push em container invisível/zerado.
    if (rect.width < 20 || rect.height < 20) return false;

    return true;
  }

  function pushIns(ins){
    if (!ins || ins.__iaTiobenPushed) return;

    if (!window.adsbygoogle) {
      waitForAdsense(function(){
        pushIns(ins);
      });
      return;
    }

    if (!canPush(ins)) {
      window.setTimeout(function(){
        if (canPush(ins)) pushIns(ins);
      }, 300);
      return;
    }

    try {
      ins.__iaTiobenPushed = true;

      window.adsbygoogle = window.adsbygoogle || [];
      window.adsbygoogle.push({});

      ins.removeAttribute('data-ads-init');
    } catch(e) {
      ins.__iaTiobenPushed = false;
    }
  }

  function initLegacyAds(root){
    root = root || document;

    // Compatibilidade apenas com blocos antigos marcados.
    // Os blocos novos que já têm push inline não são tocados aqui.
    var nodes = root.querySelectorAll('ins.adsbygoogle[data-ads-init="1"]');

    if (!nodes.length) return;

    waitForAdsense(function(){
      nodes.forEach(function(node){
        pushIns(node);
      });
    });
  }

  function domReady(cb){
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', cb, { once:true });
      return;
    }

    cb();
  }

  domReady(function(){
    waitForAdsense(function(){
      initLegacyAds(document);
    });
  });

  // API global para AJAX, abas, acordeões ou conteúdo carregado depois.
  window.__IA_TIOBEN_ADS_INIT__ = function(root){
    initLegacyAds(root || document);
  };

  // API explícita para um bloco específico, caso precise.
  window.__IA_TIOBEN_ADS_PUSH__ = function(ins){
    pushIns(ins);
  };
})();
</script>

<script>
(function(){
  'use strict';

  // =====================================================
  // GA4
  // Carrega analytics sem bloquear renderização.
  // Mantém controle manual de page_view.
  // =====================================================

  var GA4_ID = @json($ga4Id);

  if (!GA4_ID) return;
  if (window.__IA_TIOBEN_GA4__) return;

  window.__IA_TIOBEN_GA4__ = true;

  var s = document.createElement('script');
  s.async = true;
  s.src = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(GA4_ID);
  document.head.appendChild(s);

  window.dataLayer = window.dataLayer || [];

  function gtag(){
    window.dataLayer.push(arguments);
  }

  window.gtag = window.gtag || gtag;

  window.gtag('js', new Date());

  // send_page_view false evita pageview duplicado.
  window.gtag('config', GA4_ID, {
    anonymize_ip: true,
    send_page_view: false,
    transport_type: 'beacon'
  });

  function sendPageView(){
    try {
      window.gtag('event', 'page_view', {
        page_location: window.location.href,
        page_path: window.location.pathname,
        page_title: document.title
      });
    } catch(e) {}
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', sendPageView, { once:true });
  } else {
    sendPageView();
  }

  // API para navegação AJAX/SPA, se algum dia precisar.
  window.__IA_TIOBEN_GA4_PAGEVIEW__ = function(url, title){
    try {
      window.gtag('event', 'page_view', {
        page_location: url || window.location.href,
        page_path: window.location.pathname,
        page_title: title || document.title
      });
    } catch(e) {}
  };
})();
</script>