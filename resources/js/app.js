import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

// inicia quando o browser estiver ocioso (não compete com LCP)
function startAlpine() {
  try { Alpine.start(); } catch (e) {}
}

if ('requestIdleCallback' in window) {
  requestIdleCallback(startAlpine, { timeout: 2500 });
} else {
  setTimeout(startAlpine, 1200);
}