@extends('layouts.app')

@section('content')
  <div class="page-wrap">
    @while (have_posts()) @php(the_post())
      <article @php(post_class('page-article'))>
        <header class="page-article__header">
          <h1 class="page-article__title">{!! get_the_title() !!}</h1>
        </header>
        <div class="page-article__content entry-content">
          @php(the_content())
        </div>
      </article>
    @endwhile
  </div>
@endsection
