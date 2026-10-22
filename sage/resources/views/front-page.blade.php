@extends('layouts.app')

@section('content')
<div class="hf-home">
  @if (! get_theme_mod('sage_home_show_hero', true))<h1 class="sr-only">{{ get_bloginfo('name') }}</h1>@endif
  @if (get_theme_mod('sage_home_show_hero', true))
  <section class="hf-hero" aria-labelledby="hf-title">
    <div class="hf-container hf-hero__edition" aria-hidden="true"><span>{{ get_bloginfo('name') }} / {{ __('The everyday collection', 'sage') }}</span><span>{{ __('Good things, found here', 'sage') }} <b>✳</b></span></div>
    <div class="hf-container hf-hero__grid">
      <div class="hf-hero__copy">
        <p class="hf-eyebrow" data-home-intro><span class="hf-dot" aria-hidden="true"></span>{{ $storefront['eyebrow'] }}</p>
        <h1 id="hf-title" data-home-intro>{{ $storefront['title'] }}<br><span>{{ $storefront['accent'] }}</span></h1>
        <p class="hf-hero__description" data-home-intro>{{ $storefront['description'] }}</p>
        <div class="hf-actions" data-home-intro>
          @if ($storefront['enabled'])
            <a class="hf-button" href="{{ esc_url($storefront['shop']) }}">{{ __('Explore the shop', 'sage') }} <span aria-hidden="true">↗</span></a>
          @endif
          @if ($storefront['specialsAnchor'])
            <a class="hf-text-link" href="#{{ $storefront['specialsAnchor'] }}">{{ __('See current specials', 'sage') }} <span aria-hidden="true">→</span></a>
          @elseif ($storefront['categories'] && get_theme_mod('sage_home_show_categories', true))
            <a class="hf-text-link" href="#home-categories">{{ __('Find your category', 'sage') }} <span aria-hidden="true">↓</span></a>
          @endif
        </div>
        <p class="hf-hero__footnote" data-home-intro>{{ get_bloginfo('name') }} <span aria-hidden="true">/</span> {{ __('A little more to discover.', 'sage') }}</p>
      </div>
      <div class="hf-hero__visual" data-home-intro>
        <div class="hf-hero__orbit" aria-hidden="true"></div>
        <span class="hf-hero__cross hf-hero__cross--top" aria-hidden="true">+</span>
        <span class="hf-hero__cross hf-hero__cross--bottom" aria-hidden="true">+</span>
        <span class="hf-hero__vertical" aria-hidden="true">{{ __('Everyday / Unexpected', 'sage') }}</span>
        @if ($storefront['heroImage'])
          <div class="hf-hero__custom">{!! wp_get_attachment_image($storefront['heroImage'], 'large', false, ['fetchpriority' => 'high', 'loading' => 'eager']) !!}</div>
        @elseif ($storefront['heroProducts'])
          @foreach ($storefront['heroProducts'] as $heroItem)
            <a class="hf-hero-card hf-hero-card--{{ $loop->iteration }}" href="{{ esc_url($heroItem->get_permalink()) }}">
              <span class="hf-hero-card__label">{{ __('In the collection', 'sage') }} <span aria-hidden="true">{{ sprintf('%02d', $loop->iteration) }}</span></span>
              <div class="hf-hero-card__image">{!! $heroItem->get_image('medium_large', ['loading' => 'eager', 'fetchpriority' => $loop->first ? 'high' : 'auto']) !!}</div>
              <div class="hf-hero-card__caption"><span>{{ $heroItem->get_name() }}</span><strong>{!! $heroItem->get_price_html() !!}</strong></div>
              <span class="hf-hero-card__arrow" aria-hidden="true">↗</span>
            </a>
          @endforeach
        @else
          <div class="hf-hero__wordmark" aria-hidden="true">{{ __('Find.', 'sage') }}<br>{{ __('Love.', 'sage') }}<br><em>{{ __('Repeat.', 'sage') }}</em></div>
        @endif
        <div class="hf-hero__stamp"><span>{{ __('Your next', 'sage') }}</span><strong>{{ __('great find.', 'sage') }}</strong><span aria-hidden="true">✦</span></div>
      </div>
    </div>
  </section>

  <div class="hf-value-strip">
    <div class="hf-container">
      <span><b aria-hidden="true">01</b>{{ __('Discover new favourites', 'sage') }}</span>
      <span><b aria-hidden="true">02</b>{{ __('Explore current offers', 'sage') }}</span>
      <span><b aria-hidden="true">03</b>{{ __('Shop at your own pace', 'sage') }}</span>
    </div>
  </div>

  @endif

  @if ($storefront['categories'] && get_theme_mod('sage_home_show_categories', true))
    <section class="hf-section hf-container" id="home-categories" aria-labelledby="hf-categories-title">
      <div class="hf-section-head" data-home-reveal>
        <div><p class="hf-eyebrow">{{ __('A good place to start', 'sage') }}</p><h2 id="hf-categories-title">{{ __('What are you looking for?', 'sage') }}</h2></div>
        <a class="hf-text-link" href="{{ esc_url($storefront['shop']) }}">{{ __('Shop everything', 'sage') }} <span aria-hidden="true">↗</span></a>
      </div>
      <div class="hf-categories">
        @foreach ($storefront['categories'] as $category)
          <a class="hf-category" href="{{ esc_url($category['url']) }}" data-home-reveal>
            <div class="hf-category__image">
              <span class="hf-category__number" aria-hidden="true">{{ sprintf('%02d', $loop->iteration) }}</span>
              @if ($category['image'])
                {!! wp_get_attachment_image($category['image'], 'medium', false, ['loading' => 'lazy', 'alt' => '']) !!}
              @else
                <span class="hf-category__initial" aria-hidden="true">{{ mb_substr($category['name'], 0, 1) }}</span>
              @endif
              <span class="hf-category__arrow" aria-hidden="true">↗</span>
            </div>
            <h3>{{ $category['name'] }}</h3>
            <span class="hf-category__count">{{ sprintf(_n('%s product', '%s products', $category['count'], 'sage'), number_format_i18n($category['count'])) }}</span>
          </a>
        @endforeach
      </div>
    </section>
  @endif

  @foreach ($productStrips as $strip)
    @include('sections.product-strip', ['strip' => $strip])
  @endforeach

  @if (get_theme_mod('sage_home_show_story', true))
  <section class="hf-story hf-container" aria-labelledby="hf-story-title" data-home-reveal>
    <div class="hf-story__brand"><span class="hf-eyebrow">{{ get_bloginfo('name') }}</span><span class="hf-story__symbol" aria-hidden="true">✳</span><span>{{ __('Everyday essentials. Unexpected favourites.', 'sage') }}</span></div>
    <div class="hf-story__copy"><p class="hf-eyebrow">{{ __('More than a shopping list', 'sage') }}</p><h2 id="hf-story-title">{{ __('Little things.', 'sage') }}<br>{{ __('A lot to love.', 'sage') }}</h2><p>{{ $storefront['about'] }}</p>
      @if ($storefront['enabled'])<a class="hf-text-link" href="{{ esc_url($storefront['shop']) }}">{{ __('Find something for you', 'sage') }} <span aria-hidden="true">↗</span></a>@endif
    </div>
  </section>

  @endif

  @if ($storefront['reviews'] && get_theme_mod('sage_home_show_reviews', true))
    <section class="hf-section hf-container" aria-labelledby="hf-reviews-title">
      <div class="hf-section-head" data-home-reveal><div><p class="hf-eyebrow">{{ __('In their own words', 'sage') }}</p><h2 id="hf-reviews-title">{{ __('From our customers.', 'sage') }}</h2></div></div>
      <div class="hf-reviews">
        @foreach ($storefront['reviews'] as $review)
          <figure class="hf-review" data-home-reveal><span class="hf-review__quote" aria-hidden="true">“</span><div class="hf-review__stars" aria-label="{{ sprintf(__('%d out of 5 stars', 'sage'), $review['rating']) }}"><span aria-hidden="true">{{ str_repeat('★', $review['rating']) }}{{ str_repeat('☆', 5 - $review['rating']) }}</span></div><blockquote>{{ $review['text'] }}</blockquote><figcaption>{{ $review['name'] }} <span>{{ __('Product review', 'sage') }}</span></figcaption></figure>
        @endforeach
      </div>
    </section>
  @endif

  @if (get_theme_mod('sage_home_show_help', true))
  <section class="hf-help hf-container hf-section" aria-labelledby="hf-help-title">
    <div data-home-reveal><p class="hf-eyebrow">{{ __('Before you check out', 'sage') }}</p><h2 id="hf-help-title">{{ __('A little help', 'sage') }}<br>{{ __('goes a long way.', 'sage') }}</h2>
      @if ($storefront['contact'])<a class="hf-text-link" href="{{ esc_url($storefront['contact']) }}">{{ __('Talk to us', 'sage') }} <span aria-hidden="true">↗</span></a>@endif
    </div>
    <div class="hf-faqs" data-home-reveal>
      <details><summary>{{ __('How do I find the right product?', 'sage') }}<span aria-hidden="true">+</span></summary><div><p>{{ __('Browse a category or search the shop. Each product page has its current price, availability and any options you need to select.', 'sage') }}</p></div></details>
      <details><summary>{{ __('Where can I check delivery options?', 'sage') }}<span aria-hidden="true">+</span></summary><div><p>{{ __('Enter your delivery address at checkout to see the available shipping methods and charges for your order.', 'sage') }}</p>@if ($storefront['shipping'])<a href="{{ esc_url($storefront['shipping']) }}">{{ __('Read our delivery information', 'sage') }}</a>@endif</div></details>
      <details><summary>{{ __('Can I get help with an order?', 'sage') }}<span aria-hidden="true">+</span></summary><div><p>{{ __('Keep your order number handy when contacting us so we can help you with your purchase.', 'sage') }}</p>@if ($storefront['returns'])<a href="{{ esc_url($storefront['returns']) }}">{{ __('Read our returns policy', 'sage') }}</a>@endif</div></details>
    </div>
  </section>

  @endif

  @if ($storefront['enabled'] && get_theme_mod('sage_home_show_cta', true))
    <section class="hf-last-call"><div class="hf-container"><p class="hf-eyebrow">{{ __('Your next great find is waiting', 'sage') }}</p><h2>{{ __('Go on. Have a look.', 'sage') }}</h2><a class="hf-button hf-button--dark" href="{{ esc_url($storefront['shop']) }}">{{ __('Shop the collection', 'sage') }} <span aria-hidden="true">↗</span></a></div></section>
  @endif
</div>
@endsection
