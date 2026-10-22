# Original theme — slider and logo update (11.2.7)

This is the original monochrome theme, not the later modular theme.

## WordPress setup

- **Appearance → Customize → Site Identity:** select your logo. It now appears to the left of the site title, inside one home link. The title is smaller (20px desktop, 17px mobile); the logo is fitted without cropping. Without a logo, the title still displays normally.
- **Appearance → Customize → Homepage Slider:** edit up to five slides. Each has a small heading, headline, description, link label, URL and optional Media Library image. Two editable text slides are supplied to start. Clear both a slide's title and image to hide it. A blank URL links to the WooCommerce shop if that page is published.
- The slider appears on the storefront homepage, after the names-only category strip and before the existing dark jumbotron. It does not replace the jumbotron.
- Autoplay is off by default. If enabled, slides advance every seven seconds, pause on hover/focus or when the browser tab is hidden, and stop after manual navigation. Reduced-motion users get manual controls. Swipe, previous/next buttons and horizontal scrolling without JavaScript are supported.
- Images retain their colours and use contain sizing. Use similarly sized landscape images for consistent results.

## Installation and validation

Replace the original Sage theme with this package, including its compiled `public/build` assets. Clear page and Blade caches after updating. Existing content and Customizer settings are retained.

Production build passed. Twelve theme PHP files and 25 Blade templates passed isolated compilation/syntax checks on PHP 8.4.25 (component-tag expansion disabled). Carousel behavior checks passed for slide visibility, inactive-slide focus exclusion, wraparound, hover/focus pause, manual pause/resume and reduced motion. The contact template's inline/block PHP syntax conflict was also corrected.

Live WordPress rendering and browser visual testing have not been verified for this update. Add your actual logo and slide images in WordPress and check desktop/mobile appearance after installation.
