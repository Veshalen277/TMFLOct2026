import { initPromoSlider } from './promo-slider.js';
import { initStorefrontLayouts } from './storefront-layouts.js';
import { initMotion } from './motion.js';
import { initCategoryStrip } from './category-strip.js';

/**
 * IG Hoosen — app.js
 * Handles: hero slider, mobile menu, sticky header, search, product slider
 */

document.addEventListener('DOMContentLoaded', () => {
  initCategoryStrip();
  initPromoSlider();
  initStorefrontLayouts();

  /* ══════════════════════════════════════
     HERO SLIDER
  ══════════════════════════════════════ */
  const track     = document.getElementById('slider-track');
  const prevBtn   = document.getElementById('slider-prev');
  const nextBtn   = document.getElementById('slider-next');
  const dotsWrap  = document.getElementById('slider-dots');

  if (track && track.querySelector('.slider__slide')) {
    const slides  = [...track.querySelectorAll('.slider__slide')];
    const dots    = dotsWrap ? [...dotsWrap.querySelectorAll('.slider__dot')] : [];
    let current   = 0;
    let autoTimer = null;
    const DELAY   = 5500;

    function goTo(index) {
      slides[current].classList.remove('is-active');
      slides[current].setAttribute('aria-hidden', 'true');
      if (dots[current]) {
        dots[current].classList.remove('is-active');
        dots[current].setAttribute('aria-selected', 'false');
      }

      current = (index + slides.length) % slides.length;

      slides[current].classList.add('is-active');
      slides[current].setAttribute('aria-hidden', 'false');
      if (dots[current]) {
        dots[current].classList.add('is-active');
        dots[current].setAttribute('aria-selected', 'true');
      }
    }

    function startAuto() {
      clearInterval(autoTimer);
      if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || document.hidden || track.contains(document.activeElement)) return;
      autoTimer = setInterval(() => goTo(current + 1), DELAY);
    }

    function resetAuto() {
      startAuto();
    }

    prevBtn?.addEventListener('click', () => { goTo(current - 1); resetAuto(); });
    nextBtn?.addEventListener('click', () => { goTo(current + 1); resetAuto(); });

    dots.forEach((dot, i) => {
      dot.addEventListener('click', () => { goTo(i); resetAuto(); });
    });

    // Touch/swipe support
    let touchStartX = 0;
    track.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; }, { passive: true });
    track.addEventListener('touchend', e => {
      const diff = touchStartX - e.changedTouches[0].clientX;
      if (Math.abs(diff) > 50) { diff > 0 ? goTo(current + 1) : goTo(current - 1); resetAuto(); }
    }, { passive: true });

    // Pause on hover
    track.addEventListener('mouseenter', () => clearInterval(autoTimer));
    track.addEventListener('mouseleave', startAuto);

    // Keyboard
    track.setAttribute('tabindex', '0');
    track.addEventListener('keydown', e => {
      if (e.target.matches('input, textarea, select')) return;
      if (e.key === 'ArrowLeft')  { goTo(current - 1); resetAuto(); }
      if (e.key === 'ArrowRight') { goTo(current + 1); resetAuto(); }
    });

    track.addEventListener('focusin', () => clearInterval(autoTimer));
    track.addEventListener('focusout', () => setTimeout(startAuto, 0));
    document.addEventListener('visibilitychange', () => document.hidden ? clearInterval(autoTimer) : startAuto());
    startAuto();
  }

  /* ══════════════════════════════════════
     STICKY HEADER
  ══════════════════════════════════════ */
  const header = document.getElementById('site-header');
  if (header) {
    const onScroll = () => {
      header.classList.toggle('scrolled', window.scrollY > 60);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  /* ══════════════════════════════════════
     MOBILE MENU
  ══════════════════════════════════════ */
  const hamburger   = document.getElementById('hamburger');
  const mobileMenu  = document.getElementById('mobile-menu');
  const menuClose   = document.getElementById('mobile-menu-close');
  const backdrop    = document.getElementById('menu-backdrop');

  if (mobileMenu) mobileMenu.inert = true;
  let previousOverflow = '';
  function openMenu() {
    if (!mobileMenu || mobileMenu.classList.contains('is-open')) return;
    closeSearch();
    previousOverflow = document.body.style.overflow;
    mobileMenu.inert = false;
    mobileMenu?.classList.add('is-open');
    backdrop?.classList.add('is-visible');
    hamburger?.classList.add('is-open');
    hamburger?.setAttribute('aria-expanded', 'true');
    mobileMenu?.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    menuClose?.focus();
  }

  function closeMenu() {
    const wasOpen = mobileMenu?.classList.contains('is-open');
    if (mobileMenu) mobileMenu.inert = true;
    mobileMenu?.classList.remove('is-open');
    backdrop?.classList.remove('is-visible');
    hamburger?.classList.remove('is-open');
    hamburger?.setAttribute('aria-expanded', 'false');
    mobileMenu?.setAttribute('aria-hidden', 'true');
    if (wasOpen) { document.body.style.overflow = previousOverflow; hamburger?.focus(); }
  }

  hamburger?.addEventListener('click', () => {
    mobileMenu?.classList.contains('is-open') ? closeMenu() : openMenu();
  });
  mobileMenu?.addEventListener('keydown', e => {
    if (e.key !== 'Tab') return;
    const items = [...mobileMenu.querySelectorAll('a[href],button:not([disabled]),input:not([disabled])')].filter(el => el.getClientRects().length);
    const first = items[0], last = items.at(-1);
    if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last?.focus(); }
    else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first?.focus(); }
  });
  menuClose?.addEventListener('click', closeMenu);
  backdrop?.addEventListener('click', closeMenu);
  const desktopMenu = window.matchMedia('(min-width: 1025px)');
  desktopMenu.addEventListener('change', event => { if (event.matches) closeMenu(); });
  mobileMenu?.querySelectorAll('a[href]').forEach(link => link.addEventListener('click', closeMenu));

  // Close on ESC
  document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeMenu(); closeSearch(); } });

  /* ══════════════════════════════════════
     SEARCH TOGGLE
  ══════════════════════════════════════ */
  const searchToggle = document.querySelector('.header-search-toggle');
  const searchBar    = document.getElementById('site-search');
  const searchClose  = document.getElementById('search-close');
  const searchInput  = searchBar?.querySelector('input[type="search"]');
  if (searchInput && document.getElementById('site-search-hint')) {
    const description = searchInput.getAttribute('aria-describedby') || '';
    searchInput.setAttribute('aria-describedby', `${description} site-search-hint`.trim());
  }


  if (searchBar) searchBar.inert = true;
  let searchTimer;
  function openSearch() {
    closeMenu();
    searchToggle?.setAttribute('aria-expanded', 'true');
    if (searchBar) searchBar.inert = false;
    clearTimeout(searchTimer);
    searchBar?.classList.add('is-open');
    searchBar?.setAttribute('aria-hidden', 'false');
    searchTimer = setTimeout(() => searchInput?.focus(), 350);
  }

  function closeSearch() {
    searchToggle?.setAttribute('aria-expanded', 'false');
    clearTimeout(searchTimer);
    const wasOpen = searchBar?.classList.contains('is-open');
    if (searchBar) searchBar.inert = true;
    if (wasOpen) searchToggle?.focus();
    searchBar?.classList.remove('is-open');
    searchBar?.setAttribute('aria-hidden', 'true');
  }

  searchToggle?.addEventListener('click', e => {
    e.preventDefault();
    searchBar?.classList.contains('is-open') ? closeSearch() : openSearch();
  });
  searchClose?.addEventListener('click', closeSearch);

  /* ══════════════════════════════════════
     PRODUCTS SLIDER (horizontal scroll)
  ══════════════════════════════════════ */
  document.querySelectorAll('.products-slider-wrap').forEach(wrap => {
    const slider  = wrap.querySelector('.products-slider');
    const btnPrev = wrap.querySelector('.products-slider__btn--prev');
    const btnNext = wrap.querySelector('.products-slider__btn--next');
    if (!slider) return;

    const scrollBy = () => slider.clientWidth * 0.75;

    btnPrev?.addEventListener('click', () => slider.scrollBy({ left: -scrollBy(), behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' }));
    btnNext?.addEventListener('click', () => slider.scrollBy({ left:  scrollBy(), behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' }));

    // Show/hide arrows based on scroll position
    const updateBtns = () => {
      if (btnPrev) btnPrev.style.opacity = slider.scrollLeft <= 0 ? '0.3' : '1';
      if (btnNext) btnNext.style.opacity = slider.scrollLeft + slider.clientWidth >= slider.scrollWidth - 1 ? '0.3' : '1';
    };
    slider.addEventListener('scroll', updateBtns, { passive: true });
    updateBtns();
  });

  /* ══════════════════════════════════════
     SMOOTH SCROLL for anchor links
  ══════════════════════════════════════ */
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', e => {
      const hash = anchor.getAttribute('href');
      if (!hash || hash === '#' || anchor.closest('.wc-tabs, .woocommerce-MyAccount-navigation')) return;
      let id;
      try { id = decodeURIComponent(hash.slice(1)); } catch { return; }
      const target = document.getElementById(id);
      if (target) {
        e.preventDefault();
        const offset = (header?.offsetHeight ?? 0) + 16;
        target.setAttribute('tabindex', target.getAttribute('tabindex') || '-1');
        target.focus({ preventScroll: true });
        window.scrollTo({ top: target.getBoundingClientRect().top + window.scrollY - offset, behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' });
      }
    });
  });

  // Blocks update the server cart; request the authoritative header fragment.
  ['wc-blocks_added_to_cart', 'wc-blocks_removed_from_cart'].forEach(event => {
    document.addEventListener(event, () => window.jQuery?.(document.body).trigger('wc_fragment_refresh'));
  });

  initMotion();
  if (document.getElementById('sage-commerce-root')) {
    import('./commerce/index.jsx').then(({ mountCommerce }) => mountCommerce()).catch(console.error);
  }

});
