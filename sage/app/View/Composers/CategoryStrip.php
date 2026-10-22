<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class CategoryStrip extends Composer
{
    protected static $views = ['sections.category-strip'];

    public function with(): array
    {
        $categories = [];
        if (get_theme_mod('sage_category_strip_enabled', true) && taxonomy_exists('product_cat')) {
            // Include every category and subcategory, not just the six homepage tiles.
            $terms = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false, 'orderby' => 'name', 'order' => 'ASC']);
            if (! is_wp_error($terms)) {
                foreach ($terms as $term) {
                    $url = get_term_link($term);
                    if (is_wp_error($url)) {
                        continue;
                    }
                    $categories[] = ['name' => $term->name, 'url' => $url, 'current' => is_tax('product_cat', $term->term_id)];
                }
            }
        }

        return [
            'stripCategories' => $categories,
            'stripMotion' => (bool) get_theme_mod('sage_category_strip_motion', true),
        ];
    }
}
