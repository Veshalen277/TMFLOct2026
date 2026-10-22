@php
  $collectionId = 'product-related';
  $layout = get_theme_mod('sage_related_layout', 'grid') === 'scroll' ? 'scroll' : 'grid';
  $columns = \App\Storefront::number(get_theme_mod('sage_related_columns', 4), 2, 6);
  $heading = apply_filters('woocommerce_product_related_products_heading', get_theme_mod('sage_related_heading', __('You may also like', 'sage')));
  $description = get_theme_mod('sage_related_description', __('A few more finds from the collection.', 'sage'));
@endphp
@if ($products)
<section class="sf-collection sf-collection--related" aria-labelledby="product-related-title" data-product-collection>
  <div class="sf-collection__header flex flex-wrap items-end justify-between gap-4">
    <div>
      <h2 id="product-related-title">{{ $heading ?: __('Related products', 'sage') }}</h2>
      @if ($description)<p class="sf-collection__intro">{{ $description }}</p>@endif
    </div>
    <div class="sf-collection__actions flex items-center gap-3">
      @if (get_theme_mod('sage_related_shop_link', true))<a class="sf-collection__link" href="{{ esc_url(wc_get_page_permalink('shop')) }}">{{ __('Explore the shop', 'sage') }} ↗</a>@endif
      @if ($layout === 'scroll')@include('partials.collection-controls', ['collectionId' => $collectionId])@endif
    </div>
  </div>
  @include('partials.product-collection', compact('products', 'collectionId', 'layout', 'columns'))
</section>
@endif
