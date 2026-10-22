<?php

namespace App\Customizer;

/** Loaded by customize_register, after WordPress has loaded its control base. */
class ProductStripsControl extends \WP_Customize_Control
{
    public $type = 'sage-product-strips';

    public function enqueue()
    {
        $path = 'resources/js/customizer-strips.js';
        wp_enqueue_script('sage-customizer-strips', get_theme_file_uri($path), ['customize-controls'], (string) filemtime(get_theme_file_path($path)), true);
        $terms = taxonomy_exists('product_cat') ? get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false, 'orderby' => 'name']) : [];
        $categories = [];
        if (! is_wp_error($terms)) {
            $names = [];
            foreach ($terms as $term) $names[$term->term_id] = $term->name;
            foreach ($terms as $term) {
                $label = [];
                foreach (array_reverse(get_ancestors($term->term_id, 'product_cat', 'taxonomy')) as $id) $label[] = $names[$id] ?? '';
                $label[] = $term->name;
                $categories[] = ['id' => $term->term_id, 'name' => implode(' › ', array_filter($label))];
            }
        }
        wp_localize_script('sage-customizer-strips', 'sageStripControl', [
            'categories' => $categories,
            'labels' => [
                'block' => __('Product strip', 'sage'), 'heading' => __('Heading', 'sage'),
                'enabled' => __('Show this strip', 'sage'), 'source' => __('Products to show', 'sage'),
                'latest' => __('Newest products', 'sage'), 'category' => __('Selected category', 'sage'),
                'featured' => __('Featured products', 'sage'), 'sale' => __('On-sale products', 'sage'),
                'categoryLabel' => __('Category (optional filter for other sources)', 'sage'),
                'all' => __('All categories / select a category', 'sage'), 'missing' => __('Unavailable category', 'sage'),
                'count' => __('Product count (1–24)', 'sage'), 'columns' => __('Desktop columns (2–6)', 'sage'),
                'layout' => __('Layout', 'sage'), 'grid' => __('Grid', 'sage'), 'scroll' => __('Scrollable row', 'sage'),
                'up' => __('Move up', 'sage'), 'down' => __('Move down', 'sage'), 'remove' => __('Remove', 'sage'),
                'add' => __('Add product strip', 'sage'), 'limit' => __('Maximum 20 strips. Empty categories are hidden on the front page.', 'sage'),
            ],
        ]);
        wp_enqueue_style('sage-customizer-strips', get_theme_file_uri('resources/css/customizer-strips.css'), [], (string) filemtime(get_theme_file_path('resources/css/customizer-strips.css')));
    }

    protected function render_content()
    {
        echo '<span class="customize-control-title">'.esc_html($this->label).'</span>';
        echo '<p class="description">'.esc_html($this->description).'</p>';
        echo '<div class="sage-strips-editor"></div><noscript>'.esc_html__('Enable JavaScript to edit product strips.', 'sage').'</noscript>';
    }
}
