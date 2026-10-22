<article class="hf-product {{ get_theme_mod('sage_cards_hover', true) ? '' : 'hf-product--no-hover' }}" data-home-reveal>
  <a class="hf-product__image" href="{{ esc_url($item->get_permalink()) }}" aria-label="{{ esc_attr($item->get_name()) }}">
    {!! $item->get_image('woocommerce_thumbnail', ['loading' => 'lazy', 'decoding' => 'async']) !!}
    @if (! $item->is_in_stock())
      <span class="hf-product__badge hf-product__badge--quiet">{{ __('Sold out', 'sage') }}</span>
    @elseif ($item->is_on_sale())
      <span class="hf-product__badge">{{ __('Special', 'sage') }}</span>
    @endif
    <span class="hf-product__peek" aria-hidden="true">↗</span>
  </a>
  <div class="hf-product__body">
    @if (get_theme_mod('sage_cards_ratings', true) && $item->get_rating_count() > 0 && wc_review_ratings_enabled())
      <div class="hf-product__rating">{!! wc_get_rating_html($item->get_average_rating(), $item->get_rating_count()) !!}<span>({{ number_format_i18n($item->get_rating_count()) }})</span></div>
    @endif
    <h3><a href="{{ esc_url($item->get_permalink()) }}">{{ $item->get_name() }}</a></h3>
    <div class="hf-product__price">{!! $item->get_price_html() !!}</div>
    @if (get_theme_mod('sage_cards_cart', true))
    <div class="hf-product__action">
      @php
        $previousProduct = $GLOBALS['product'] ?? null;
        $GLOBALS['product'] = $item;
        try {
            if (function_exists('woocommerce_template_loop_add_to_cart')) {
                woocommerce_template_loop_add_to_cart();
            }
        } finally {
            $GLOBALS['product'] = $previousProduct;
        }
      @endphp
    </div>
    @endif
  </div>
</article>
