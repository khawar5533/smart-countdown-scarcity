<?php
//Helper functions
// Render the banner templates
if (!function_exists('wbgs_render_template')) {
    function wbgs_render_template($template_key, $data) {
        $template_key  = str_replace('_', '-', $template_key);
        $template_file = plugin_dir_path(__DIR__) . 'templates/' . $template_key . '.php';

        if (!file_exists($template_file)) {
            return '<div class="wbgs-template-fallback">Template not found.</div>';
        }

        $template = file_get_contents($template_file);
        if (!$template) {
            return '<div class="wbgs-template-fallback">Template is empty.</div>';
        }

        $replacements = [
            '{{title}}'        => esc_html($data['title'] ?? ''),
            '{{subtitle}}'        => esc_html($data['subtitle'] ?? ''),
            '{{description}}'        => esc_html($data['description'] ?? ''),
            '{{flashtitle}}'        => esc_html($data['flashsaletitle'] ?? ''),
            '{{discounttitle}}'        => esc_html($data['discounttitle'] ?? ''),
            '{{off}}'        => esc_html($data['discountoff'] ?? ''),
            '{{sale_price}}'   => esc_html($data['sale_price'] ?? ''),
            '{{stock}}'        => intval($data['stock_alert'] ?? 0),
            '{{image_url}}'    => esc_url($data['banner_image'] ?? ''),
            '{{custom_text}}'  => esc_html($data['custom_text'] ?? ''),
            '{{end_time}}'     => intval($data['end_time'] ?? 0),
            '{{countdown_id}}' => esc_attr($data['countdown_id'] ?? ''),
            '{{shop now}}' => esc_attr('SHOP NOW'),
             '{{url}}' => get_permalink($data['id']),
            '{{percent}}' => esc_attr('%'),
        ];

        return strtr($template, $replacements);
    }


}
// save the banner template
if (!function_exists('wbgs_save_and_register_product_data')) {
   function wbgs_save_and_register_product_data($product_id, $data) {
        if (!$product_id || get_post_type($product_id) !== 'product') {
            return new WP_Error('invalid_product', 'Invalid product.');
        }

        $product = wc_get_product($product_id);
        if (!$product) {
            return new WP_Error('invalid_product', 'WooCommerce product not found.');
        }

        $is_editing = !empty($data['edit_id']);
        $option_key = "wbgs_product_{$product_id}_data";
        $existing   = get_option($option_key);

        // Prevent duplicate insert
        if (!$is_editing && !empty($existing)) {
            return new WP_Error('duplicate', 'Settings for this product already exist.');
        }

        // In edit mode, preserve the current status
        if ($is_editing && !empty($existing['status'])) {
            $data['status'] = $existing['status'];
        } else {
            $data['status'] = 'disable'; // default for new
        }

        $data['id']        = $product_id;
        $data['shortcode'] = 'wbgs_product_' . $product_id;

        if (!empty($data['template'])) {
            $data['template'] = sanitize_text_field($data['template']);
            $data['template_file'] = str_replace('_', '-', $data['template']) . '.php';
        }

        $data['title']         = isset($data['title']) ? esc_attr($data['title']) : '';
        $data['subtitle']         = isset($data['subtitle']) ? esc_attr($data['subtitle']) : '';
        $data['description']         = isset($data['description']) ? esc_attr($data['description']) : '';
        $data['flashsaletitle']         = isset($data['flashsaletitle']) ? esc_attr($data['flashsaletitle']) : '';
        $data['discounttitle']         = isset($data['discounttitle']) ? esc_attr($data['discounttitle']) : '';
        $data['discountoff']         = isset($data['discountoff']) ? esc_attr($data['discountoff']) : '';
        $data['sale_price']    = $product->get_sale_price();
        $data['custom_text']   = get_option('wbgs_custom_option_text');
        $data['countdown_id']  = 'wbgs_countdown_' . $product_id;
        $data['stock_alert']   = isset($data['stock_alert']) ? intval($data['stock_alert']) : 0;
        $data['end_time']      = isset($data['end_time']) ? intval($data['end_time']) : 0;
        $data['banner_image']  = isset($data['banner_image']) ? esc_url_raw($data['banner_image']) : '';
        update_option($option_key, $data);
        return true;
}


}
// Hook into WordPress 'init' to register dynamic shortcodes for all products
add_action('init', 'wbgs_register_all_product_shortcodes', 10);

if (!function_exists('wbgs_register_all_product_shortcodes')) {
    function wbgs_register_all_product_shortcodes() {

        $args = [
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ];

        $product_ids = get_posts($args);

        foreach ($product_ids as $product_id) {
            $data = get_option("wbgs_product_{$product_id}_data");

            if (!empty($data) && !empty($data['template'])) {
                $shortcode_tag = 'wbgs_product_' . $product_id;

                add_shortcode($shortcode_tag, function () use ($product_id) {
                    if (!function_exists('wbgs_render_template')) {
                        return '<div class="wbgs-template-fallback">Template renderer not found.</div>';
                    }

                    // Always get the latest data
                    $live_data = get_option("wbgs_product_{$product_id}_data");

                    // Render the template regardless of status
                    return wbgs_render_template($live_data['template'], $live_data);
                });
            }
        }
    }
}
// Hook into WooCommerce content area early to manually render the post content (with shortcodes)

add_action('woocommerce_before_main_content', 'wbgs_output_editor_shortcodes_on_shop_pages', 5);
if (!function_exists('wbgs_output_editor_shortcodes_on_shop_pages')) {

    function wbgs_output_editor_shortcodes_on_shop_pages() {
        // Only run on shop, category, or tag archive pages
        if (is_shop() || is_product_category() || is_product_tag()) {
            // Get the proper page ID (shop or current archive term)
            $page_id = is_shop() ? wc_get_page_id('shop') : get_queried_object_id();

            if ($page_id && $post = get_post($page_id)) {
                // Apply content filters to make sure shortcodes are rendered
                $content = apply_filters('the_content', $post->post_content);

                if (!empty($content)) {
                    echo '<div class="wbgs-content-area">' . esc_attr($content) . '</div>';
                }
            }
        }
    }
}


?>