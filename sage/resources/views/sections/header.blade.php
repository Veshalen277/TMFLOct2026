<div class="topbar">
  <div class="topbar__inner">
    <div class="topbar__left">
      @if (is_user_logged_in())
        @php($user = wp_get_current_user())
        <a href="{{ esc_url(function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : home_url('/')) }}">{{ __('My Account', 'sage') }}</a>
        <a href="{{ esc_url(wp_logout_url(home_url())) }}">{{ __('Logout', 'sage') }}</a>
        <span class="topbar__welcome">{{ sprintf(__('Welcome back %s', 'sage'), $user->display_name) }}</span>
      @else
        <a href="{{ esc_url(function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : home_url('/')) }}">{{ __('Login', 'sage') }}</a>
      @endif
    </div>
    <div class="topbar__center">
      <span>{{ get_option('ighoosen_topbar_message', 'FREE delivery for orders R799 & over') }}</span>
    </div>
    <div class="topbar__right">
      @if ($fb = get_option('ighoosen_facebook'))
        <a href="{{ esc_url($fb) }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
        </a>
      @endif
      @if ($ig = get_option('ighoosen_instagram'))
        <a href="{{ esc_url($ig) }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
        </a>
      @endif
    </div>
  </div>
</div>

<header class="site-header" id="site-header">
  <div class="site-header__inner">
    <div class="site-logo">
      <a class="site-brand" href="{{ esc_url(home_url('/')) }}" rel="home">
        @if (has_custom_logo())
          {!! wp_get_attachment_image(get_theme_mod('custom_logo'), 'full', false, ['class' => 'site-brand__logo', 'alt' => '', 'loading' => 'eager']) !!}
        @endif
        <span class="site-logo__text">{{ get_bloginfo('name') }}</span>
      </a>
    </div>
    <nav class="site-nav" id="site-nav" aria-label="Primary Navigation">
      @if (has_nav_menu('primary_navigation'))
        {!! wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'site-nav__list', 'container' => false, 'fallback_cb' => false, 'echo' => false]) !!}
      @else
        <ul class="site-nav__list">
          {!! wp_list_pages(['title_li' => '', 'echo' => false, 'sort_column' => 'menu_order,post_title']) !!}
        </ul>
      @endif
    </nav>
    <div class="site-header__actions">
      @if (function_exists('WC') && function_exists('wc_get_cart_url') && function_exists('wc_get_page_permalink'))
        <button type="button" class="header-icon header-search-toggle" aria-controls="site-search" aria-expanded="false" aria-label="{{ esc_attr__('Search', 'sage') }}">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </button>
        <a class="header-icon header-cart" href="{{ esc_url(wc_get_cart_url()) }}" aria-label="Cart">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
          <span class="cart-count">{{ WC()?->cart ? WC()->cart->get_cart_contents_count() : 0 }}</span>
        </a>
      @endif
      <button class="hamburger" id="hamburger" type="button" aria-controls="mobile-menu" aria-label="Menu" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
  <div class="site-search" id="site-search" aria-hidden="true" inert>
    <div class="site-search__inner">
      @if (function_exists('get_product_search_form'))
        {!! get_product_search_form() !!}
      @else
        {!! get_search_form(false) !!}
      @endif
      <button class="site-search__close" id="search-close" aria-label="Close">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <p class="sage-guide site-search__hint" id="site-search-hint">{{ __('Try a product name or a few keywords.', 'sage') }}</p>
  </div>
</header>

<div class="mobile-menu" id="mobile-menu" role="dialog" aria-modal="true" aria-label="{{ esc_attr__('Navigation', 'sage') }}" aria-hidden="true" inert>
  <div class="mobile-menu__inner">
    <button class="mobile-menu__close" id="mobile-menu-close" aria-label="Close">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
    @if (has_nav_menu('primary_navigation'))
      {!! wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'mobile-nav__list', 'container' => false, 'fallback_cb' => false, 'echo' => false]) !!}
    @else
      <ul class="mobile-nav__list">
        {!! wp_list_pages(['title_li' => '', 'echo' => false, 'sort_column' => 'menu_order,post_title']) !!}
      </ul>
    @endif
    <div class="mobile-menu__footer">
      @if (function_exists('wc_get_page_permalink'))
        <a class="btn btn--outline" href="{{ esc_url(wc_get_page_permalink('myaccount')) }}">{{ __('My Account', 'sage') }}</a>
      @endif
    </div>
  </div>
</div>
<div class="mobile-menu__backdrop" id="menu-backdrop"></div>
