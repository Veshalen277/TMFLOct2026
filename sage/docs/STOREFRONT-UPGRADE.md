# Configurable storefront upgrade — 22 October 2026

This upgrade extends the supplied Sage theme. The ZIP contains the complete theme, the original Composer vendor directory, and rebuilt production assets. No changes have been applied to the live website. This document supersedes earlier slider/front-page notes where they differ.

## Configure the result

Open **Appearance → Customize**.

| Location | Controls |
| --- | --- |
| Storefront Layouts → Front Page Product Strips | Master visibility; add/remove strips; Move Up/Down; per-strip visibility, heading, source, category, count, layout and desktop columns; section spacing |
| Storefront Layouts → WooCommerce Layouts | Related-products visibility, matching theme layout, heading, introduction, count, desktop columns, grid/scroll choice and shop link; shared card ratings, purchase buttons and hover effects |
| Homepage Slider | Visibility, autoplay, interval, arrows, desktop/mobile height, overlay strength; five editable slides with uploaded image, fallback image URL, text, destination, visibility and ordering position |
| Storefront Home Page | Existing introduction text and hero image; visibility of the original introduction, category tiles, story, reviews, help and final shop banner |

The slider is immediately below the site header and above category navigation. It spans the full site width. Slide copy is aligned with the inner content width. It is not placed above the site header. Turn off **Show the original introduction below the slider** for a simpler homepage with only one hero area.

The first two slides have `via.placeholder.com` image URLs by default, as requested. Uploading a Media Library image overrides the URL. This remote service may be unavailable; the slider retains its dark background and copy if an image fails. Replace these placeholders with real banner images before launch. Existing uploaded images and saved slider copy remain in use.

To create a category strip, choose **Add product strip**, select **Selected category**, choose the category, enter a heading and choose a count. The category selector includes parents and subcategories. Newest, Featured and On-sale sources also accept an optional category filter. Reordering is keyboard-accessible through Move Up/Down buttons. Publish in the Customizer to save; previews refresh using the actual PHP/Blade rendering.

Limits are 20 strips, 1–24 products per strip and 2–6 desktop columns. Mobile grids use two columns; scroll rows show a partial next card. Empty, disabled and invalid-category strips are omitted. Deleting every strip intentionally leaves the homepage without product strips. Default strips replace the former hard-coded specials/newest blocks, so those sections are not duplicated.

## Installation

1. Back up the active theme and test the ZIP on a staging copy. This archive updates the existing **sage** theme directory: keep that directory name to retain WordPress theme-mod settings.
2. Replace the active theme files with the supplied `sage/` directory, or use your normal theme deployment workflow. Do not upload it as a plugin. If using the WordPress theme upload screen, use the replace-existing-theme flow for `sage`.
3. PHP **8.3 or newer** remains required by the original theme. The original `vendor/` directory and new `public/build/` assets are included; no npm build is needed for the supplied archive.
4. Clear Acorn view caches (`wp acorn view:clear`) and site/CDN caches through your deployment tools. If your deployment uses Acorn optimisation/discovery caches, clear those with `wp acorn optimize:clear` as well.
5. Configure the sections above. Verify a category strip, a variable-product card, a single product with related products, and cart updates on staging. Verify membership/catalogue-visibility plugins if used.
6. For future source changes, use `npm ci` followed by `npm run build` in the theme directory. Keep the existing Composer workflow; no new production PHP or JavaScript packages were added.

## Exact code changes

All modified files in this archive are complete replacements, ready to copy. `docs/STOREFRONT-CHANGES.patch` contains the source-level unified diff, including new files. The archive is the simplest installation route; the patch is primarily for code review because the supplied source uses mixed line endings.

| File | Change |
| --- | --- |
| `app/customizer.php` | Adds the two layout sections, repeater setting/control, sanitization, card toggles, existing-section toggles and slider options. Keeps existing setting IDs. |
| `app/Storefront.php` | Shared defaults, strict strip sanitization, unique IDs, bounds and category-aware WooCommerce product retrieval. |
| `app/Customizer/ProductStripsControl.php` | Native Customizer control with translated labels and hierarchical category choices. Loads its own admin-only JS/CSS. |
| `app/View/Composers/FrontPage.php` | Supplies `productStrips` and a valid specials link target alongside the existing storefront data. |
| `app/View/Composers/PromoSlider.php` | Reads image URLs, per-slide visibility/order, slider dimensions, overlay, interval and arrows. |
| `app/woocommerce.php` | Related visibility/count/column/heading hooks and narrowly scoped related-template routing. |
| `app/woocommerce/related.php` | Small native WooCommerce-to-Blade bridge using WooCommerce’s supplied related product objects. |
| `resources/views/front-page.blade.php` | Loops through strips instead of rendering the fixed specials/newest blocks; honours section toggles. |
| `resources/views/layouts/app.blade.php` | Moves the homepage slider above category navigation without duplicating it. |
| `resources/views/sections/product-strip.blade.php` | Accessible section heading, collection link and optional scroll controls. |
| `resources/views/sections/promo-slider.blade.php` | Full-width banner with cover images, captions, controls and responsive sizing. |
| `resources/views/partials/product-collection.blade.php` | Shared responsive grid/scroll container for home and related products. |
| `resources/views/partials/collection-controls.blade.php` | Labelled previous/next buttons with unique controlled-region IDs. |
| `resources/views/partials/home/product.blade.php` | Reuses existing WooCommerce cards and adds configurable ratings, purchase buttons and hover state. |
| `resources/views/woocommerce/single-product/related.blade.php` | One cohesive related-products section with matching cards and configurable copy/layout. |
| `resources/js/customizer-strips.js` | Add/remove/reorder/toggle editor, category selection and setting persistence. |
| `resources/js/storefront-layouts.js` | Scroll controls, keyboard navigation, carousel playback, swipe and reduced-motion handling. |
| `resources/js/app.js` | Initialises the new frontend interactions. |
| `resources/css/customizer-strips.css` | Admin-only repeater styling. |
| `resources/css/storefront-layouts.css` | Responsive banner, collections, focus states, aligned cards and reduced-motion styles. |
| `resources/css/app.css` | Includes the new stylesheet and Tailwind theme/utilities layers, without adding Tailwind Preflight or resetting the existing theme. |
| `public/build/` | Rebuilt CSS, JavaScript, generated theme data and manifest. |

The existing `CategoryStrip.php` and `sections/category-strip.blade.php` remain dedicated to category navigation. Product strips use a separate partial so navigation and product queries cannot collide.

## Query and integration behaviour

- Product queries use `wc_get_products`, published parent products and catalogue visibility. Stock hiding follows the WooCommerce setting. Extension-level `is_visible()` checks remain active; a bounded three-page refill can fill gaps without unbounded catalogue scans. Highly restrictive visibility extensions may still result in fewer cards than requested.
- No AJAX endpoints, credentials, direct database queries, price edits, taxonomy edits or product imports are introduced.
- Related-product IDs, exclusions and ordering continue to come from WooCommerce’s native related-product function. The theme replaces only `single-product/related.php` when the matching layout toggle is enabled. Disabling it restores the native related template; count, columns and heading settings still apply, while the matching-card controls apply only to the custom layout.
- This is the classic WooCommerce template flow already used by the supplied theme. A separately inserted WooCommerce Related Products block is not modified.
- Native loop add-to-cart rendering is retained, including the existing global-product save/restore logic. Product URLs, variation selection and price markup remain WooCommerce-owned.
- Card options apply to the reusable home/related cards, not to the native shop archive cards or the hero’s decorative product tiles.
- Existing `sage_promo_*` values carry over. The old bundled promo initializer finds no matching old carousel markup; the new slider initializer handles the replacement once.

## Accessibility and responsive behaviour

Horizontal strips work with touch, native scrollbars, previous/next controls and keyboard navigation. Each section has a labelled region and unique heading IDs. Buttons keep keyboard focus at row boundaries. Cards remain visible without JavaScript.

The slider has explicit previous/next and play/pause controls, stops automatic motion on hover/focus or a hidden tab, and respects reduced-motion preferences. Manual navigation pauses autoplay. Inactive enhanced slides are hidden and inert. Without JavaScript, the slides remain a native horizontally scrollable row. The first image is eager/high-priority; later images are lazy loaded.

## Verification and remaining staging checks

Production Vite build, PHP parsing, Blade compilation/rendering, Customizer registration and strip sanitization/query fixtures were checked. DOM interaction tests also passed for slider navigation, pause/autoplay, product scrolling, failed-image fallback and Customizer editing/persistence. These DOM tests use simulated dimensions and are not visual browser tests. The PHP/Blade checks ran under PHP 8.5 in a WebAssembly CLI against the supplied Composer dependencies; the source uses PHP 8.3-compatible syntax.

An actual WordPress/WooCommerce database, theme activation, native Customizer publishing, live add-to-cart requests and production plugin interactions were not available in this workspace. Browser visual tests could not run because the available browser runtime failed to launch. Responsive appearance and those live integrations must be checked on staging. `docs/STOREFRONT-TEST-RESULTS.txt` records the automated checks that did run.

## References used

- WooCommerce product-query API: https://developer.woocommerce.com/docs/features/products/wc-get-products/
- WooCommerce related-product template: https://woocommerce.github.io/code-reference/files/woocommerce-templates-single-product-related.html
- WordPress Customizer control assets: https://developer.wordpress.org/reference/classes/wp_customize_control/enqueue/
- WordPress Customizer script hook: https://developer.wordpress.org/reference/hooks/customize_controls_enqueue_scripts/
