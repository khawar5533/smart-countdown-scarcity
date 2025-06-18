<?php
if (!class_exists('WBGS_SmartCountdownScarcitySetting')) {
    class WBGS_SmartCountdownScarcitySetting {
        public function __construct() {
            add_action('admin_menu', [ $this, 'wbgs_add_settings_menu' ]);
            add_action('admin_menu', [ $this, 'wbgs_add_submenu_page' ]);
            add_action('wp_ajax_wbgs_edit_product_status', [$this, 'wbgs_edit_product_status']); 
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
        public function wbgs_add_submenu_page() {
            add_submenu_page(
                'wbgs_settings',                          // Parent slug
                'Custom Settings',                     // Page title
                'Custom Option',                          // Submenu title
                'manage_options',                         // Capability
                'wbgs_custom_option',                     // Menu slug
                [ $this, 'wbgs_render_custom_option_page' ] // Callback
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
                <input type="datetime-local" id="wbgs_modal_end_time" step="1"><br><br>

                <label><?php esc_html_e('Banner Image', 'smart-countdown-scarcity'); ?></label><br>
                <input type="hidden" id="wbgs_modal_banner" name="wbgs_modal_banner">
                <button class="button" id="wbgs_upload_banner"><?php esc_html_e('Select Image', 'smart-countdown-scarcity'); ?></button>
                <div id="wbgs_banner_preview" style="margin-top:10px;"></div><br>
            </div>
             <button class="button button-primary" id="wbgs_save_modal"><?php esc_html_e('Save Settings', 'smart-countdown-scarcity'); ?></button>
            </form>
        </div> 
        <?php
        //fetch products
        $combined_data = [];
        $products = get_posts([
            'post_type' => 'product',
            'numberposts' => -1
        ]);
        foreach ($products as $product) {
            $product_id = $product->ID;
            $product_data = get_option("wbgs_product_{$product_id}_data");
            if (is_array($product_data)) {
        // Index the data using the product ID
             $combined_data[$product_id] = $product_data;
           }
        }
        // Sort by product ID
        ksort($combined_data);
         ?>
         <div class="wbgs_products_detail">
            <h3><?php esc_html_e('Product Sale Alert', 'smart-countdown-scarcity'); ?></h3>
         <table id="wbgs_products_table"  cellpadding="8" cellspacing="0" border="1">
            <thead>
               <tr>
                <th><?php echo esc_html( 'Action' ); ?></th>
                <th><?php echo esc_html( 'Name' ); ?></th>
                <th><?php echo esc_html( 'Stock Alert' ); ?></th>
                <th><?php echo esc_html( 'Duration' ); ?></th>
                <th><?php echo esc_html( 'Banner' ); ?></th>
                <th><?php echo esc_html( 'Status' ); ?></th>
            </tr>
            </thead>
            <tbody>
                <?php
                if(!empty($combined_data)){
                 foreach($combined_data as $key => $alert_data) { 
                    $product_id = isset($alert_data['id']) ? $alert_data['id'] : '';
                    $stock_alert = isset($alert_data['stock_alert']) ? $alert_data['stock_alert'] : '';
                    $end_time = isset($alert_data['end_time']) ? $alert_data['end_time'] : '';

                    if ( ! empty( $end_time ) ) {
                        $date_format = get_option( 'date_format' );
                        $formatted_date = date_i18n( $date_format . ' H:i:s', $end_time );
                    }

                    $banner_image = isset($alert_data['banner_image']) && !empty($alert_data['banner_image']) ? $alert_data['banner_image'] : plugin_dir_url(dirname(__FILE__)) . 'assets/images/default-banner.jpg';
                    $status = isset($alert_data['status']) ? $alert_data['status'] : '';
                    ?>
                <tr>
                    <td>
                    <input 
                        type="radio" 
                        id="wbgs-option-<?php echo esc_attr( $product_id ); ?>" 
                        name="choice" 
                        value="<?php echo esc_attr( $status ); ?>" 
                        data-product-id="<?php echo esc_attr( $product_id ); ?>" <?php echo ( isset($alert_data['status']) && $alert_data['status'] === 'enable' ) ? 'checked' : ''; ?>>
                        
                    </td>
                    <td><?php echo esc_attr(get_the_title( $product_id  ));?></td>
                    <td><?php echo esc_attr($stock_alert); ?></td>
                    <td><?php echo esc_attr($formatted_date);?></td>
                    <td><img src="<?php echo esc_url($banner_image) ;?>" alt="Banner Image" width="50" height="25"></td>
                    <td><?php echo esc_attr($status);?></td>
                </tr> 
                <?php }}else{ ?>
                    <tr><td colspan="6" style="text-align: center;"><?php esc_html_e('Not Recored Found', 'smart-countdown-scarcity'); ?></td></tr>
                <?php } ?>
            </tbody>
            </table>
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
    //update the product statius
    public function wbgs_edit_product_status() {
        $selected_id = intval($_POST['selected_product_id']);
        $all_ids = isset($_POST['all_product_ids']) ? $_POST['all_product_ids'] : [];

        if (!is_array($all_ids)) {
            wp_send_json_error('Invalid product IDs');
            return;
        }

        foreach ($all_ids as $id) {
            $product_id = intval($id);
            if ($product_id <= 0) continue;

            $status = ($product_id === $selected_id) ? 'enable' : 'disable';
            $option_key = 'wbgs_product_' . $product_id . '_data';

            $option_data = get_option($option_key);

            if (is_array($option_data)) {
                $option_data['status'] = $status;
                update_option($option_key, $option_data);
            }
        }

        wp_send_json_success('All statuses updated successfully.');
    }

    public function wbgs_render_custom_option_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Custom Option Setting', 'smart-countdown-scarcity'); ?></h1>
        <?php if (isset($_POST['wbgs_custom_option_text'])) : 
            check_admin_referer('wbgs_custom_option_save');
            $custom_text = sanitize_text_field($_POST['wbgs_custom_option_text']);
            update_option('wbgs_custom_option_text', $custom_text);
            echo '<div class="updated"><p>Saved successfully.</p></div>';
        endif;

        $saved_value = get_option('wbgs_custom_option_text', '');
        ?>
        <form method="post">
            <?php wp_nonce_field('wbgs_custom_option_save'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="wbgs_custom_option_text"><?php esc_html_e('Section Heading', 'smart-countdown-scarcity'); ?></label>
                    </th>
                    <td>
                        <input type="text" name="wbgs_custom_option_text" id="wbgs_custom_option_text" value="<?php echo esc_attr($saved_value); ?>" class="regular-text" />
                    </td>
                </tr>
            </table>
            <?php submit_button(__('Save', 'smart-countdown-scarcity')); ?>
        </form>
    </div>
    <?php
}



 }

    new WBGS_SmartCountdownScarcitySetting();
}




