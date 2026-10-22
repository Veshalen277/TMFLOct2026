@extends('layouts.app')

@section('content')
  @if (! function_exists('wc_get_page_permalink') || ! function_exists('woocommerce_content'))
    @while(have_posts())
      <?php the_post(); ?>
      <?php the_content(); ?>
    @endwhile
  @else
  @php
    $pageType = is_cart() ? 'cart' : (is_checkout() ? (is_order_received_page() ? 'confirmation' : 'checkout') : (is_account_page() ? 'account' : (is_product() ? 'product' : 'shop')));
    $commerceConfig = [
      'page' => $pageType,
      'shop' => wc_get_page_permalink('shop'),
      'cart' => wc_get_cart_url(),
      'checkout' => wc_get_checkout_url(),
      'account' => wc_get_page_permalink('myaccount'),
      'search' => home_url('/'),
      'labels' => [
        'shop' => __('Shop', 'sage'), 'cart' => __('Cart', 'sage'),
        'checkout' => __('Checkout', 'sage'), 'account' => __('My account', 'sage'),
        'search' => __('Search products', 'sage'), 'grid' => __('Grid', 'sage'),
        'list' => __('List', 'sage'), 'view' => __('Product view', 'sage'),
        'details' => __('Product details', 'sage'), 'purchase' => __('Choose options', 'sage'),
        'top' => __('Back to top', 'sage'), 'added' => __('Added to your cart', 'sage'),
        'dismiss' => __('Dismiss', 'sage'), 'confirmation' => __('Order received', 'sage'),
      ],
    ];
  @endphp
  <div class="wc-page-wrap" data-commerce-page="{{ $pageType }}">
    <div id="sage-commerce-root" data-config="{{ wp_json_encode($commerceConfig) }}"></div>
    @if ($pageType === 'shop')
      <p class="sage-guide sage-commerce-guide">{{ __('Sort by price or newest arrivals to narrow the collection.', 'sage') }}</p>
    @elseif ($pageType === 'product')
      @php($guideProduct = wc_get_product(get_queried_object_id()))
      @if ($guideProduct && $guideProduct->is_type('variable') && $guideProduct->is_purchasable() && $guideProduct->is_in_stock())
        <p class="sage-guide sage-commerce-guide">{{ __('Choose your options to see the matching price and availability.', 'sage') }}</p>
      @endif
    @elseif (in_array($pageType, ['cart', 'checkout'], true) && ! is_wc_endpoint_url('order-pay') && WC()?->cart && WC()->cart->needs_shipping())
      <p class="sage-guide sage-commerce-guide">{{ __('Delivery options and charges are based on your address at checkout.', 'sage') }}</p>
    @endif
    <div id="sage-commerce-content">
      @if (is_cart() || is_checkout() || is_account_page())
        @while(have_posts())
          <?php the_post(); ?>
          <?php the_content(); ?>
        @endwhile
      @else
        <?php woocommerce_content(); ?>
      @endif
    </div>
  </div>
  @endif
@endsection
