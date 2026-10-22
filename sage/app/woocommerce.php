<?php

/**

 * WooCommerce integration.

 *

 * Mirrors the WooCommerce setup from the standalone IG Hoosen theme:

 * theme supports, product image sizes, removal of WooCommerce's default

 * content wrappers (we supply our own via resources/views/woocommerce.blade.php),

 * and the cart-fragments filter used by the header's cart badge.

 */

namespace App;

// Keep WooCommerce's related-product selection, filters and exclusions intact.
add_action('wp', function () {
    if (! get_theme_mod('sage_related_enabled', true)) {
        remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
    }
});

add_filter('woocommerce_output_related_products_args', function ($args) {
    $args['posts_per_page'] = Storefront::number(get_theme_mod('sage_related_count', 4), 1, 24);
    $args['columns'] = Storefront::number(get_theme_mod('sage_related_columns', 4), 2, 6);
    return $args;
});

add_filter('woocommerce_product_related_products_heading', function ($heading) {
    return get_theme_mod('sage_related_heading', __('You may also like', 'sage')) ?: $heading;
});

add_filter('wc_get_template', function ($template, $name) {
    if ($name === 'single-product/related.php' && get_theme_mod('sage_related_refined', true)) {
        return get_theme_file_path('app/woocommerce/related.php');
    }
    return $template;
}, 20, 2);

/**

 * WooCommerce-specific theme supports.

 *

 * @return void

 */

add_action('after_setup_theme', function () {

    if (! class_exists('WooCommerce')) {

        return;

    }

    add_theme_support('woocommerce', [

        'thumbnail_image_width' => 480,

    ]);

    add_theme_support('wc-product-gallery-zoom');

    add_theme_support('wc-product-gallery-lightbox');

    add_theme_support('wc-product-gallery-slider');

    /**

     * We render our own wrapper markup in resources/views/woocommerce.blade.php

     * (a single `.wc-page-wrap` div), so remove WooCommerce's default

     * wrapper hooks to avoid double-wrapping the content.

     */

    remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper');

    remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end');

    remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar');

});

/**

 * Route every WooCommerce page (shop, product, cart, checkout, account)

 * through the theme's Blade-rendered `woocommerce.blade.php` view instead

 * of whatever WooCommerce or Acorn's own resolver would otherwise pick.

 *

 * Acorn's Sage view-finder hooks `template_include` at priority 100

 * (see Roots\Acorn\Sage\SageServiceProvider); WooCommerce's own template

 * loader hooks it at the default priority of 10. Running at priority 101

 * guarantees this filter has the final say for WooCommerce requests, and

 * hands off to the theme-root `woocommerce.php`, which in turn renders

 * `resources/views/woocommerce.blade.php`.

 *

 * @return void

 */

add_filter('template_include', function ($template) {

    if (! function_exists('is_woocommerce') || ! function_exists('is_cart')

        || ! function_exists('is_checkout') || ! function_exists('is_account_page')

        || ! function_exists('wc_get_page_permalink') || ! function_exists('woocommerce_content')) {

        return $template;

    }

    if (is_woocommerce() || is_cart() || is_checkout() || is_account_page()) {

        return get_theme_file_path('woocommerce.php');

    }

    return $template;

}, 101);

/**

 * Keep the header cart badge in sync via WooCommerce's fragment refresh.

 *

 * @param  array  $fragments

 * @return array

 */

add_filter('woocommerce_add_to_cart_fragments', function ($fragments) {

    $cart = function_exists('WC') ? WC()?->cart : null;

    $fragments['.cart-count'] = '<span class="cart-count">'.($cart ? $cart->get_cart_contents_count() : 0).'</span>';

    return $fragments;

});

// Header badge uses WooCommerce's authoritative fragments, including cached pages.

add_action('wp_enqueue_scripts', function () {

    if (class_exists('WooCommerce')) {

        wp_enqueue_script('wc-cart-fragments');

    }

});

// Native WooCommerce loop buttons on the custom front page use its standard AJAX handler.

add_action('wp_enqueue_scripts', function () {

    if (is_front_page() && function_exists('wc_get_products')) {

        wp_enqueue_script('wc-add-to-cart');

    }

}, 30);

