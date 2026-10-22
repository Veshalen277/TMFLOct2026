@if ($promoSlides)
<section class="sf-hero" data-storefront-slider data-autoplay="{{ $promoAutoplay ? 'true' : 'false' }}" data-interval="{{ $promoInterval }}" style="--sf-hero-height: {{ $promoHeight }}px; --sf-hero-mobile-height: {{ $promoMobileHeight }}px; --sf-overlay: {{ $promoOverlay / 100 }}" aria-label="{{ esc_attr__('Store highlights', 'sage') }}" aria-roledescription="{{ esc_attr__('carousel', 'sage') }}">
  <div class="sf-hero__viewport" data-storefront-viewport>
    @foreach ($promoSlides as $slide)
      <article class="sf-hero__slide" data-storefront-slide role="group" aria-roledescription="{{ esc_attr__('slide', 'sage') }}" aria-label="{{ sprintf(__('%1$d of %2$d', 'sage'), $loop->iteration, count($promoSlides)) }}">
        <div class="sf-hero__image" aria-hidden="true">
          @if ($slide['image'])
            {!! wp_get_attachment_image($slide['image'], 'full', false, ['loading' => $loop->first ? 'eager' : 'lazy', 'fetchpriority' => $loop->first ? 'high' : 'auto', 'decoding' => 'async', 'sizes' => '100vw', 'alt' => '']) !!}
          @elseif ($slide['imageUrl'])
            <img src="{{ esc_url($slide['imageUrl']) }}" alt="" width="1920" height="720" loading="{{ $loop->first ? 'eager' : 'lazy' }}" fetchpriority="{{ $loop->first ? 'high' : 'auto' }}" decoding="async">
          @endif
        </div>
        <div class="sf-hero__copy">
          @if ($slide['eyebrow'])<p class="sf-hero__eyebrow">{{ $slide['eyebrow'] }}</p>@endif
          @if ($slide['title'])<h2>{{ $slide['title'] }}</h2>@endif
          @if ($slide['text'])<p class="sf-hero__text">{{ $slide['text'] }}</p>@endif
          @if ($slide['url'] && $slide['label'])<a class="sf-hero__link" href="{{ esc_url($slide['url']) }}">{{ $slide['label'] }} <span aria-hidden="true">↗</span></a>@endif
        </div>
      </article>
    @endforeach
  </div>
  @if (count($promoSlides) > 1)
    <div class="sf-hero__controls" data-storefront-controls hidden>
      @if ($promoArrows)<button type="button" data-storefront-prev aria-label="{{ esc_attr__('Previous slide', 'sage') }}">←</button>@endif
      <span data-storefront-status aria-live="polite" aria-atomic="true">1 / {{ count($promoSlides) }}</span>
      @if ($promoArrows)<button type="button" data-storefront-next aria-label="{{ esc_attr__('Next slide', 'sage') }}">→</button>@endif
      @if ($promoAutoplay)<button type="button" data-storefront-play data-play-label="{{ esc_attr__('Play slides', 'sage') }}" data-pause-label="{{ esc_attr__('Pause slides', 'sage') }}" aria-label="{{ esc_attr__('Pause slides', 'sage') }}">Ⅱ</button>@endif
    </div>
  @endif
</section>
@endif
