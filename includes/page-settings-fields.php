<?php
if (!class_exists('WBGS_SmartCountdownScarcitySetting')) {
    class WBGS_SmartCountdownScarcitySetting {
        public function __construct() {
            add_action('admin_menu', [ $this, 'wbgs_add_settings_menu' ]);
           add_action('wp_ajax_wbgs_save_product_settings', [$this, 'wbgs_save_product_settings']);
        }
// For setting menu
        public function wbgs_add_settings_menu() {
            add_menu_page(
                'Smart Countdown Settings',        // Page title
                'Countdown Settings',              // Menu title
                'manage_options',                  // Capability
                'wbgs_settings',                   // Menu slug
                [ $this, 'wbgs_render_settings_page' ], // Callback
                'dashicons-clock',                 // Icon
                56
            );
        }
    // For Register setting
       public function wbgs_render_settings_page() {
     
        ?>
      
        <div id="wbgs_modal" style="background:#fff; padding:20px; max-width:500px;">
        <div id="wbgs_message" class="notice is-dismissible" style="display: none; padding: 10px;"></div>
        <form action="" method="post">
            <div>
                <h2><?php esc_html_e('Add Product Settings', 'smart-countdown-scarcity'); ?></h2>

                <label><?php esc_html_e('Select Product', 'smart-countdown-scarcity'); ?></label><br>
                <select id="wbgs_modal_product_id">
                    <option value=""><?php esc_html_e('-- Select --', 'smart-countdown-scarcity'); ?></option>
                    <?php
                    $products = get_posts(['post_type' => 'product', 'numberposts' => -1]);
                    foreach ($products as $p) {
                        echo '<option value="' . esc_attr($p->ID) . '">' . esc_html($p->post_title) . '</option>';
                    }
                    ?>
                </select><br><br>

                <label><?php esc_html_e('Stock Alert', 'smart-countdown-scarcity'); ?></label><br>
                <input type="number" id="wbgs_modal_stock_alert"><br><br>

                <label><?php esc_html_e('End Date/Time', 'smart-countdown-scarcity'); ?></label><br>
                <input type="datetime-local" id="wbgs_modal_end_time"><br><br>

                <label><?php esc_html_e('Banner Image', 'smart-countdown-scarcity'); ?></label><br>
                <input type="hidden">
                <button class="button" id="wbgs_upload_banner"><?php esc_html_e('Select Image', 'smart-countdown-scarcity'); ?></button>
                <div id="wbgs_banner_preview" style="margin-top:10px;"></div><br>
            </div>
             <button class="button button-primary" id="wbgs_save_modal"><?php esc_html_e('Save Settings', 'smart-countdown-scarcity'); ?></button>
            </form>
        </div> 
        <?php
        }
        //Save product settings
     public function wbgs_save_product_settings() {
      
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        if (!isset($_POST['wbgs_ajax_nonce']) || !wp_verify_nonce($_POST['wbgs_ajax_nonce'], 'wbgs_nonce')) {
            wp_send_json_error(['message' => 'Invalid nonce']);
        }

        $product_id = absint($_POST['product_id']);

        if (!$product_id || get_post_type($product_id) !== 'product') {
            wp_send_json_error(['message' => 'Invalid product']);
        }
    // Check if data for this product already exists
        $existing_data = get_option("wbgs_product_{$product_id}_data");
        if (!empty($existing_data)) {
            wp_send_json_error(['message' => 'Settings for this product already exist.']);
        }

        $data = [
            'id' => $product_id,
            'stock_alert'  => sanitize_text_field($_POST['stock_alert']),
            'end_time'     => sanitize_text_field(strtotime($_POST['end_time'])),
            'banner_image' => esc_url_raw($_POST['banner_image']),
            'wbgs_ajax_nonce' => sanitize_text_field($_POST['wbgs_ajax_nonce']),
            'status' => 'disable'
        ];

        update_option("wbgs_product_{$product_id}_data", $data);
        wp_send_json_success(['message' => 'Product settings saved successfully.']);
    }

 }

    new WBGS_SmartCountdownScarcitySetting();
}


