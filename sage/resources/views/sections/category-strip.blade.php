@if ($stripCategories)
  <nav class="sage-category-strip" aria-label="{{ esc_attr__('Product categories', 'sage') }}" data-category-strip data-motion="{{ $stripMotion ? 'true' : 'false' }}">
    <div class="sage-category-strip__inner">
      <div class="sage-category-strip__viewport" data-category-viewport>
        <div class="sage-category-strip__track" data-category-track>
          <ul class="sage-category-strip__list" data-category-list>
            @foreach ($stripCategories as $category)
              <li><a href="{{ esc_url($category['url']) }}" dir="auto" @if ($category['current']) aria-current="page" @endif>{{ $category['name'] }}</a></li>
            @endforeach
          </ul>
        </div>
      </div>
      <button class="sage-category-strip__toggle" type="button" data-category-toggle hidden aria-pressed="false" aria-label="{{ esc_attr__('Pause category scrolling', 'sage') }}" data-pause-label="{{ esc_attr__('Pause category scrolling', 'sage') }}" data-play-label="{{ esc_attr__('Resume category scrolling', 'sage') }}">
        <svg class="sage-category-strip__pause" width="12" height="12" viewBox="0 0 12 12" aria-hidden="true"><path d="M3 2v8M9 2v8" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>
        <svg class="sage-category-strip__play" width="12" height="12" viewBox="0 0 12 12" aria-hidden="true"><path d="m4 2 6 4-6 4Z" fill="currentColor"/></svg>
      </button>
    </div>
  </nav>
@endif
