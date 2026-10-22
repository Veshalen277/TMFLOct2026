import React, { useEffect, useLayoutEffect, useRef, useState } from 'react';
import { createRoot } from 'react-dom/client';
import { gsap } from 'gsap';
import { reducedMotion, timing } from '../motion.js';

function Commerce({ config }) {
  const { labels: t, page } = config;
  const scope = useRef(null);
  const [accountLinks, setAccountLinks] = useState([]);
  const [view, setView] = useState('grid');
  const [notice, setNotice] = useState(false);
  const [showTop, setShowTop] = useState(false);
  const timer = useRef();
  useLayoutEffect(() => {
    const media = gsap.matchMedia();
    media.add('(prefers-reduced-motion: no-preference)', () => {
      gsap.from(scope.current.querySelectorAll('[data-enter]'), {
        y: 12, opacity: 0, duration: timing.panel, stagger: timing.stagger,
        ease: 'power3.out', clearProps: 'transform,opacity',
      });
    });
    return () => media.revert();
  }, []);
  useEffect(() => {
    if (page === 'account') {
      setAccountLinks([...document.querySelectorAll('.woocommerce-MyAccount-navigation a')].map(link => ({ href: link.href, label: link.textContent.trim(), active: link.parentElement.classList.contains('is-active') })));
    }
    const added = () => {
      setNotice(true); clearTimeout(timer.current);
      timer.current = setTimeout(() => setNotice(false), 6000);
    };
    const scroll = () => setShowTop(window.scrollY > 650);
    document.addEventListener('wc-blocks_added_to_cart', added);
    window.jQuery?.(document.body).on('added_to_cart.sageReact', added);
    window.addEventListener('scroll', scroll, { passive: true }); scroll();
    return () => {
      clearTimeout(timer.current);
      document.removeEventListener('wc-blocks_added_to_cart', added);
      window.jQuery?.(document.body).off('.sageReact');
      window.removeEventListener('scroll', scroll);
    };
  }, []);
  useEffect(() => {
    document.querySelector('.wc-page-wrap')?.classList.toggle('sage-list-view', view === 'list');
  }, [view]);
  function changeView(next) {
    if (view === next) return;
    setView(next);
    const products = document.querySelectorAll('#sage-commerce-content ul.products > li.product');
    if (!reducedMotion() && products.length) gsap.fromTo(products, { opacity: 0.55, y: 8 }, {
      opacity: 1, y: 0, duration: timing.control, stagger: { each: 0.025, amount: 0.25 },
      overwrite: true, clearProps: 'transform,opacity',
    });
  }
  function jump(selector) {
    const element = document.querySelector(selector);
    if (!element) return;
    const top = element.getBoundingClientRect().top + window.scrollY - (document.getElementById('site-header')?.offsetHeight || 80) - 24;
    window.scrollTo({ top, behavior: reducedMotion() ? 'auto' : 'smooth' });
    const focus = element.querySelector('select, input:not([type="hidden"]), button, a') || element;
    if (!focus.matches('select,input,button,a')) focus.setAttribute('tabindex', '-1');
    focus.focus({ preventScroll: true });
  }
  return <div className="sage-commerce" ref={scope}>
    <nav className="sage-commerce-nav" aria-label={t.shop} data-enter>
      {['shop', 'cart', 'checkout', 'account'].map(key => <a key={key} href={config[key]} aria-current={(page === key || (key === 'shop' && page === 'product')) ? 'page' : undefined}>{t[key]}</a>)}
    </nav>
    {page === 'shop' && <div className="sage-catalog-tools" data-enter>
      <form role="search" action={config.search} method="get" className="sage-product-search">
        <label className="sr-only" htmlFor="sage-product-search">{t.search}</label>
        <input id="sage-product-search" type="search" name="s" placeholder={t.search} required />
        <input type="hidden" name="post_type" value="product" />
        <button type="submit">{t.search}</button>
      </form>
      <div className="sage-view-controls" role="group" aria-label={t.view}>
        {['grid', 'list'].map(mode => <button key={mode} type="button" aria-pressed={view === mode} onClick={() => changeView(mode)}>{t[mode]}</button>)}
      </div>
    </div>}
    {page === 'account' && accountLinks.length > 0 && <div className="sage-account-switcher" data-enter>
      <label htmlFor="sage-account-section">{t.account}</label>
      <select id="sage-account-section" value={accountLinks.find(link => link.active)?.href || ''} onChange={event => { window.location.href = event.target.value; }}>
        <option value="" disabled>{t.account}</option>
        {accountLinks.map(link => <option key={link.href} value={link.href}>{link.label}</option>)}
      </select>
    </div>}
    {page === 'product' && <div className="sage-product-tools" data-enter>
      <button type="button" onClick={() => jump('.woocommerce div.product .summary')}>{t.purchase}</button>
      <button type="button" onClick={() => jump('.woocommerce-tabs')}>{t.details}</button>
    </div>}
    {['cart', 'checkout', 'confirmation'].includes(page) && <ol className="sage-checkout-steps" aria-label={t.checkout} data-enter>
      {['cart', 'checkout', 'confirmation'].map((step, index) => <li key={step} aria-current={page === step ? 'step' : undefined}>
        <span aria-hidden="true">{index + 1}</span>{step === 'cart' ? <a href={config.cart}>{t[step]}</a> : t[step]}
      </li>)}
    </ol>}
    <div className="sage-toast-region" role="status" aria-live="polite" aria-atomic="true">
      {notice && <div className="sage-toast"><span>{t.added}</span><a href={config.cart}>{t.cart}</a><button type="button" aria-label={t.dismiss} onClick={() => setNotice(false)}>×</button></div>}
    </div>
    {showTop && <button className="sage-back-top" type="button" onClick={() => window.scrollTo({ top: 0, behavior: reducedMotion() ? 'auto' : 'smooth' })} aria-label={t.top}>↑</button>}
  </div>;
}

export function mountCommerce() {
  const container = document.getElementById('sage-commerce-root');
  if (!container || container.dataset.mounted) return;
  const config = JSON.parse(container.dataset.config);
  container.dataset.mounted = 'true';
  createRoot(container).render(<Commerce config={config} />);
}
