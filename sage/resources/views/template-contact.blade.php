{{--
  Template Name: Contact — Monochrome
--}}

@extends('layouts.app')

@section('content')
  @while (have_posts())
    <?php the_post(); ?>
    @php
      $contactIntro = get_theme_mod('sage_contact_intro', __('A question about a product, an order, or something else? We would love to hear from you.', 'sage'));
      $contactEmail = sanitize_email(get_theme_mod('sage_contact_email', ''));
      $contactPhone = get_theme_mod('sage_contact_phone', '');
      $contactDial = preg_replace('/[^0-9+]/', '', $contactPhone);
      $contactAddress = get_theme_mod('sage_contact_address', '');
      $contactHours = get_theme_mod('sage_contact_hours', '');
      $contactContent = trim((string) get_the_content());
      $contactHasContent = trim(preg_replace('/<!--.*?-->/s', '', $contactContent)) !== '';
      $contactPrivacy = get_privacy_policy_url();
      $contactCf7Unavailable = ! shortcode_exists('contact-form-7') && (preg_match('/\[contact-form-7(?:\s|\])/', $contactContent) || str_contains($contactContent, 'wp:contact-form-7/'));
      $contactHasDetails = $contactEmail || $contactDial || $contactAddress || $contactHours;
    @endphp
    <article class="sage-contact">
      <header class="sage-contact__heading">
        <p class="sage-contact__eyebrow">{{ __('A conversation starts here', 'sage') }}</p>
        <h1>{!! get_the_title() !!}<span aria-hidden="true">↗</span></h1>
        @if ($contactIntro)<p class="sage-contact__intro">{{ $contactIntro }}</p>@endif
      </header>
      <div class="sage-contact__layout">
        <aside class="sage-contact__details" aria-label="{{ esc_attr__('Contact information', 'sage') }}">
          <p class="sage-contact__eyebrow">{{ __('Here to help', 'sage') }}</p>
          <h2>{{ __('Let’s talk.', 'sage') }}</h2>
          <p class="sage-contact__note">{{ __('For an order enquiry, include your order number so we can find the right details.', 'sage') }}</p>
          @if ($contactEmail || $contactDial || $contactAddress || $contactHours)
            <dl class="sage-contact__channels">
              @if ($contactEmail)
                <div><dt>{{ __('Email', 'sage') }}</dt><dd><a href="{{ esc_url('mailto:'.$contactEmail) }}">{{ $contactEmail }} <span aria-hidden="true">↗</span></a></dd></div>
              @endif
              @if ($contactDial)
                <div><dt>{{ __('Phone', 'sage') }}</dt><dd><a href="{{ esc_url('tel:'.$contactDial) }}">{{ $contactPhone }} <span aria-hidden="true">↗</span></a></dd></div>
              @endif
              @if ($contactHours)<div><dt>{{ __('Hours', 'sage') }}</dt><dd>{{ $contactHours }}</dd></div>@endif
              @if ($contactAddress)<div><dt>{{ __('Address', 'sage') }}</dt><dd>{{ $contactAddress }}</dd></div>@endif
            </dl>
          @endif
          <div class="sage-contact__signature" aria-hidden="true"><span>✳</span>{{ get_bloginfo('name') }}</div>
        </aside>
        <section class="sage-contact__panel" aria-labelledby="contact-message-title">
          <div class="sage-contact__panel-heading"><h2 id="contact-message-title">{{ __('Send a message', 'sage') }}</h2><span aria-hidden="true">01 /</span></div>
          <div class="sage-contact-form">
            @if ($contactHasContent && ! $contactCf7Unavailable)
              @php(the_content())
            @elseif (current_user_can('edit_post', get_the_ID()))
              <p class="sage-contact__setup">{{ $contactCf7Unavailable ? __('Activate Contact Form 7 to display the form already added to this page.', 'sage') : __('Add your Contact Form 7 block or shortcode to this page’s content. Its fields will pick up the theme styling automatically.', 'sage') }}</p>
            @else
              <p>{{ $contactHasDetails ? __('The contact form is currently unavailable. Please use the contact details on this page.', 'sage') : __('The contact form is currently unavailable. Please check back shortly.', 'sage') }}</p>
            @endif
          </div>
          @if ($contactPrivacy)
            <p class="sage-contact__privacy"><a href="{{ esc_url($contactPrivacy) }}">{{ __('Read our privacy policy', 'sage') }} <span aria-hidden="true">↗</span></a></p>
          @endif
        </section>
      </div>
    </article>
  @endwhile
@endsection
