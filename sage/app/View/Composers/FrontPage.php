<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class FrontPage extends Composer

{

    protected static $views = ['front-page'];

    public function with(): array

    {

        $strips = \App\Storefront::strips();
        $storefront = $this->storefront();
        $storefront['specialsAnchor'] = '';
        foreach ($strips as $strip) {
            if ($strip['source'] === 'sale') { $storefront['specialsAnchor'] = 'home-strip-'.$strip['id']; break; }
        }
        return ['storefront' => $storefront, 'productStrips' => $strips];

    }

    public function storefront(): array

    {

        $enabled = function_exists('wc_get_products') && function_exists('wc_get_page_permalink');

        $shop = $enabled ? wc_get_page_permalink('shop') : home_url('/');

        $latest = $featured = $offers = $categories = $reviews = [];

        if ($enabled) {

            $visible = static fn ($products) => array_values(array_filter($products, static fn ($p) => $p->is_visible()));

            $latest = array_slice($visible(wc_get_products(['limit' => 12, 'status' => 'publish', 'orderby' => 'date', 'order' => 'DESC'])), 0, 8);

            $featured = $visible(wc_get_products(['limit' => 4, 'status' => 'publish', 'featured' => true]));

            if (function_exists('wc_get_product_ids_on_sale')) {

                $ids = wc_get_product_ids_on_sale();

                if ($ids) {

                    $offers = array_slice($visible(wc_get_products(['include' => $ids, 'limit' => 8, 'status' => 'publish', 'orderby' => 'date', 'order' => 'DESC'])), 0, 4);

                }

            }

            $terms = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => true, 'parent' => 0, 'number' => 6, 'orderby' => 'count', 'order' => 'DESC']);

            if (! is_wp_error($terms)) {

                foreach ($terms as $term) {

                    $url = get_term_link($term);

                    if (is_wp_error($url)) continue;

                    $thumbnail = (int) get_term_meta($term->term_id, 'thumbnail_id', true);

                    if (! $thumbnail) {

                        $sample = wc_get_products(['category' => [$term->slug], 'limit' => 1, 'status' => 'publish', 'visibility' => 'visible']);

                        $thumbnail = $sample ? $sample[0]->get_image_id() : 0;

                    }

                    $categories[] = ['name' => $term->name, 'url' => $url, 'count' => $term->count, 'image' => $thumbnail];

                }

            }

            $comments = get_comments(['type' => 'review', 'status' => 'approve', 'number' => 6, 'post_type' => 'product']);

            foreach ($comments as $comment) {

                $reviewProduct = function_exists('wc_get_product') ? wc_get_product($comment->comment_post_ID) : null;

                $rating = (int) get_comment_meta($comment->comment_ID, 'rating', true);

                if (! $reviewProduct || ! $reviewProduct->is_visible() || $rating < 1 || $rating > 5) continue;

                $reviews[] = ['name' => get_comment_author($comment), 'text' => wp_trim_words(wp_strip_all_tags($comment->comment_content), 42), 'rating' => $rating];

                if (count($reviews) === 3) break;

            }

        }

        $heroProducts = array_slice($featured ?: $latest, 0, 2);

        $pageLink = static function ($slug) {

            $page = get_page_by_path($slug);

            return $page && get_post_status($page) === 'publish' ? get_permalink($page) : '';

        };

        return [

            'enabled' => $enabled, 'shop' => $shop, 'latest' => $latest,

            'offers' => $offers, 'categories' => $categories, 'reviews' => $reviews,

            'heroProducts' => $heroProducts,

            'heroImage' => (int) get_theme_mod('sage_home_image', 0),

            'eyebrow' => get_theme_mod('sage_home_eyebrow', __('Something good, every day', 'sage')),

            'title' => get_theme_mod('sage_home_title', __('Everyday finds.', 'sage')),

            'accent' => get_theme_mod('sage_home_accent', __('Extraordinary value.', 'sage')),

            'description' => get_theme_mod('sage_home_description', __('Discover thoughtful gifts, useful essentials and new favourites. Find a little more to love, all in one place.', 'sage')),

            'about' => get_theme_mod('sage_home_about', __('From a thoughtful gift to an everyday essential, our collection brings the little things together. Explore what is new, rediscover a favourite, and find something that feels just right.', 'sage')),

            'shipping' => $pageLink('shipping') ?: $pageLink('shipping-policy'),

            'contact' => $pageLink('contact') ?: $pageLink('contact-us'),

            'returns' => $pageLink('returns-and-refund-policy') ?: $pageLink('returns'),

        ];

    }

}

