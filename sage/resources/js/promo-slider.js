/** Homepage carousel; static horizontal scrolling remains available without JS. */
export function initPromoSlider() {
  document.querySelectorAll('[data-promo-slider]').forEach(root => {
    const slides = [...root.querySelectorAll('[data-promo-slide]')];
    if (slides.length < 2) return;
    const viewport = root.querySelector('[data-promo-viewport]');
    const status = root.querySelector('[data-promo-status]');
    const play = root.querySelector('[data-promo-play]');
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
    let current = 0, timer, hovered = false, focused = false;
    let paused = root.dataset.autoplay !== 'true' || reduced.matches;
    const sync = () => {
      clearInterval(timer);
      const running = !paused && !reduced.matches && !hovered && !focused && !document.hidden;
      if (play) {
        play.hidden = reduced.matches;
        play.textContent = paused || reduced.matches ? '▷' : 'Ⅱ';
        play.setAttribute('aria-label', paused || reduced.matches ? play.dataset.playLabel : play.dataset.pauseLabel);
      }
      status.setAttribute('aria-live', running ? 'off' : 'polite');
      if (running) timer = setInterval(() => show(current + 1), 7000);
    };
    const show = index => {
      current = (index + slides.length) % slides.length;
      slides.forEach((slide, i) => { slide.hidden = i !== current; slide.inert = i !== current; });
      status.textContent = `${current + 1} / ${slides.length}`;
    };
    const manual = index => { paused = true; show(index); sync(); };
    root.classList.add('is-enhanced');
    root.querySelector('[data-promo-controls]').hidden = false;
    root.querySelector('[data-promo-prev]').addEventListener('click', () => manual(current - 1));
    root.querySelector('[data-promo-next]').addEventListener('click', () => manual(current + 1));
    play?.addEventListener('click', () => { paused = !paused; sync(); });
    root.addEventListener('mouseenter', () => { hovered = true; sync(); });
    root.addEventListener('mouseleave', () => { hovered = false; sync(); });
    root.addEventListener('focusin', () => { focused = true; sync(); });
    root.addEventListener('focusout', event => { if (!root.contains(event.relatedTarget)) { focused = false; sync(); } });
    root.addEventListener('keydown', event => {
      if (!['ArrowLeft','ArrowRight'].includes(event.key) || event.target.closest('a,input,textarea,select')) return;
      event.preventDefault(); manual(current + (event.key === 'ArrowRight' ? 1 : -1));
    });
    let start;
    viewport.addEventListener('touchstart', event => { start = { x:event.touches[0].clientX,y:event.touches[0].clientY }; }, {passive:true});
    viewport.addEventListener('touchend', event => {
      if (!start) return;
      const dx = event.changedTouches[0].clientX-start.x, dy = event.changedTouches[0].clientY-start.y;
      if (Math.abs(dx)>55 && Math.abs(dx)>Math.abs(dy)) manual(current+(dx<0?1:-1));
      start = null;
    }, {passive:true});
    viewport.addEventListener('touchcancel', () => { start = null; });
    reduced.addEventListener('change', () => { if (reduced.matches) paused = true; sync(); });
    document.addEventListener('visibilitychange', sync);
    show(0); sync();
  });
}
