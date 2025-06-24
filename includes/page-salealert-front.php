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

          // Sort by product ID
          ksort($combine_data_front);

          foreach ($combine_data_front as $key => $front) {
              $data_id        = !empty($front['id']) ? $front['id'] : '';
              $status         = !empty($front['status']) ? $front['status'] : '';
              $rendered_html  = !empty($front['rendered_html']) ? $front['rendered_html'] : '';
             

              if (!empty($status) && $status === 'enable' && !empty($rendered_html)) {
                  // Safe display of trusted HTML (generated internally)
                   echo $rendered_html;

              }
          }
      }

    }
    new WBGS_SmartCountdownFront();
}

?>