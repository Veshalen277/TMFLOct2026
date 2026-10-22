> Historical implementation notes. See FINISHING-NOTES.md for the current monochrome finish.

# React and GSAP storefront upgrade

This package retains the supplied Sage/IG Hoosen theme, its Composer vendor files, and its original WooCommerce content. Compiled production assets are included; no npm build is needed to try this package.

## Implemented

- React interface on the shop and product archives, single products, cart, checkout and checkout endpoints, order confirmation, and account endpoints.
- Shared commerce navigation, product search, grid/list controls, product section shortcuts, account section selector, cart/checkout/order progress, live add-to-cart feedback, and back-to-top control.
- GSAP header entrance, hero text transitions, menu/search content entrances, viewport reveals, product-image hover motion, catalog view transitions, and WooCommerce notice/update feedback.
- Motion timings in `resources/js/motion.js`: controls 180–280 ms, panels 450 ms, reveals 650 ms, stagger 65 ms capped at five offsets. Intro hero entrance 850 ms.
- Runtime reduced-motion support; CSS transitions and slider autoplay respect reduced motion. Content remains visible without animation initialization.
- WooCommerce's native forms, hooks, nonces, product gallery, variation handling, payment integrations, and Blocks continue to own their existing DOM. This is a shared React interface with page-specific controls, not a headless replacement of every WooCommerce template. Existing Cart/Checkout Blocks retain their own React runtime.

## Repairs

- Cart, checkout, account and their endpoints now render the actual WordPress page content through `the_content()`; product pages and archives continue using `woocommerce_content()`.
- Removed invalid native listeners for jQuery WooCommerce events, the undefined JavaScript `WC` reference, redundant cart fetches and guessed cart-count increments.
- Header counts use WooCommerce's server fragments. Cart-fragments is enqueued and Blocks cart changes request refreshed fragments.
- Fragment count callback tolerates an unavailable cart object.
- Empty/malformed hash links no longer cause selector errors. Native WooCommerce tabs keep their own handling.
- Removed nested header logo links.
- Closed menu/search panels are inert. Menu focus loops, Escape restores focus, and scroll locking restores the preceding overflow value.
- Slider handles empty markup, pauses on focus/hidden documents, and scopes arrow-key handling to the slider.
- Replaced the fixed `/app/themes/sage/` Vite asset base with relative URLs, supporting conventional WordPress theme paths.
- Configured the Roots JSX factory to use the bundled React instance rather than an undeclared `wp.element` global.

## Installation and rebuilding

1. Back up the current theme and test the replacement `sage/` directory on a staging installation.
2. Replace the theme files with the contents of this archive. Keep the directory named `sage` to preserve the theme identity.
3. Clear page/CDN caches and the Acorn view cache (`wp acorn view:clear` where available).
4. The included Composer dependencies are preserved from the upload. The target server must still satisfy the supplied `composer.json` requirements.
5. For future frontend changes, use the Node version specified by `package.json`, then run `npm ci` and `npm run build` from the theme directory. Upload source changes and `public/build/` together.

Source: `resources/js/commerce/index.jsx`, `resources/js/motion.js`, `resources/js/app.js`, `resources/css/motion.css`, `resources/views/woocommerce.blade.php`, `app/woocommerce.php`, and `vite.config.js`.

## Validation completed

- Production Vite build passed, including lazy-loaded React storefront chunk and generated manifest.
- JavaScript syntax checks passed.
- PHP parser syntax checks passed for `app/woocommerce.php`, `app/setup.php`, `functions.php`, and the root `woocommerce.php` bridge. This is static parsing, not a PHP runtime test.
- jsdom tests exercised all six page contexts using the compiled production bundle: React mount/navigation, catalog grid/list changes, account selector, current checkout step, native form preservation, empty anchors, Blocks add-to-cart toast and dismissal. Reduced-motion and normal-motion initialization were exercised.
- ZIP integrity and manifest asset references checked before delivery.

## Staging checks still required

No WordPress database/server or PHP executable was provided. A Chromium download failed, so visual browser QA was not completed. jsdom checks cannot verify visual layout, real animation smoothness, PHP/Blade execution, or WooCommerce server behavior.

Check shop/category/search pagination and sorting; simple/variable/grouped/external products; quantity, stock and gallery behavior; classic and Blocks carts; coupons/shipping/removals; cached header counts; classic and Blocks checkout; gateway success/error/3DS/redirect flows; order-pay and order-received pages; login/logout, orders, addresses, downloads and account forms. Check keyboard and mobile layouts, reduced motion, AJAX-inserted products/notices, and operation with JavaScript disabled.

The grid/list switch targets classic WooCommerce `ul.products`; plugin-specific product grids keep their own layout. Account navigation is derived from the rendered WooCommerce menu, including plugin-added endpoints. Store-specific plugins, blocks, translations and custom checkout behavior need staging verification.

## Reference documentation

- React integration: https://react.dev/learn/add-react-to-an-existing-project
- React roots: https://react.dev/reference/react-dom/client/createRoot
- GSAP media preferences and cleanup: https://gsap.com/docs/v3/GSAP/gsap.matchMedia()/
- GSAP contexts: https://gsap.com/docs/v3/GSAP/gsap.context()/
