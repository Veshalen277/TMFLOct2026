<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class PromoSlider extends Composer
{
    protected static $views = ['sections.promo-slider'];

    public static function defaults(): array
    {
        return [
            1 => ['eyebrow' => __('The collection', 'sage'), 'title' => __('Find something you love.', 'sage'), 'text' => __('Explore thoughtful gifts and everyday favourites.', 'sage'), 'label' => __('Explore the shop', 'sage')],
            2 => ['eyebrow' => __('A little inspiration', 'sage'), 'title' => __('Small details. Lovely finds.', 'sage'), 'text' => __('Take a closer look at the collection.', 'sage'), 'label' => __('Browse the collection', 'sage')],
        ];
    }

    public function with(): array
    {
        $slides = [];
        $defaults = self::defaults();
        if (get_theme_mod('sage_promo_enabled', true)) {
            for ($i = 1; $i <= 5; $i++) {
                if (! get_theme_mod("sage_promo_{$i}_enabled", true)) continue;
                $slide = [];
                foreach (['eyebrow', 'title', 'text', 'label'] as $key) {
                    $slide[$key] = (string) get_theme_mod("sage_promo_{$i}_{$key}", $defaults[$i][$key] ?? '');
                }
                $slide['image'] = absint(get_theme_mod("sage_promo_{$i}_image", 0));
                if ($slide['image'] && ! wp_attachment_is_image($slide['image'])) $slide['image'] = 0;
                $slide['imageUrl'] = esc_url_raw(get_theme_mod("sage_promo_{$i}_image_url", $i <= 2 ? "https://via.placeholder.com/1920x720/333333/ffffff?text=Store+Highlight+{$i}" : ''));
                $slide['position'] = \App\Storefront::number(get_theme_mod("sage_promo_{$i}_position", $i * 10), 1, 100);
                if (! trim($slide['title']) && ! $slide['image'] && ! $slide['imageUrl']) continue;
                $slide['url'] = esc_url_raw(get_theme_mod("sage_promo_{$i}_url", ''));
                if (! $slide['url'] && function_exists('wc_get_page_permalink')) {
                    $shop = wc_get_page_id('shop');
                    if ($shop > 0 && get_post_status($shop) === 'publish') $slide['url'] = wc_get_page_permalink('shop');
                }
                $slides[] = $slide;
            }
        }
        usort($slides, static fn ($a, $b) => $a['position'] <=> $b['position']);
        return [
            'promoSlides' => $slides,
            'promoAutoplay' => (bool) get_theme_mod('sage_promo_autoplay', false),
            'promoArrows' => (bool) get_theme_mod('sage_promo_arrows', true),
            'promoInterval' => \App\Storefront::number(get_theme_mod('sage_promo_interval', 7), 4, 20),
            'promoHeight' => \App\Storefront::number(get_theme_mod('sage_promo_height', 520), 280, 800),
            'promoMobileHeight' => \App\Storefront::number(get_theme_mod('sage_promo_mobile_height', 440), 280, 640),
            'promoOverlay' => \App\Storefront::number(get_theme_mod('sage_promo_overlay', 55), 20, 85),
        ];
    }
}
