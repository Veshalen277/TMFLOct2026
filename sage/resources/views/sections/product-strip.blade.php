@php
  $collectionId = 'home-strip-'.$strip['id'];
  $products = $strip['products'];
@endphp
@if ($products)
<section id="{{ $collectionId }}" class="sf-collection hf-container {{ get_theme_mod('sage_strips_spacing', 'comfortable') === 'compact' ? 'sf-collection--compact' : '' }}" aria-labelledby="{{ $collectionId }}-title" data-product-collection>
  <div class="sf-collection__header flex flex-wrap items-end justify-between gap-4">
    <h2 id="{{ $collectionId }}-title">{{ $strip['heading'] }}</h2>
    <div class="sf-collection__actions flex items-center gap-3">
      @if ($strip['url'])<a class="sf-collection__link" href="{{ esc_url($strip['url']) }}">{{ __('View collection', 'sage') }} <span aria-hidden="true">↗</span></a>@endif
      @if ($strip['layout'] === 'scroll')
        @include('partials.collection-controls', ['collectionId' => $collectionId])
      @endif
    </div>
  </div>
  @include('partials.product-collection', ['products' => $products, 'collectionId' => $collectionId, 'layout' => $strip['layout'], 'columns' => $strip['columns']])
</section>
@endif
