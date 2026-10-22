<div id="{{ $collectionId }}-products" class="sf-collection__products sf-collection__products--{{ $layout === 'scroll' ? 'scroll' : 'grid' }}" style="--sf-columns: {{ \App\Storefront::number($columns, 2, 6) }}" @if ($layout === 'scroll') data-collection-track tabindex="0" role="region" aria-labelledby="{{ $collectionId }}-title" @endif>
  @foreach ($products as $item)
    @include('partials.home.product', ['item' => $item])
  @endforeach
</div>
