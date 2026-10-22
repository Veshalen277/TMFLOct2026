@extends('layouts.app')

@section('content')
  <div class="page-wrap">
    @if (have_posts())
      @while (have_posts()) @php(the_post())
        <article @php(post_class())>
          <h2 class="entry-title"><a href="{{ get_permalink() }}">{!! get_the_title() !!}</a></h2>
          @php(the_excerpt())
        </article>
      @endwhile

      {!! get_the_posts_navigation() !!}
    @else
      <p>{{ __('No posts found.', 'sage') }}</p>
    @endif
  </div>
@endsection
