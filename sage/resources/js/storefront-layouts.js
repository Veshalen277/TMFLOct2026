/** Native scrolling remains available when JavaScript is disabled. */
export function initStorefrontLayouts() {
  const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
  document.querySelectorAll('[data-product-collection]').forEach(root => {
    const track = root.querySelector('[data-collection-track]');
    const controls = root.querySelector('[data-collection-controls]');
    if (!track || !controls || root.dataset.collectionReady) return;
    root.dataset.collectionReady = 'true';
    const previous = controls.querySelector('[data-collection-prev]');
    const next = controls.querySelector('[data-collection-next]');
    const rtl = getComputedStyle(track).direction === 'rtl';
    const sync = () => {
      const maximum = track.scrollWidth - track.clientWidth;
      const position = Math.abs(track.scrollLeft);
      controls.hidden = maximum < 2;
      previous.setAttribute('aria-disabled', String(position <= 2));
      next.setAttribute('aria-disabled', String(position >= maximum - 2));
    };
    const scroll = direction => track.scrollBy({ left: direction * (rtl ? -1 : 1) * track.clientWidth * 0.85, behavior: reduced.matches ? 'instant' : 'smooth' });
    previous.addEventListener('click', () => { if (previous.getAttribute('aria-disabled') !== 'true') scroll(-1); });
    next.addEventListener('click', () => { if (next.getAttribute('aria-disabled') !== 'true') scroll(1); });
    track.addEventListener('keydown', event => {
      if (event.target !== track || !['ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(event.key)) return;
      event.preventDefault();
      if (event.key === 'Home' || event.key === 'End') track.scrollTo({ left: event.key === 'Home' ? 0 : (rtl ? -1 : 1) * track.scrollWidth, behavior: reduced.matches ? 'instant' : 'smooth' });
      else scroll((event.key === 'ArrowRight' ? 1 : -1) * (rtl ? -1 : 1));
    });
    track.addEventListener('scroll', sync, { passive: true });
    if ('ResizeObserver' in window) new ResizeObserver(sync).observe(track);
    else window.addEventListener('resize', sync, { passive: true });
    sync();
  });
  document.querySelectorAll('[data-storefront-slider]').forEach(root => {
    if (root.dataset.sliderReady) return;
    root.dataset.sliderReady = 'true';
    root.querySelectorAll('img').forEach(img => {
      const failed = () => { img.hidden = true; };
      img.addEventListener('error', failed);
      if (img.complete && !img.naturalWidth) failed();
    });
    const slides = [...root.querySelectorAll('[data-storefront-slide]')];
    if (slides.length < 2) return;
    const viewport = root.querySelector('[data-storefront-viewport]');
    const controls = root.querySelector('[data-storefront-controls]');
    const status = root.querySelector('[data-storefront-status]');
    const play = root.querySelector('[data-storefront-play]');
    const interval = Math.max(4, Math.min(20, Number(root.dataset.interval) || 7)) * 1000;
    let current = 0, timer, hovered = false, focused = false;
    let paused = root.dataset.autoplay !== 'true' || reduced.matches;
    const show = index => {
      if (slides[current].contains(document.activeElement)) viewport.focus({ preventScroll: true });
      current = (index + slides.length) % slides.length;
      slides.forEach((slide, i) => { slide.hidden = i !== current; slide.inert = i !== current; });
      status.textContent = `${current + 1} / ${slides.length}`;
    };
    const sync = () => {
      clearInterval(timer);
      const running = !paused && !reduced.matches && !hovered && !focused && !document.hidden;
      status.setAttribute('aria-live', running ? 'off' : 'polite');
      if (play) {
        play.textContent = paused || reduced.matches ? '▷' : 'Ⅱ';
        play.setAttribute('aria-label', paused || reduced.matches ? play.dataset.playLabel : play.dataset.pauseLabel);
        play.disabled = reduced.matches;
      }
      if (running) timer = setInterval(() => show(current + 1), interval);
    };
    const manual = index => { paused = true; show(index); sync(); };
    root.classList.add('is-enhanced'); controls.hidden = false;
    root.querySelector('[data-storefront-prev]')?.addEventListener('click', () => manual(current - 1));
    root.querySelector('[data-storefront-next]')?.addEventListener('click', () => manual(current + 1));
    play?.addEventListener('click', () => { paused = !paused; sync(); });
    root.addEventListener('mouseenter', () => { hovered = true; sync(); });
    root.addEventListener('mouseleave', () => { hovered = false; sync(); });
    root.addEventListener('focusin', () => { focused = true; sync(); });
    root.addEventListener('focusout', event => { if (!root.contains(event.relatedTarget)) { focused = false; sync(); } });
    viewport.tabIndex = 0;
    viewport.addEventListener('keydown', event => {
      if (event.target !== viewport || !['ArrowLeft', 'ArrowRight'].includes(event.key)) return;
      event.preventDefault(); manual(current + (event.key === 'ArrowRight' ? 1 : -1));
    });
    let start;
    viewport.addEventListener('touchstart', event => { start = { x: event.touches[0].clientX, y: event.touches[0].clientY }; }, { passive: true });
    viewport.addEventListener('touchend', event => {
      if (!start) return;
      const dx = event.changedTouches[0].clientX - start.x, dy = event.changedTouches[0].clientY - start.y;
      if (Math.abs(dx) > 55 && Math.abs(dx) > Math.abs(dy)) manual(current + (dx < 0 ? 1 : -1));
      start = null;
    }, { passive: true });
    viewport.addEventListener('touchcancel', () => { start = null; });
    reduced.addEventListener('change', () => { if (reduced.matches) paused = true; sync(); });
    document.addEventListener('visibilitychange', sync);
    show(0); sync();
  });
}
