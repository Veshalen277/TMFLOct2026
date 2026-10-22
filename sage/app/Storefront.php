<?php

namespace App;

/** Shared, bounded settings and product queries for the Customizer and views. */
final class Storefront
{
    public static function number($value, int $min, int $max): int
    {
        return max($min, min($max, (int) $value));
    }

    public static function defaults(): string
    {
        return wp_json_encode([
            ['id' => 'specials', 'enabled' => true, 'heading' => __('Good finds. Better prices.', 'sage'), 'source' => 'sale', 'category' => 0, 'count' => 4, 'layout' => 'grid', 'columns' => 4],
            ['id' => 'new-arrivals', 'enabled' => true, 'heading' => __('Meet your next favourite.', 'sage'), 'source' => 'latest', 'category' => 0, 'count' => 8, 'layout' => 'scroll', 'columns' => 4],
        ]);
    }

    public static function sanitizeStrips($value): string
    {
        $rows = is_string($value) ? json_decode($value, true) : $value;
        if (! is_array($rows)) {
            return '[]';
        }
        $clean = $used = [];
        foreach (array_slice($rows, 0, 20) as $index => $row) {
            if (! is_array($row)) continue;
            $id = sanitize_key(is_scalar($row['id'] ?? null) ? (string) $row['id'] : '');
            if (! $id || isset($used[$id])) $id = 'strip-'.($index + 1);
            while (isset($used[$id])) $id .= '-copy';
            $used[$id] = true;
            $source = $row['source'] ?? 'latest';
            $clean[] = [
                'id' => $id,
                'enabled' => rest_sanitize_boolean($row['enabled'] ?? true),
                'heading' => sanitize_text_field(is_scalar($row['heading'] ?? null) ? (string) $row['heading'] : ''),
                'source' => in_array($source, ['latest', 'category', 'featured', 'sale'], true) ? $source : 'latest',
                'category' => absint(is_scalar($row['category'] ?? null) ? $row['category'] : 0),
                'count' => self::number(is_scalar($row['count'] ?? null) ? $row['count'] : 8, 1, 24),
                'layout' => ($row['layout'] ?? '') === 'grid' ? 'grid' : 'scroll',
                'columns' => self::number(is_scalar($row['columns'] ?? null) ? $row['columns'] : 4, 2, 6),
            ];
        }
        return wp_json_encode($clean);
    }

    public static function strips(): array
    {
        if (! function_exists('wc_get_products') || ! get_theme_mod('sage_product_strips_enabled', true)) return [];
        $settings = json_decode(self::sanitizeStrips(get_theme_mod('sage_product_strips', self::defaults())), true);
        $blocks = [];
        foreach ($settings as $block) {
            if (! $block['enabled']) continue;
            $term = $block['category'] ? get_term($block['category'], 'product_cat') : null;
            // Never silently show the whole shop for a deleted/invalid category.
            if (($block['category'] && (! $term || is_wp_error($term))) || ($block['source'] === 'category' && ! $term)) continue;
            $args = ['limit' => $block['count'], 'status' => 'publish', 'parent' => 0, 'visibility' => 'catalog', 'orderby' => 'date', 'order' => 'DESC'];
            if ($term) $args['category'] = [$term->slug];
            if ($block['source'] === 'featured') $args['featured'] = true;
            if ($block['source'] === 'sale') {
                $args['include'] = wc_get_product_ids_on_sale();
                if (! $args['include']) continue;
            }
            if (get_option('woocommerce_hide_out_of_stock_items') === 'yes') $args['stock_status'] = ['instock', 'onbackorder'];
            $products = [];
            // A bounded refill also honours extension-level visibility restrictions.
            for ($page = 1; $page <= 3 && count($products) < $block['count']; $page++) {
                $batch = wc_get_products($args + ['page' => $page]);
                foreach ($batch as $product) {
                    if ($product->is_visible()) $products[$product->get_id()] = $product;
                    if (count($products) === $block['count']) break;
                }
                if (count($batch) < $block['count']) break;
            }
            if (! $products) continue;
            $url = $term ? get_term_link($term) : wc_get_page_permalink('shop');
            $block['url'] = is_wp_error($url) ? '' : $url;
            $block['heading'] = $block['heading'] ?: ($term ? $term->name : __('Explore the collection', 'sage'));
            $block['products'] = array_values($products);
            $blocks[] = $block;
        }
        return $blocks;
    }
}
