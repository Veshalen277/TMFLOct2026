# Sage Monochrome — finishing pass

## Apply the update

This ZIP contains the existing `sage/` theme folder, its PHP vendor dependencies, source files and freshly compiled production assets. Keep the folder name `sage` when replacing the existing theme so WordPress continues to use the same theme settings. No npm build or font upload is required to use this package.

Use your normal theme replacement workflow, then clear your page/CDN cache. This remains a Sage/Acorn WordPress theme with the original PHP and WordPress requirements; it is not a standalone HTML site.

## Design

- Black, white and neutral grays across the theme UI, including legacy theme rules, homepage, commerce controls and the editor color palette. Existing product photography is shown accurately in its original colors.
- Cormorant Garamond for prominent headings, paired with Inter for navigation, product names, prices, buttons, forms and checkout. Both are standard Google Fonts. One stylesheet loads the required weights with `display=swap`, early connections, and Georgia/system fallbacks. No font plugin, paid font or manual upload is needed.
- Fine dotted hero texture, framing rules, orbit lines, registration marks, catalog labels, layered product cards and a outlined circular stamp.
- Numbered category panels, inset product-image frames, consistent price hierarchy, real product ratings when available, and contrasting sale/availability badges.
- Framed brand story, editorial customer-review typography, numbered FAQ rows, ring details around the final call to action, and a large footer wordmark.
- Mobile spacing and layout adjustments, keyboard focus styles and reduced-motion support.

## Behavior and integration

The existing homepage composer continues to supply real products, prices, categories, offers and approved product reviews. Existing customizer text and hero-image options remain connected. Sample products/reviews appear only in the included preview screenshots, never in the live template.

Search now uses a proper button with expanded/control state. Mobile navigation closes on desktop resize and restores scroll behavior. Dropdown menus work with keyboard focus. The skip link becomes visible on focus. Anchor navigation transfers keyboard focus to its destination. Footer logo markup no longer nests links; logout is hidden for logged-out visitors; default policy links appear only for published pages. Out-of-stock badges take precedence over sale badges.

## Files to customize

- `resources/css/finish.css`: the detailed finish layer, responsive rules and display typography.
- `resources/css/app.css`, `home.css`, `woocommerce.css`, `motion.css`: original theme CSS, now grayscale.
- `resources/views/front-page.blade.php`: homepage detail markup.
- `resources/views/partials/home/product.blade.php`: live product cards.
- `resources/views/sections/header.blade.php`, `footer.blade.php`: navigation and footer.
- `resources/js/app.js`: navigation, focus and search behavior.
- `app/setup.php`: the single Google Fonts stylesheet.
- `theme.json`, `vite.config.js`: monochrome editor palette.

Legacy `--yellow`/`--gold` variable names are retained for compatibility; their values are neutral grayscale, not yellow or gold.

## Validation completed

- Production Vite build succeeds; every manifest reference resolves to a packaged asset.
- No chromatic hex or RGB colors remain in the theme CSS.
- Google Fonts returns the requested families and weights; Cormorant Garamond loaded successfully for visual QA.
- Actual homepage Blade markup rendered with sample data for browser layout verification.
- Tested widths: 320, 390, 768, 1024 and 1440 pixels; no horizontal overflow.
- Search opens with input focus and closes on Escape. Mobile menu opens/closes and FAQ details expand. No browser JavaScript errors in the preview.
- Desktop and mobile preview screenshots are included in `docs/`. Their catalog images are illustrative placeholders, and their reviews are explicitly sample content.

A PHP/WordPress server and live shop database were not supplied, so server-side Blade rendering, live cart/checkout, payment gateways, plugin-specific styling and customer orders could not be exercised here. Check those on your existing staging store before deploying to production.

## Recent products and shopping hints — 11.2.3

Recent products now explicitly stretch across the homepage panel. Their columns fit the available cards, with two columns on mobile and a full-width single-card fallback. Native WooCommerce product grids use `auto-fit` so a four-product row no longer reserves empty columns alongside it; the minimum track size also fits narrow screens. Existing product queries/counts are unchanged, and the shop list-view toggle still takes precedence.

Short, low-contrast-but-readable guidance appears at a few relevant points: recent product details, expanded search, shop sorting, options on purchasable variable products, and delivery for carts that require shipping. No pop-ups or repeated per-card notes were added. Digital-only carts, account pages, confirmations, and payment-only endpoints do not receive delivery hints.

Validated this update with a production build and browser assertions: four homepage cards fill the 1280px panel edge to edge at 305px each; four native WooCommerce cards fill their panel; mobile renders two rows of two; list view remains one column; search input is linked to its hint; no JavaScript errors or horizontal overflow from 320–1440px. Live WooCommerce server-side behavior still requires staging verification.

## Product-page detail correction — 11.2.4

The supplied screenshot exposed a float-clearing issue: the related-products heading flowed beside the tall main gallery, while only its product list cleared below it. Related products, upsells and cross-sells now clear the product columns as complete, full-width sections. Their larger Cormorant headings align directly above the cards with a restrained divider and a 24px desktop / 18px mobile gap.

WooCommerce finish selectors now also target the theme's actual `.wc-page-wrap` wrapper. Previously some rules assumed a nested `.woocommerce` container, although native product pages put that class on the body. This restores consistent product controls and title styling on those pages.

Other details: product-card prices and purchase buttons align; image frames use contain rather than applying additional cropping to book-cover thumbnails; tabs clear the gallery; recommendation spacing is reduced from the original 60px margin plus 60px padding; the content maximum increases to 1480px; repeated homepage, call-to-action and footer padding is reduced while keeping mobile breathing room. Original uploaded image colors are preserved.

Validation: production build succeeds. Native product-page fixtures deliberately reproduce a tall floated gallery with a short summary, a taller summary, and optional tabs; recommendations clear correctly at 320, 390, 768, 1024 and 1440px, with no horizontal overflow. Heading/card alignment and gap assertions pass. Related-product screenshots in `docs/` use clearly illustrative book-cover placeholders. Homepage, recent-products, search, mobile navigation and list-view checks also pass. Live WordPress/plugin integration still requires staging verification.

## Contact page and automatic CF7 styling — 11.2.5

Added the `Contact — Monochrome` page template. Place your existing CF7 block/shortcode in the page content; it renders inside the styled message panel. Optional introduction, public email, phone, address and hours are editable in Appearance → Customize → Contact Page. Blank details are omitted, privacy links use the configured policy URL, and no business details or response-time promises are invented.

`resources/css/contact.css` automatically styles Contact Form 7 throughout the theme using documented `.wpcf7` wrapper/status classes and HTML input types, without generated IDs or field-name selectors. It covers labels, inputs, textareas, selects, file inputs, checkboxes, radio buttons, submit/loading/disabled states, focus indicators, inline validation and response messages. CF7 continues to own submission, accessibility announcements, validation, anti-spam and mail configuration. No submit-handler JavaScript is added.

Setup instructions and an optional standard-mail-tag starter form are in `docs/CONTACT-PAGE.md` and `docs/cf7-starter-form.txt`. Existing forms need no extra field classes. The theme archive cannot create or publish the actual WordPress page without access to your site's database; select the new template and paste your existing form in WordPress.

Production build passes. The contact template was rendered to a representative markup fixture, but browser visual checks could not be completed because the local Chromium process failed to start. Live CF7 submission/mail testing also remains a staging check; the prior versions' browser verification results do not apply to this new contact page.

## Category-name marquee — 11.2.6

A slim strip appears immediately below the header navigation, on pages using the main theme layout. Its visible content is category names only: no Explore label, badges, counts or decorative separators. Every WooCommerce product category and subcategory is included, including empty categories, alphabetically, with normal category links. With WooCommerce unavailable or no categories, the strip is omitted.

Desktop movement is gentle (18 pixels/second), seamless and only enabled when names overflow. Hover pauses it; keyboard focus restores ordinary scrolling so the focused real link is visible. A pause/resume button is available on keyboard focus without permanent visual clutter. Repeated visual links are hidden from assistive technology and excluded from keyboard order; pointer activation retains native link navigation. Reduced-motion preferences, touch-primary devices, disabled animation, missing animation APIs and JavaScript-disabled browsers use a static, horizontally scrollable list. Background tabs pause their animation. Layout/font changes recalculate the loop.

Appearance → Customize → Category Strip provides show/hide and desktop-motion toggles. No category entry or code editing is needed when the catalog changes.

Production build and DOM behavior checks pass, covering animation speed, pause/resume, focus, duplicate-link handling, reduced-motion changes, touch/static behavior and short lists. These are DOM-level checks with simulated animation/media APIs; browser visual verification and live WordPress taxonomy queries remain staging checks.
