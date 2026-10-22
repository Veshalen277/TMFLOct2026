<?php

/**
 * WooCommerce template bridge.
 *
 * WooCommerce's own template loader looks for a physical `woocommerce.php`
 * file in the theme root (see app/woocommerce.php, which forces
 * `template_include` to this file on WooCommerce pages). We hand off to the
 * normal Acorn/Blade rendering pipeline from here, the same way the theme's
 * root index.php does.
 */

echo view('woocommerce')->render();
