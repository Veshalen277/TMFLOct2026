/** Motion is enhancement only. Original links always provide a usable fallback. */
export function initCategoryStrip() {
  document.querySelectorAll('[data-category-strip]').forEach(strip => {
    const viewport = strip.querySelector('[data-category-viewport]');
    const track = strip.querySelector('[data-category-track]');
    const list = strip.querySelector('[data-category-list]');
    const toggle = strip.querySelector('[data-category-toggle]');
    if (!viewport || !track || !list || !toggle || !track.animate) return;

    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
    const pointer = window.matchMedia('(hover: hover) and (pointer: fine)');
    let animation;
    let copy;
    let userPaused = false;
    let hovering = false;
    let focusInside = false;
    let frame;

    const pauseTransiently = () => {
      if (!animation) return;
      if (hovering || document.hidden) animation.pause();
      else animation.play();
    };
    const update = () => {
      animation?.cancel();
      animation = undefined;
      strip.classList.remove('is-moving');
      const width = list.getBoundingClientRect().width;
      const allowed = strip.dataset.motion === 'true' && !reduced.matches && pointer.matches;
      const overflowing = width > viewport.clientWidth + 2;
      toggle.hidden = !allowed || !overflowing;
      toggle.setAttribute('aria-pressed', String(userPaused));
      toggle.setAttribute('aria-label', userPaused ? toggle.dataset.playLabel : toggle.dataset.pauseLabel);
      if (!allowed || !overflowing || userPaused || focusInside) return;
      if (!copy) {
        copy = list.cloneNode(true);
        copy.removeAttribute('data-category-list');
        copy.setAttribute('data-category-copy', '');
        copy.setAttribute('aria-hidden', 'true');
        copy.querySelectorAll('a').forEach(link => { link.tabIndex = -1; link.removeAttribute('aria-current'); });
        track.append(copy);
      }
      strip.classList.add('is-moving');
      viewport.scrollLeft = 0;
      // Constant, gentle speed even when the catalog gains more categories.
      animation = track.animate([{ transform: 'translateX(0)' }, { transform: `translateX(-${width}px)` }], {
        duration: width / 18 * 1000, iterations: Infinity, easing: 'linear',
      });
      pauseTransiently();
    };
    const schedule = () => { cancelAnimationFrame(frame); frame = requestAnimationFrame(update); };
    toggle.addEventListener('click', () => { userPaused = !userPaused; update(); });
    viewport.addEventListener('mouseenter', () => { hovering = true; pauseTransiently(); });
    viewport.addEventListener('mouseleave', () => { hovering = false; pauseTransiently(); });
    // Keep repeated links clickable without focusing an aria-hidden visual copy.
    viewport.addEventListener('mousedown', event => {
      if (event.target.closest('[data-category-copy] a')) event.preventDefault();
    });
    viewport.addEventListener('focusin', event => {
      if (event.target.closest('[data-category-copy]')) return;
      focusInside = true;
      update();
      // Native scrolling reveals the actual link, never an inaccessible copy.
      event.target.closest('a')?.scrollIntoView({ block: 'nearest', inline: 'nearest', behavior: 'instant' });
    });
    viewport.addEventListener('focusout', () => {
      queueMicrotask(() => {
        focusInside = viewport.contains(document.activeElement);
        if (!focusInside) update();
      });
    });
    document.addEventListener('visibilitychange', pauseTransiently);
    reduced.addEventListener('change', schedule);
    pointer.addEventListener('change', schedule);
    if ('ResizeObserver' in window) {
      const observer = new ResizeObserver(schedule);
      observer.observe(viewport);
      observer.observe(list);
    } else window.addEventListener('resize', schedule, { passive: true });
    document.fonts?.ready.then(schedule);
    update();
  });
}
