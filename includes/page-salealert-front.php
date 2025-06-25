<?php
if (!class_exists('WBGS_SmartCountdownFront')) {
    class WBGS_SmartCountdownFront {
        public function __construct() {
          add_action('woocommerce_before_main_content', [$this,'wbgs_woocommerce_shop_sale_alert'], 5,0);
        }
        //show sale alert on shop page
       public function wbgs_woocommerce_shop_sale_alert() {
            $combine_data_front = [];

            $args = array(
                'post_type'      => 'product',
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'fields'         => 'ids',
            );

            $product_ids = get_posts($args);

            foreach ($product_ids as $product_id) {
                $product_data_front = get_option("wbgs_product_{$product_id}_data");
                if (is_array($product_data_front)) {
                    $combine_data_front[$product_id] = $product_data_front;
                }
            }

            ksort($combine_data_front);

            foreach ($combine_data_front as $key => $front) {
                $status        = !empty($front['status']) ? $front['status'] : '';
                $template_file = !empty($front['template_file']) ? $front['template_file'] : '';
                $template_key  = basename($template_file, '.php');

                if ($status === 'enable' && !empty($template_key) && function_exists('wbgs_render_template')) {
                    echo wbgs_render_template($template_key, $front);
                }
            }
        }

    }
    new WBGS_SmartCountdownFront();
}

?>