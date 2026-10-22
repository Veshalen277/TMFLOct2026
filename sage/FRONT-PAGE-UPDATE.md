> Historical implementation notes. See FINISHING-NOTES.md for the current monochrome finish.

# Native Sage storefront home page

The home page follows the reference's shopping flow with an original black, warm-white and yellow design: split hero, categories, current specials, new arrivals, store story, genuine product reviews when present, FAQ, and final shop link. It uses Sage Blade and WooCommerce data. No Elementor installation or page-builder content is required.

## Install the update

The small `sage-homepage-update.zip` is an overlay, not a standalone theme. Extract it into `wp-content/themes/` so its `sage/` directory merges with the existing `sage/` directory. Replace matching files. It contains source, required configuration and compiled assets; it does not contain Composer vendor files.

Alternatively, the complete `sage.zip` is the full updated theme based on the files supplied in this conversation. Use either the overlay or the complete package, not both. Back up any newer local edits before replacing matching files.

Then run `wp acorn view:clear` from the WordPress root and clear SpeedyCache/CDN caches. If your installation caches discovered view composers, run `wp acorn optimize:clear` as well. Do not remove the existing vendor directory when applying the overlay.

Under Settings > Reading, choose the desired static front page. Sage's `front-page.blade.php` now supplies its layout. Configure copy and the optional hero image under Appearance > Customize > Storefront Home Page.

Compiled assets are included. No npm build is needed for installation. For later source edits use `npm ci` then `npm run build` from the theme directory; upload `public/build/` and source changes together.

## How the content works

- Hero: featured visible products, with newest visible products as the fallback. An optional Customizer image replaces the product display.
- Category section: up to six populated top-level categories, ordered by product count. Uses category thumbnails or a product image as fallback.
- Current specials: up to four visible sale products. Hidden if none are available.
- New arrivals: up to eight visible newest products.
- Product cards: WooCommerce's real images, prices and sale/stock status. The native loop-button function handles simple, variable, external and unavailable products with their appropriate actions. The front page enqueues WooCommerce's normal add-to-cart script.
- Reviews: up to three approved product reviews with valid star ratings, for visible products. No invented reviews, counts or ratings.
- Delivery/contact/returns links appear only when matching published pages exist. No new delivery-price promises or payment-provider claims are introduced by the home page.
- With WooCommerce unavailable, the page still renders the introduction, store story and FAQ without invoking missing WooCommerce functions.

The previous header, footer, account-grid and shop-grid corrections are retained. The account and shop fixes remain at the end of app.css, as supplied by you.

## Motion

GSAP animates the home introduction over 800 ms with 90 ms staggering. Viewport reveals use the shared 650 ms duration and capped 65 ms staggering. FAQ answers have a 280 ms entrance; product and category image hovers use CSS transitions. Content remains visible without JavaScript. Reduced-motion preferences disable the animated presentation. There is no automatic carousel or scroll interception beyond the existing same-page anchor behavior.

## Validation and limits

- Vite 8.2.2 / Tailwind 4.3.3 production build passed.
- PHP 8.4.25 with the bundled Illuminate Blade engine rendered the actual layout, header, footer, home composer and product partial using WordPress/WooCommerce test doubles.
- Tested populated and empty catalogs, WooCommerce absent, an optional hero image, and product reviews. Verified product count, one H1 and restoration of the native product global.
- DOM tests exercised normal and reduced-motion initialization, reveal completion and native FAQ opening using the built production JS.
- The reference was visually inspected in the browser. The browser security policy blocked the local-file preview, so the finished layout has not had desktop/mobile visual browser QA. CSS includes responsive layouts at 1100, 768 and 420 px; final visual checks must take place on staging.
- Live WooCommerce cart AJAX, product variations, third-party plugins and checkout/payment flows have not been tested on your server. The test doubles do not replace those integration checks.
- Composer dependencies are unchanged. The bundled vendor directory still requires PHP 8.4.1 or newer (its platform check is stricter than the original composer.json). Confirm your server meets that requirement before using the complete theme.

## Changed entry points

app/View/Composers/FrontPage.php
app/customizer.php
app/woocommerce.php
resources/views/front-page.blade.php
resources/views/partials/home/product.blade.php
resources/css/home.css
resources/css/app.css
resources/js/motion.js
public/build/

Reference: https://332.co.za/ (layout inspiration only; its branding, product images and customer reviews were not copied into this theme).
