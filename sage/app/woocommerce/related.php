<?php
/** WooCommerce query stays native; only its related-products view is replaced. */
defined('ABSPATH') || exit;

$products = array_values(array_filter($related_products ?? [], static fn ($item) => $item instanceof \WC_Product && $item->is_visible()));
if ($products) {
    echo \Roots\view('woocommerce.single-product.related', ['products' => $products])->render();
}
