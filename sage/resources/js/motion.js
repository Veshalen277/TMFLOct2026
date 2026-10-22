import { gsap } from 'gsap';

// Seconds. Shared by page reveals, React controls and WooCommerce feedback.
export const timing = Object.freeze({ fast: 0.18, control: 0.28, panel: 0.45, reveal: 0.65, stagger: 0.065 });
export const reducedMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

export function initMotion() {
  const media = gsap.matchMedia();
  media.add('(prefers-reduced-motion: no-preference)', (context) => {
    const seen = new WeakSet();
    const active = new Map();
    const selector = '.cat-card, .product-card, .trust-item, .section-title, .wc-page-wrap ul.products > li.product, .woocommerce .product_title, .woocommerce .woocommerce-tabs, .woocommerce .cart_totals, .woocommerce-MyAccount-content, .woocommerce-order, .wc-block-cart, .wc-block-checkout, [data-home-reveal]';
    // Observe while visible: content is never left hidden waiting for JavaScript.
    const observer = new IntersectionObserver(context.add('reveal', entries => {
      entries.filter(entry => entry.isIntersecting).forEach((entry, index) => {
        observer.unobserve(entry.target);
        const tween = gsap.fromTo(entry.target, { y: 22, opacity: 0.35 }, {
          y: 0, opacity: 1, duration: timing.reveal,
          delay: Math.min(index, 5) * timing.stagger, ease: 'power3.out',
          clearProps: 'transform,opacity', onComplete: () => active.delete(entry.target),
        });
        active.set(entry.target, tween);
      });
    }), { threshold: 0.08 });
    function scan() {
      document.querySelectorAll(selector).forEach(el => {
        if (!seen.has(el)) { seen.add(el); observer.observe(el); }
      });
      active.forEach((tween, el) => { if (!el.isConnected) { tween.kill(); active.delete(el); } });
    }
    const intro = gsap.timeline({ defaults: { ease: 'power3.out', clearProps: 'transform,opacity' } });
    const header = document.querySelector('.site-header__inner');
    if (header) intro.from(header, { y: -12, opacity: 0.5, duration: timing.panel });
    const hero = document.querySelector('.slider__slide.is-active .slider__content');
    if (hero) intro.from(hero, { y: 28, opacity: 0, duration: 0.85 }, 0.15);
    const homeIntro = document.querySelectorAll('[data-home-intro]');
    if (homeIntro.length) intro.fromTo(homeIntro, { y: 20, opacity: 0.3 }, { y: 0, opacity: 1, duration: 0.8, stagger: 0.09, clearProps: 'transform,opacity' }, 0.12);
    const faqListeners = [];
    document.querySelectorAll('.hf-faqs details').forEach(details => {
      const revealAnswer = context.add('faq-' + faqListeners.length, () => {
        if (!details.open) return;
        const answer = details.querySelector(':scope > div');
        if (answer) gsap.fromTo(answer, { y: -6, opacity: 0.4 }, { y: 0, opacity: 1, duration: timing.control, clearProps: 'transform,opacity', overwrite: true });
      });
      details.addEventListener('toggle', revealAnswer);
      faqListeners.push(() => details.removeEventListener('toggle', revealAnswer));
    });
    const hover = context.add('hover', event => {
      const card = event.target.closest('.product-card, li.product, .cat-card');
      if (!card || card.contains(event.relatedTarget) || !window.matchMedia('(hover: hover)').matches) return;
      const img = card.querySelector('img');
      if (img) gsap.to(img, { scale: event.type === 'pointerover' ? 1.035 : 1, duration: timing.panel, ease: 'power2.out', overwrite: true });
    });
    const feedback = context.add('feedback', () => {
      const notices = document.querySelectorAll('.woocommerce-message, .woocommerce-error, .woocommerce-info');
      if (notices.length) gsap.fromTo(notices, { y: -8, opacity: 0.5 }, { y: 0, opacity: 1, duration: timing.control, clearProps: 'transform,opacity' });
      scan();
    });
    const panels = [];
    ['mobile-menu', 'site-search', 'slider-track'].forEach(id => {
      const panel = document.getElementById(id);
      if (!panel) return;
      const changed = context.add(`panel-${id}`, () => {
        const target = id === 'slider-track' ? panel.querySelector('.is-active .slider__content') : panel.querySelector(id === 'mobile-menu' ? '.mobile-menu__inner' : '.site-search__inner');
        if (!target || (id !== 'slider-track' && !panel.classList.contains('is-open'))) return;
        gsap.fromTo(target, { opacity: 0.4, y: 12 }, { opacity: 1, y: 0, delay: 0.08, duration: timing.panel, ease: 'power3.out', overwrite: true, clearProps: 'opacity,transform' });
      });
      const watcher = new MutationObserver(changed);
      watcher.observe(panel, { attributes: true, attributeFilter: ['class'], subtree: id === 'slider-track' });
      panels.push(watcher);
    });
    let frame;
    const mutations = new MutationObserver(() => { cancelAnimationFrame(frame); frame = requestAnimationFrame(scan); });
    mutations.observe(document.getElementById('main') || document.body, { childList: true, subtree: true });
    document.addEventListener('pointerover', hover);
    document.addEventListener('pointerout', hover);
    const jq = window.jQuery;
    jq?.(document.body).on('updated_wc_div.sageMotion updated_checkout.sageMotion checkout_error.sageMotion found_variation.sageMotion', feedback);
    scan();
    return () => {
      faqListeners.forEach(remove => remove());
      panels.forEach(panel => panel.disconnect());
      observer.disconnect(); mutations.disconnect(); cancelAnimationFrame(frame);
      document.removeEventListener('pointerover', hover); document.removeEventListener('pointerout', hover);
      jq?.(document.body).off('.sageMotion'); active.clear();
    };
  });
  return () => media.revert();
}
