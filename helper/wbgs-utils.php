<?php
//Helper functions
if (!function_exists('wbgs_render_template')) {
function wbgs_render_template($template_key, $data) {
    // Optional: Convert underscores to hyphens if you standardized filenames with hyphens
    $template_key = str_replace('_', '-', $template_key);  // 'template_1' → 'template-1'
    $template_file = plugin_dir_path(__DIR__) . 'templates/' . $template_key . '.php';
    
    if (!file_exists($template_file)) {
        error_log("[WBGS] Missing template file: $template_file"); // Optional debug
        return '<div class="wbgs-template-fallback">Template not found.</div>';
    }

    $template = file_get_contents($template_file);
    if (!$template) {
        return '<div class="wbgs-template-fallback">Template is empty.</div>';
    }

    if (!isset($data['id']) || !$product = wc_get_product($data['id'])) {
        return '<div class="wbgs-template-fallback">Invalid product ID.</div>';
    }

    $replacements = [
        '{{title}}'        => esc_html(get_the_title($data['id'])),
        '{{permalink}}'    => esc_url(get_permalink($data['id'])),
        '{{sale_price}}'   => esc_html($product->get_sale_price()),
        '{{stock}}'        => intval($data['stock_alert']),
        '{{image_url}}'    => esc_url($data['banner_image']),
        '{{custom_text}}'  => esc_html(get_option('wbgs_custom_option_text')),
        '{{end_time}}'     => intval($data['end_time']),
        '{{countdown_id}}' => esc_attr('wbgs_countdown_' . $data['id']),
    ];

    return strtr($template, $replacements);
 }
}

if (!function_exists('wbgs_save_product_data')) {
   function wbgs_save_product_data($product_id, $data) {
    if (!$product_id || get_post_type($product_id) !== 'product') {
        return new WP_Error('invalid_product', 'Invalid product.');
    }

    $existing = get_option("wbgs_product_{$product_id}_data");
    if (!empty($existing)) {
        return new WP_Error('duplicate', 'Settings for this product already exist.');
    }

    $data['id'] = $product_id;
    $data['status'] = 'disable';

    if (!empty($data['template']) && function_exists('wbgs_render_template')) {
        try {
            // Re-render HTML using the original template key
            $data['rendered_html'] = wbgs_render_template($data['template'], $data);

            // Save the actual file name, using hyphens (template-1.php)
            $data['template_file'] = str_replace('_', '-', $data['template']) . '.php';
        } catch (Throwable $e) {
            return new WP_Error('template_rendering_error', 'Template rendering failed: ' . $e->getMessage());
        }
    }

    update_option("wbgs_product_{$product_id}_data", $data);
    return true;
 }

}

?>