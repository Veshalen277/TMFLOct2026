<?php

/**

 * Customizer settings.

 *

 * Powers the announcement bar message and social links used in

 * resources/views/sections/header.blade.php and sections/footer.blade.php.

 * Setting IDs are kept as `ighoosen_*` for parity with the original theme

 * (so values carry over cleanly if a site ever switches between the two).

 */

namespace App;

add_action('customize_register', function ($wp_customize) {

    $wp_customize->add_panel('ighoosen_panel', [

        'title' => __('IG Hoosen Theme', 'sage'),

        'priority' => 30,

    ]);

    $wp_customize->add_section('ighoosen_topbar', [

        'title' => __('Top Bar', 'sage'),

        'panel' => 'ighoosen_panel',

    ]);

    $wp_customize->add_setting('ighoosen_topbar_message', [

        'default' => 'FREE delivery for orders R799 & over',

        'sanitize_callback' => 'wp_kses_post',

    ]);

    $wp_customize->add_control('ighoosen_topbar_message', [

        'label' => __('Announcement Message', 'sage'),

        'section' => 'ighoosen_topbar',

        'type' => 'text',

    ]);

    $wp_customize->add_section('ighoosen_social', [

        'title' => __('Social Links', 'sage'),

        'panel' => 'ighoosen_panel',

    ]);

    foreach (['facebook', 'instagram'] as $platform) {

        $wp_customize->add_setting("ighoosen_{$platform}", [

            'sanitize_callback' => 'esc_url_raw',

        ]);

        $wp_customize->add_control("ighoosen_{$platform}", [

            'label' => ucfirst($platform).' URL',

            'section' => 'ighoosen_social',

            'type' => 'url',

        ]);

    }

});

/** Native home-page content controls. */

add_action('customize_register', function ($wp_customize) {

    $wp_customize->add_section('sage_home', [

        'title' => __('Storefront Home Page', 'sage'),

        'priority' => 35,

    ]);

    $fields = [

        'eyebrow' => [__('Small heading', 'sage'), __('Something good, every day', 'sage')],

        'title' => [__('Main heading', 'sage'), __('Everyday finds.', 'sage')],

        'accent' => [__('Highlighted heading', 'sage'), __('Extraordinary value.', 'sage')],

        'description' => [__('Hero introduction', 'sage'), __('Discover thoughtful gifts, useful essentials and new favourites. Find a little more to love, all in one place.', 'sage')],

        'about' => [__('About the store', 'sage'), __('From a thoughtful gift to an everyday essential, our collection brings the little things together. Explore what is new, rediscover a favourite, and find something that feels just right.', 'sage')],

    ];

    foreach ($fields as $key => [$label, $default]) {

        $id = 'sage_home_'.$key;

        $wp_customize->add_setting($id, ['default' => $default, 'sanitize_callback' => 'sanitize_textarea_field']);

        $wp_customize->add_control($id, ['section' => 'sage_home', 'label' => $label, 'type' => in_array($key, ['description', 'about'], true) ? 'textarea' : 'text']);

    }

    $wp_customize->add_setting('sage_home_image', ['default' => 0, 'sanitize_callback' => 'absint']);

    $wp_customize->add_control(new \WP_Customize_Media_Control($wp_customize, 'sage_home_image', [

        'section' => 'sage_home', 'label' => __('Optional hero image', 'sage'),

        'description' => __('Leave empty to show featured products, or newest products when none are featured.', 'sage'),

        'mime_type' => 'image',

    ]));

});

/** Optional contact details; no admin email or invented business details are exposed. */
add_action('customize_register', function ($wp_customize) {
    $wp_customize->add_section('sage_contact', [
        'title' => __('Contact Page', 'sage'),
        'priority' => 36,
        'description' => __('Use the Contact — Monochrome page template, then add your existing Contact Form 7 block or shortcode to the page content. Empty contact details are hidden.', 'sage'),
    ]);
    $fields = [
        'intro' => [__('Introduction', 'sage'), 'textarea', 'sanitize_textarea_field', __('A question about a product, an order, or something else? We would love to hear from you.', 'sage')],
        'email' => [__('Public email address', 'sage'), 'email', 'sanitize_email', ''],
        'phone' => [__('Public phone number', 'sage'), 'text', 'sanitize_text_field', ''],
        'hours' => [__('Contact hours', 'sage'), 'textarea', 'sanitize_textarea_field', ''],
        'address' => [__('Address', 'sage'), 'textarea', 'sanitize_textarea_field', ''],
    ];
    foreach ($fields as $key => [$label, $type, $sanitize, $default]) {
        $id = 'sage_contact_'.$key;
        $wp_customize->add_setting($id, ['default' => $default, 'sanitize_callback' => $sanitize]);
        $wp_customize->add_control($id, ['section' => 'sage_contact', 'label' => $label, 'type' => $type]);
    }
});

/** Category navigation stays usable with or without animation. */
add_action('customize_register', function ($wp_customize) {
    $wp_customize->add_section('sage_category_strip', [
        'title' => __('Category Strip', 'sage'),
        'priority' => 37,
    ]);
    foreach (['enabled' => __('Show category strip', 'sage'), 'motion' => __('Enable gentle scrolling on desktop', 'sage')] as $key => $label) {
        $id = 'sage_category_strip_'.$key;
        $wp_customize->add_setting($id, ['default' => true, 'sanitize_callback' => 'rest_sanitize_boolean']);
        $wp_customize->add_control($id, ['section' => 'sage_category_strip', 'type' => 'checkbox', 'label' => $label]);
    }
});

/** Homepage slides above the existing jumbotron. */
add_action('customize_register', function ($wp_customize) {
    $wp_customize->add_section('sage_promo', [
        'title' => __('Homepage Slider', 'sage'), 'priority' => 34,
        'description' => __('Full-width banner directly below the header, above category navigation. Up to five slides; use their visibility and position controls to curate the order. Uploaded images replace placeholder URLs. Blank destination links use the published shop.', 'sage'),
    ]);
    foreach (['enabled' => [__('Show slider', 'sage'), true], 'autoplay' => [__('Automatically advance slides', 'sage'), false]] as $key => [$label, $default]) {
        $id = 'sage_promo_'.$key;
        $wp_customize->add_setting($id, ['default' => $default, 'sanitize_callback' => 'rest_sanitize_boolean']);
        $wp_customize->add_control($id, ['section' => 'sage_promo', 'label' => $label, 'type' => 'checkbox']);
    }
    $defaults = \App\View\Composers\PromoSlider::defaults();
    for ($i = 1; $i <= 5; $i++) {
        foreach (['eyebrow' => __('Small heading', 'sage'), 'title' => __('Headline', 'sage'), 'text' => __('Description', 'sage'), 'label' => __('Link label', 'sage'), 'url' => __('Link URL', 'sage')] as $key => $label) {
            $id = "sage_promo_{$i}_{$key}";
            $wp_customize->add_setting($id, ['default' => $defaults[$i][$key] ?? '', 'sanitize_callback' => $key === 'url' ? 'esc_url_raw' : 'sanitize_text_field']);
            $wp_customize->add_control($id, ['section' => 'sage_promo', 'label' => sprintf(__('Slide %1$d — %2$s', 'sage'), $i, $label), 'type' => $key === 'url' ? 'url' : 'text']);
        }
        $id = "sage_promo_{$i}_image";
        $wp_customize->add_setting($id, ['default' => 0, 'sanitize_callback' => 'absint']);
        $wp_customize->add_control(new \WP_Customize_Media_Control($wp_customize, $id, ['section' => 'sage_promo', 'label' => sprintf(__('Slide %d — image', 'sage'), $i), 'mime_type' => 'image']));
    }
});

/** Refresh transport renders the real Blade queries inside Customizer previews. */
add_action('customize_register', function ($customize) {
    $customize->add_panel('sage_storefront_layouts', ['title' => __('Storefront Layouts', 'sage'), 'priority' => 33]);
    foreach (['sage_product_strips' => __('Front Page Product Strips', 'sage'), 'sage_wc_layouts' => __('WooCommerce Layouts', 'sage')] as $id => $title) {
        $customize->add_section($id, ['title' => $title, 'panel' => 'sage_storefront_layouts']);
    }
    $field = static function ($id, $section, $label, $default, $type = 'checkbox', $choices = [], $min = 0, $max = 100) use ($customize) {
        $sanitize = match ($type) {
            'checkbox' => 'rest_sanitize_boolean',
            'number', 'range' => static fn ($value) => Storefront::number($value, $min, $max),
            'select' => static fn ($value) => is_scalar($value) && array_key_exists($value, $choices) ? $value : $default,
            'url' => 'esc_url_raw',
            default => 'sanitize_text_field',
        };
        $customize->add_setting($id, ['default' => $default, 'sanitize_callback' => $sanitize, 'transport' => 'refresh']);
        $customize->add_control($id, ['section' => $section, 'label' => $label, 'type' => $type, 'choices' => $choices, 'input_attrs' => ['min' => $min, 'max' => $max, 'step' => 1]]);
    };
    $field('sage_product_strips_enabled', 'sage_product_strips', __('Show product strips', 'sage'), true);
    $customize->add_setting('sage_product_strips', ['default' => Storefront::defaults(), 'sanitize_callback' => [Storefront::class, 'sanitizeStrips'], 'transport' => 'refresh']);
    $customize->add_control(new \App\Customizer\ProductStripsControl($customize, 'sage_product_strips', [
        'section' => 'sage_product_strips', 'label' => __('Product strips', 'sage'),
        'description' => __('Add, remove, reorder and hide strips. Select a category for category strips; newest, featured and sale strips can optionally use a category filter. Up to 20 strips, with 24 products each.', 'sage'),
    ]));
    $field('sage_strips_spacing', 'sage_product_strips', __('Section spacing', 'sage'), 'comfortable', 'select', ['compact' => __('Compact', 'sage'), 'comfortable' => __('Comfortable', 'sage')]);

    $field('sage_related_enabled', 'sage_wc_layouts', __('Show related products', 'sage'), true);
    $field('sage_related_refined', 'sage_wc_layouts', __('Use the matching theme card layout', 'sage'), true);
    $field('sage_related_heading', 'sage_wc_layouts', __('Related products heading', 'sage'), __('You may also like', 'sage'), 'text');
    $field('sage_related_description', 'sage_wc_layouts', __('Related products introduction', 'sage'), __('A few more finds from the collection.', 'sage'), 'text');
    $field('sage_related_layout', 'sage_wc_layouts', __('Related products layout', 'sage'), 'grid', 'select', ['grid' => __('Grid', 'sage'), 'scroll' => __('Scrollable row', 'sage')]);
    $field('sage_related_count', 'sage_wc_layouts', __('Related product count', 'sage'), 4, 'number', [], 1, 24);
    $field('sage_related_columns', 'sage_wc_layouts', __('Related desktop columns', 'sage'), 4, 'number', [], 2, 6);
    $field('sage_related_shop_link', 'sage_wc_layouts', __('Show a shop link beside related products', 'sage'), true);
    foreach (['hover' => __('Enable card hover effects', 'sage'), 'ratings' => __('Show card ratings', 'sage'), 'cart' => __('Show card purchase buttons', 'sage')] as $key => $label) {
        $field('sage_cards_'.$key, 'sage_wc_layouts', $label, true);
    }

    // Existing homepage content stays available and can be independently hidden.
    foreach (['hero' => __('Show the original introduction below the slider', 'sage'), 'categories' => __('Show category tiles', 'sage'), 'story' => __('Show the store story', 'sage'), 'reviews' => __('Show customer reviews', 'sage'), 'help' => __('Show help and FAQs', 'sage'), 'cta' => __('Show the final shop banner', 'sage')] as $key => $label) {
        $field('sage_home_show_'.$key, 'sage_home', $label, true);
    }
    $field('sage_promo_arrows', 'sage_promo', __('Show previous/next buttons', 'sage'), true);
    $field('sage_promo_interval', 'sage_promo', __('Autoplay interval (seconds)', 'sage'), 7, 'number', [], 4, 20);
    $field('sage_promo_height', 'sage_promo', __('Desktop slider height (pixels)', 'sage'), 520, 'number', [], 280, 800);
    $field('sage_promo_mobile_height', 'sage_promo', __('Mobile slider height (pixels)', 'sage'), 440, 'number', [], 280, 640);
    $field('sage_promo_overlay', 'sage_promo', __('Image darkening (%)', 'sage'), 55, 'range', [], 20, 85);
    for ($i = 1; $i <= 5; $i++) {
        $field("sage_promo_{$i}_enabled", 'sage_promo', sprintf(__('Slide %d — show', 'sage'), $i), true);
        $field("sage_promo_{$i}_position", 'sage_promo', sprintf(__('Slide %d — position (lowest first)', 'sage'), $i), $i * 10, 'number', [], 1, 100);
        $field("sage_promo_{$i}_image_url", 'sage_promo', sprintf(__('Slide %d — fallback image URL', 'sage'), $i), $i <= 2 ? "https://via.placeholder.com/1920x720/333333/ffffff?text=Store+Highlight+{$i}" : '', 'url');
    }
}, 20);
