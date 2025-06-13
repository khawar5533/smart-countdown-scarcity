<?php

if (!class_exists('WBGS_SmartCountdownScarcitySetting')) {
    class WBGS_SmartCountdownScarcitySetting {

        public function __construct() {
            add_action('admin_menu', [ $this, 'add_settings_menu' ]);
            add_action('admin_init', [ $this, 'register_settings' ]);
            add_action('wp_ajax_wbgs_get_product_meta', [$this,'wbgs_get_product_meta']);
        }
// For setting menu
        public function add_settings_menu() {
            add_menu_page(
                'Smart Countdown Settings',        // Page title
                'Countdown Settings',              // Menu title
                'manage_options',                  // Capability
                'wbgs_settings',                   // Menu slug
                [ $this, 'render_settings_page' ], // Callback
                'dashicons-clock',                 // Icon
                56
            );
        }
// For Register setting
        public function register_settings() {
            register_setting('wbgs_plugin_settings', 'wbgs_settings_options');

            add_settings_section(
                'wbgs_section_main',
                'Countdown Settings',
                function() {
                    echo '<p>Settings for the countdown timer.</p>';
                },
                'wbgs_settings'
            );

            add_settings_field(
                'selected_product',
                'Select Product',
                [ $this, 'render_product_dropdown' ],
                'wbgs_settings',
                'wbgs_section_main'
            );

            add_settings_field(
                'product_status',
                'Enable/Disable',
                [ $this, 'render_radio_buttons' ],
                'wbgs_settings',
                'wbgs_section_main'
            );
        }
    // Create the product dropdown
         public function render_product_dropdown() {
            $options = get_option('wbgs_settings_options');
            $selected_product = isset($options['selected_product']) ? $options['selected_product'] : '';

            $args = [
                'post_type'      => 'product',
                'posts_per_page' => -1,
                'post_status'    => 'publish',
            ];

            $products = get_posts($args);

            echo '<select name="wbgs_settings_options[selected_product]" id="wbgs_select_product">';
            echo '<option value="">-- Select a Product --</option>';
            foreach ($products as $product) {
                echo '<option value="' . esc_attr($product->ID) . '" ' . selected($selected_product, $product->ID, false) . '>' . esc_html($product->post_title) . '</option>';
            }
            echo '</select>';
        }
        //Create the radio button option
        public function render_radio_buttons() {
            $options = get_option('wbgs_settings_options');
            $status = isset($options['product_status']) ? $options['product_status'] : 'enable';

            echo '<label><input type="radio" name="wbgs_settings_options[product_status]" value="enable" ' . checked($status, 'enable', false) . '> Enable</label><br>';
            echo '<label><input type="radio" name="wbgs_settings_options[product_status]" value="disable" ' . checked($status, 'disable', false) . '> Disable</label>';
        }
        //Render the page settings
        
       public function render_settings_page() {

            $options = get_option('wbgs_settings_options');
            $product_id = isset($options['selected_product']) ? $options['selected_product'] : '';
            $status = isset($options['product_status']) ? $options['product_status'] : '';
            $product_title = $product_id ? get_the_title($product_id) : 'Not selected';

            // Get WooCommerce product
            $product = $product_id ? wc_get_product($product_id) : null;
            $sale_price = $product ? $product->get_sale_price() : '';
            // Custom Meta
            $current_time = current_time('timestamp');
            $remaining_time = '';
            $end_date      = get_post_meta($product_id, 'wbgs_end_date', true);

            $end_timestamp = strtotime($end_date);
            if ($current_time < $end_timestamp) {
            $diff_seconds = $end_timestamp - $current_time;
            $days    = floor($diff_seconds / 86400);
            $hours   = floor(($diff_seconds % 86400) / 3600);
            $minutes = floor(($diff_seconds % 3600) / 60);
            $seconds = $diff_seconds % 60;
                $remaining_time = "{$days}d {$hours}h {$minutes}m {$seconds}s";
            }else{
                $remaining_time = 'Expired'; 
            }

            $stock_alert   = get_post_meta($product_id, 'wbgs_stock_alert', true);
            $banner_image  = get_post_meta($product_id, 'wbgs_banner_image', true);
            ?>
            <div class="wrap">
                <h1><?php esc_html_e('Smart Countdown Scarcity Settings', 'smart-countdown-scarcity' ); ?></h1>
                <form action="options.php" method="post">
                    <?php
                    settings_fields('wbgs_plugin_settings');
                    do_settings_sections('wbgs_settings');
                     if ($product_id): ?>
                    <input type="hidden" name="wbgs_settings_options[wbgs_duration]" id="wbgs_duration" value="<?php //echo esc_attr($end_date); ?>" />
                    <input type="hidden" name="wbgs_settings_options[wbgs_stock_alert]" id="wbgs_stock_alert" value="<?php //echo esc_attr($stock_alert); ?>" />
                    <input type="hidden" name="wbgs_settings_options[wbgs_banner_image]" id="wbgs_banner_image" value="<?php //echo esc_url($banner_image); ?>" />
                <?php endif; ?>
                <?php submit_button();?>
                </form>
                    <h2><?php esc_html_e('Saved Product Status', 'smart-countdown-scarcity' ); ?></h2>
                    <table class="wp-list-table widefat fixed striped table-view-list">
                        <thead>
                            <tr>
                                <th scope="col" class="manage-column column-title column-primary"><?php esc_html_e('Product', 'smart-countdown-scarcity' ); ?></th>
                                <th scope="col" class="manage-column"><?php esc_html_e('Status', 'smart-countdown-scarcity' ); ?></th>
                                <th scope="col" class="manage-column"><?php esc_html_e('Metadata', 'smart-countdown-scarcity' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="column-primary">
                                    <?php echo esc_html($product_title); ?>
                                    <button type="button" class="toggle-row"><span class="screen-reader-text"><?php esc_html_e('Show details', 'smart-countdown-scarcity' ); ?></span></button>
                                </td>
                                <td data-colname="Status"><?php echo esc_html(ucfirst($status)); ?></td>
                                <td data-colname="Metadata">
                                    <?php 
                                    if ($product): ?>
                                        <ul style="margin: 0; padding-left: 1.2em; list-style: disc;">
                                            <?php if ($sale_price): ?><li><strong><?php esc_html_e('Sale Price:', 'smart-countdown-scarcity' ); ?></strong> <?php echo esc_html($sale_price); ?></li><?php endif; ?>
                                            <?php if ($stock_alert ): ?><li><strong><?php esc_html_e('Stock Alert:', 'smart-countdown-scarcity' ); ?></strong> <?php echo esc_html($stock_alert) ; ?></li><?php endif; ?>
                                            <?php if ($banner_image): ?><li><strong><?php esc_html_e('Banner Image:', 'smart-countdown-scarcity' ); ?></strong> <img src="<?php echo esc_url($banner_image); ?>" alt="" style="max-width: 20px; height: auto;"></li><?php endif; ?>
                                            <?php if ($end_date): ?>
                                                <li><strong><?php esc_html_e('Time Remaining:', 'smart-countdown-scarcity' ); ?></strong> 
                                                <?php echo esc_html($remaining_time); ?></li>
                                                <?php endif; ?>
                                        </ul>
                                        <?php else: ?>
                                            <em><?php esc_html_e('No product selected.', 'smart-countdown-scarcity' ); ?></em>
                                        <?php endif; ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php
        }
        //get the product meta and append in hidden value
        public function wbgs_get_product_meta() {

            if (!current_user_can('manage_options')) {
                wp_send_json_error(['message' => 'Unauthorized']);
            }

            $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

            if (!$product_id || get_post_type($product_id) !== 'product') {
                wp_send_json_error(['message' => 'Invalid product ID']);
            }

            $duration = get_post_meta($product_id, 'wbgs_end_date', true);
            $stock_alert = get_post_meta($product_id, 'wbgs_stock_alert', true);
            $banner_image = get_post_meta($product_id, 'wbgs_banner_image', true);

            wp_send_json_success([
                'wbgs_duration' => $duration,
                'wbgs_stock_alert' => $stock_alert,
                'wbgs_banner_image' => $banner_image,
            ]);

    }

 }

    new WBGS_SmartCountdownScarcitySetting();
}


