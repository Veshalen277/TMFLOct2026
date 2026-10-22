@php
  // The footer is rendered on every page, including when WooCommerce is disabled.
  $footerAccountUrl = function_exists('wc_get_page_permalink')
    ? wc_get_page_permalink('myaccount')
    : wp_login_url();
  $footerLostPasswordUrl = function_exists('wc_get_account_endpoint_url')
    ? wc_get_account_endpoint_url('lost-password')
    : wp_lostpassword_url();
  $footerEditAccountUrl = function_exists('wc_get_account_endpoint_url')
    ? wc_get_account_endpoint_url('edit-account')
    : (is_user_logged_in() ? get_edit_profile_url() : wp_login_url());
  $footerOrdersUrl = function_exists('wc_get_account_endpoint_url')
    ? wc_get_account_endpoint_url('orders')
    : null;
@endphp

<footer class="site-footer">
  <div class="footer-signoff"><div><p>{{ __('A little more to discover.', 'sage') }}</p><span>{{ get_bloginfo('name') }}</span></div><a href="#app" class="footer-top" aria-label="{{ esc_attr__('Back to top', 'sage') }}">↑</a></div>
  <div class="site-footer__inner">
    <div class="footer-col footer-col--brand">
      @if (has_custom_logo())
        {!! get_custom_logo() !!}
      @else
        <a class="site-logo__text" href="{{ esc_url(home_url('/')) }}">{{ get_bloginfo('name') }}</a>
      @endif
      <p class="footer-tagline">{{ get_bloginfo('description') }}</p>
      <div class="footer-social">
        @if ($fb = get_option('ighoosen_facebook'))
          <a href="{{ esc_url($fb) }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg></a>
        @endif
        @if ($ig = get_option('ighoosen_instagram'))
          <a href="{{ esc_url($ig) }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg></a>
        @endif
      </div>
    </div>

    <div class="footer-col">
      <h4 class="footer-col__title">{{ __('Useful Links', 'sage') }}</h4>
      @if (has_nav_menu('footer_links'))
        {!! wp_nav_menu(['theme_location' => 'footer_links', 'menu_class' => 'footer-nav', 'container' => false, 'echo' => false]) !!}
      @else
        <ul class="footer-nav">
          <li><a href="{{ esc_url($footerAccountUrl) }}">{{ __('My Account', 'sage') }}</a></li>
          <li><a href="{{ esc_url($footerLostPasswordUrl) }}">{{ __('Lost Password', 'sage') }}</a></li>
          @if ($footerOrdersUrl)
            <li><a href="{{ esc_url($footerOrdersUrl) }}">{{ __('Orders', 'sage') }}</a></li>
          @endif
          @if (is_user_logged_in())<li><a href="{{ esc_url(wp_logout_url(home_url())) }}">{{ __('Logout', 'sage') }}</a></li>@endif
        </ul>
      @endif
    </div>

    <div class="footer-col">
      <h4 class="footer-col__title">{{ __('Legal', 'sage') }}</h4>
      @if (has_nav_menu('footer_legal'))
        {!! wp_nav_menu(['theme_location' => 'footer_legal', 'menu_class' => 'footer-nav', 'container' => false, 'echo' => false]) !!}
      @else
        <ul class="footer-nav">
          @foreach ([
            'privacy-policy' => __('Privacy Policy', 'sage'),
            'returns-and-refund-policy' => __('Returns & Refund Policy', 'sage'),
            'shipping' => __('Shipping', 'sage'),
            'terms-and-conditions' => __('Terms and Conditions', 'sage'),
            'contact' => __('Contact Us', 'sage'),
          ] as $policySlug => $policyLabel)
            @php($policyPage = get_page_by_path($policySlug))
            @if ($policyPage && get_post_status($policyPage) === 'publish')
              <li><a href="{{ esc_url(get_permalink($policyPage)) }}">{{ $policyLabel }}</a></li>
            @endif
          @endforeach
        </ul>
      @endif
    </div>

    <div class="footer-col footer-col--payments">
      <h4 class="footer-col__title">{{ __('Secure Payments', 'sage') }}</h4>
      <div class="payment-badge">
        <p class="payment-badge__label">SECURE PAYMENTS BY</p>
        <div class="payment-icons">
          <span class="payment-icon">PayFast</span>
          <span class="payment-icon">EFT</span>
          <span class="payment-icon">Visa</span>
          <span class="payment-icon">MC</span>
        </div>
      </div>
    </div>
  </div>
  <div class="site-footer__bottom">
    <p>&copy;{{ date('Y') }} {{ get_bloginfo('name') }} &mdash; All Rights Reserved</p>
    <nav class="footer-bottom-nav">
      <a href="{{ esc_url($footerLostPasswordUrl) }}">{{ __('Lost password', 'sage') }}</a>
      <a href="{{ esc_url($footerEditAccountUrl) }}">{{ __('Account details', 'sage') }}</a>
      @if ($footerOrdersUrl)
        <a href="{{ esc_url($footerOrdersUrl) }}">{{ __('Orders', 'sage') }}</a>
      @endif
      @if (is_user_logged_in())<a href="{{ esc_url(wp_logout_url(home_url())) }}">{{ __('Logout', 'sage') }}</a>@endif
    </nav>
  </div>
</footer>
