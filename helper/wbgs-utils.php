<?php
//Helper functions
if (!function_exists('wbgs_render_template')) {
   function wbgs_render_template($template_key, $data) {
    switch ($template_key) {
        case 'template_1':
            return function_exists('wbgs_template_1') ? wbgs_template_1($data) : wp_kses_post('<div>Template 1 not found</div>');
        case 'template_2':
            return function_exists('wbgs_template_2') ? wbgs_template_2($data) : wp_kses_post('<div>Template 2 not found</div>');;
        case 'template_3':
            return function_exists('wbgs_template_3') ? wbgs_template_3($data) : wp_kses_post('<div>Template 3 not found</div>');;
        case 'template_4':
            return function_exists('wbgs_template_4') ? wbgs_template_4($data) : wp_kses_post('<div>Template 4 not found</div>');;
        default:
            return '<div class="wbgs-template-fallback">Invalid template selected.</div>';
    }
}

}

// Template 1: Default layout
if (!function_exists('wbgs_template_1')) {
    function wbgs_template_1($data) {
        $product = get_post($data['id']);
        if (!$product) return '';
        
        $title       = get_the_title($product);
        $permalink   = get_permalink($product);
        $stock       = intval($data['stock_alert']);
        $image       = esc_url($data['banner_image']);
        $end_time    = $data['end_time'];
        $countdown_id = 'wbgs_countdown_' . $data['id'];

        ob_start();
        ?>
        <div class="wbgs_main_banner">
            <a href="<?php echo esc_url($permalink); ?>" target="_blank">
                <h3><?php echo esc_html__('Sale Alert', 'smart-countdown-scarcity'); ?></h3>
                <h4><?php echo esc_html($title); ?></h4>
                <p><?php echo esc_html__('Stock Left:', 'smart-countdown-scarcity') . ' ' . $stock; ?></p>
                <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>" />
                <div id="<?php echo esc_attr($countdown_id); ?>" class="wbgs-countdown-timer"></div>
            </a>
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    let countdownEl = document.getElementById("<?php echo esc_js($countdown_id); ?>");
                    let end = new Date(<?php echo esc_js($end_time * 1000); ?>).getTime();
                    let interval = setInterval(function () {
                        let now = new Date().getTime();
                        let distance = end - now;

                        if (distance < 0) {
                            clearInterval(interval);
                            countdownEl.innerHTML = "EXPIRED";
                            return;
                        }

                        let days = Math.floor(distance / (1000 * 60 * 60 * 24));
                        let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        let seconds = Math.floor((distance % (1000 * 60)) / 1000);

                        countdownEl.innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";
                    }, 1000);
                });
            </script>
        </div>
        <?php
        return ob_get_clean();
    }

}
if (!function_exists('wbgs_template_2')) {
    function wbgs_template_2($data) {
        wp_die('i am khaway and stand in template 2');
        return '<div class="wbgs_template_2">Template 2 content here</div>';
    }
}
if (!function_exists('wbgs_template_3')) {
    function wbgs_template_3($data) {
        wp_die('i am khaway and stand in template 3');
        return '<div class="wbgs_template_3">Template 3 content here</div>';
    }
}
if (!function_exists('wbgs_template_4')) {
    function wbgs_template_4($data) {
        wp_die('i am khaway and stand in template 4');
        return '<div class="wbgs_template_4">Template 4 content here</div>';
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
            $data['rendered_html'] = wbgs_render_template($data['template'], $data);
        } catch (Throwable $e) {
            return new WP_Error('template_rendering_error', 'Template rendering failed: ' . $e->getMessage());
        }
    }

    update_option("wbgs_product_{$product_id}_data", $data);
    return true;
}

}

?>