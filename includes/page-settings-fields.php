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
                <select id="wbgs_modal_product_id" name="wbgs_product_id">
                    <option value=""><?php esc_html_e('-- Select --', 'smart-countdown-scarcity'); ?></option>
                    <?php
                    $products = get_posts(['post_type' => 'product', 'numberposts' => -1]);
                    foreach ($products as $p) {
                        echo '<option value="' . esc_attr($p->ID) . '">' . esc_html($p->post_title) . '</option>';
                    }
                    ?>
                </select><br><br>
                <label><?php esc_html_e('Select Template', 'smart-countdown-scarcity'); ?></label><br>
                <select id="wbgs_template_select" name="wbgs_template_select">
                    <option value=""><?php esc_html_e('-- Please choose a template --', 'smart-countdown-scarcity'); ?></option>
                    <option value="<?php echo esc_attr('template_1'); ?>"><?php esc_html_e('Template 1', 'smart-countdown-scarcity'); ?></option>
                    <option value="<?php echo esc_attr('template_2' ); ?>"><?php esc_html_e('Template 2', 'smart-countdown-scarcity'); ?></option>
                    <option value="<?php echo esc_attr('template_3' ); ?>"><?php esc_html_e('Template 3', 'smart-countdown-scarcity'); ?></option>
                    <option value="<?php echo esc_attr('template_4' ); ?>"><?php esc_html_e('Template 4', 'smart-countdown-scarcity'); ?></option>
                </select><br><br>

                <label for="wbgs_item_title"><?php esc_html_e('Title', 'smart-countdown-scarcity'); ?></label><br>
                <input type="text" id="wbgs_item_title" name="wbgs_item_title" style="width:100%;"><br><br>

                <label for="wbgs_item_subtitle"><?php esc_html_e('Sub Title', 'smart-countdown-scarcity'); ?></label><br>
                <input type="text" id="wbgs_item_subtitle" name="wbgs_item_subtitle" style="width:100%;"><br><br>

                <label for="wbgs_item_description"><?php esc_html_e('Description', 'smart-countdown-scarcity'); ?></label><br>
                <textarea id="wbgs_item_description" name="wbgs_item_description" rows="4" style="width:100%;"></textarea><br><br>

                <label><?php esc_html_e('Stock Alert', 'smart-countdown-scarcity'); ?></label><br>
                <input type="number" id="wbgs_modal_stock_alert" name="stock_alert"><br><br>

                <label><?php esc_html_e('End Date/Time', 'smart-countdown-scarcity'); ?></label><br>
                <input type="datetime-local" id="wbgs_modal_end_time" name="end_time" step="1"><br><br>
                <!-- Banner Title -->
                <label for="wbgs_sale_title"><?php esc_html_e('Flash Sale Title', 'smart-countdown-scarcity'); ?></label><br>
                <input type="text" id="wbgs_sale_title" name="wbgs_sale_title" class="regular-text" placeholder="<?php esc_attr_e('Flash Sale Title e.g UPTO', 'smart-countdown-scarcity'); ?>"><br><br>

                <!-- Discount Text -->
                <label for="wbgs_banner_discount"><?php esc_html_e('Discount Text', 'smart-countdown-scarcity'); ?></label><br>
                <input type="text" id="wbgs_banner_discount" name="wbgs_banner_discount" class="regular-text" placeholder="<?php esc_attr_e('Enter Discount Text e.g OFF', 'smart-countdown-scarcity'); ?>"><br><br>

                <!-- % Off -->
                <label for="wbgs_banner_percent"><?php esc_html_e('% Off', 'smart-countdown-scarcity'); ?></label><br>
                <input type="number" id="wbgs_banner_percent" name="wbgs_banner_percent" class="small-text" placeholder="e.g. 50"><br><br>


                <label><?php esc_html_e('Banner Image', 'smart-countdown-scarcity'); ?></label><br>
                <input type="hidden" id="wbgs_modal_banner" name="wbgs_modal_banner">
                <button class="button" id="wbgs_upload_banner"><?php esc_html_e('Select Image', 'smart-countdown-scarcity'); ?></button>
                <div id="wbgs_banner_preview" style="margin-top:10px;"></div><br>
            </div>
             <button class="button button-primary" id="wbgs_save_modal"><?php esc_html_e('Save Settings', 'smart-countdown-scarcity'); ?></button>
             <input type="hidden" id="wbgs_edit_id" name="wbgs_edit_id" value="">
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
            <div id="wbgs_loadingDiv">
                <?php echo esc_html( 'Loading, please wait...' ); ?>
            </div>
         <table id="wbgs_products_table"  cellpadding="8" cellspacing="0" border="1">
            <thead>
               <tr>
                <th><?php echo esc_html( 'Top Banner' ); ?></th>
                <th><?php echo esc_html( 'Name' ); ?></th>
                <th><?php echo esc_html( 'Stock Alert' ); ?></th>
                <th><?php echo esc_html( 'Duration' ); ?></th>
                <th><?php echo esc_html( 'Banner' ); ?></th>
                <th><?php echo esc_html( 'Template' ); ?></th>
                <th><?php echo esc_html( 'ShortCode' ); ?></th>
                <th><?php echo esc_html( 'Status' ); ?></th> 
                <th><?php echo esc_html( 'Action' ); ?></th>
            </tr>
            </thead>
            <tbody>
                <?php
                if(!empty($combined_data)){
                 foreach($combined_data as $key => $alert_data) { 
                    $product_id = isset($alert_data['id']) ? $alert_data['id'] : '';
                    $stock_alert = isset($alert_data['stock_alert']) ? $alert_data['stock_alert'] : '';
                    $end_time = isset($alert_data['end_time']) ? $alert_data['end_time'] : '';
                    $title = isset($alert_data['title']) ? $alert_data['title'] : '';
                    $subtitle = isset($alert_data['subtitle']) ? $alert_data['subtitle'] : '';
                    $description = isset($alert_data['description']) ? $alert_data['description'] : '';
                    $flashsaletitl = isset($alert_data['flashsaletitle']) ? $alert_data['flashsaletitle'] : '';
                    $discountTxt = isset($alert_data['discountoff']) ? $alert_data['discountoff'] : '';
                    $discountTitl = isset($alert_data['discounttitle']) ? $alert_data['discounttitle'] : '';

                    if (isset($alert_data['template'])) {
                        switch ($alert_data['template']) {
                            case 'template_1':
                                $template_name = 'First Template';
                                break;
                            case 'template_2':
                                $template_name = 'Second Template';
                                break;
                            case 'template_3':
                                $template_name = 'Third Template';
                                break;
                            case 'template_4':
                                $template_name = 'Fourth Template';
                                break;
                            default:
                                $template_name = 'Not Assigned Template';
                                break;
                        }
                    }


                    if ( ! empty( $end_time ) ) {
                        $date_format = get_option( 'date_format' );
                        $formatted_date = date_i18n( $date_format . ' h:i:s A', $end_time );
                    }

                    $banner_image = isset($alert_data['banner_image']) && !empty($alert_data['banner_image']) ? $alert_data['banner_image'] : plugin_dir_url(dirname(__FILE__)) . 'assets/images/default-banner.jpg';
                    $status = isset($alert_data['status']) ? $alert_data['status'] : '';
                    $shortcode = isset($alert_data['shortcode']) ? $alert_data['shortcode'] : '';
                    ?>
                <tr>
                    <td>
                    <input 
                        type="radio" 
                        id="wbgs-option-<?php echo esc_attr( $product_id ); ?>" 
                        name="choice" 
                        value="<?php echo esc_attr( $status ); ?>" 
                        data-product-id="<?php echo esc_attr( $product_id ); ?>" <?php echo ( isset($alert_data['status']) && $alert_data['status'] === 'enable' ) ? 'checked' : ''; ?>>
                        <span class="wbgs-checkmark"></span>
                    </td>
                    <td><?php echo esc_attr(get_the_title( $product_id  ));?></td>
                    <td><?php echo esc_attr($stock_alert); ?></td>
                    <td><?php echo esc_attr($formatted_date);?></td>
                    <td><img src="<?php echo esc_url($banner_image) ;?>" alt="Banner Image" width="50" height="25"></td>
                    <td><?php echo esc_attr($template_name);?></td>
                    <td><code>[wbgs_product_<?php echo esc_attr($product_id); ?>]</code></td>
                    <td><?php echo esc_attr($status);?></td>
                    <td class="wbgs-edit-icon">
                        <a href="#" class="wbgs-edit-button" data-product_id ="<?php echo esc_attr($product_id);?>"
                         data-template="<?php echo esc_attr($alert_data['template']);?>" data-title="<?php echo esc_attr($title);?>"
                         data-subtitle="<?php echo esc_attr($subtitle );?>" data-discounttxt="<?php echo esc_attr($discountTxt );?>"
                         data-description="<?php echo esc_attr($description);?>" data-flashsale="<?php echo esc_attr($flashsaletitl );?>" data-discountTitl="<?php echo esc_attr($discountTitl );?>"
                         data-stock="<?php echo esc_attr($stock_alert); ?>" data-end_time="<?php echo esc_attr($end_time );?>"
                         data-banner="<?php echo esc_url($banner_image) ;?>"  id="wbgs-editbox-<?php echo esc_attr($product_id);?>" value="">
                            <img src="<?php echo esc_url(plugin_dir_url(dirname(__FILE__)) . 'assets/images/edit.png'); ?>" alt="Edit" style="width:16px; height:16px;">
                        </a>
                    </td>
                </tr> 
                <?php }}else{ ?>
                    <tr><td colspan="9" style="text-align: center;"><?php esc_html_e('Not Recored Found', 'smart-countdown-scarcity'); ?></td></tr>
                <?php } ?>
            </tbody>
            </table>
            </div>
        <?php
     }
    //Save product settings
    public function wbgs_save_product_settings() {
        $alert_message = '';
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        if (!isset($_POST['wbgs_ajax_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['wbgs_ajax_nonce'])), 'wbgs_nonce')) {
            wp_send_json_error(['message' => 'Invalid nonce']);
        }

        $product_id = absint($_POST['product_id'] ?? 0);
        if (!$product_id) {
            wp_send_json_error(['message' => 'Invalid product ID']);
        }

        $data = [
            'title'           => isset($_POST['title']) ? sanitize_text_field(wp_unslash($_POST['title'])) : '',
            'subtitle'        => isset($_POST['subtitle']) ? sanitize_text_field(wp_unslash($_POST['subtitle'])) : '',
            'description'     => isset($_POST['description']) ? sanitize_text_field(wp_unslash($_POST['description'])) : '',
            'stock_alert'     => isset($_POST['stock_alert']) ? sanitize_text_field(wp_unslash($_POST['stock_alert'])) : '',
            'end_time'        => isset($_POST['end_time']) ? strtotime(sanitize_text_field(wp_unslash($_POST['end_time']))) : null,
            'banner_image'    => isset($_POST['banner_image']) ? esc_url_raw(wp_unslash($_POST['banner_image'])) : '',
            'flashsaletitle'  => isset($_POST['flashsaletitle']) ? sanitize_text_field(wp_unslash($_POST['flashsaletitle'])) : '',
            'discounttitle'   => isset($_POST['discounttitle']) ? sanitize_text_field(wp_unslash($_POST['discounttitle'])) : '',
            'discountoff'     => isset($_POST['discountoff']) ? sanitize_text_field(wp_unslash($_POST['discountoff'])) : '',
            'template'        => isset($_POST['template']) ? sanitize_text_field(wp_unslash($_POST['template'])) : '',
        ];

        if(isset($_POST['edit_id']) && !empty($_POST['edit_id'])){
           $data['edit_id'] = sanitize_text_field(wp_unslash($_POST['edit_id']) ?? '');
        }
        // Call the unified function
        $result = wbgs_save_and_register_product_data($product_id, $data);

        if (is_wp_error($result)) {
            wp_send_json_error(['message' => $result->get_error_message()]);
        }

        $shortcode = '[wbgs_product_' . $product_id . ']';
        if(!empty($data['edit_id'])){
            $alert_message = "Product settings update successfully.";
        }else{
           $alert_message = "Product settings saved successfully.";
        }
        wp_send_json_success([
            'message' => $alert_message,
            'shortcode' => $shortcode
        ]);
    }


    //update the product statius
   public function wbgs_edit_product_status() {

    // Nonce validation
    if (
        !isset($_POST['wbgs_ajax_nonce_edit']) ||
        !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['wbgs_ajax_nonce_edit'])), 'wbgs_edit_nonce')
    ) {
        wp_send_json_error(['message' => 'Invalid nonce']);
    }

    // Get and sanitize selected product ID
    $selected_id = isset($_POST['selected_product_id']) ? intval(wp_unslash($_POST['selected_product_id'])) : 0;

    // Get and sanitize all product IDs
    $all_ids = isset($_POST['all_product_ids'])
    ? (
        is_array($_POST['all_product_ids'])
            ? array_map('intval', wp_unslash($_POST['all_product_ids']))
            : array_map(
                'intval',
                explode(',', sanitize_text_field(wp_unslash($_POST['all_product_ids'])))
            )
    )
    : [];

    if (empty($all_ids) || !is_array($all_ids)) {
        wp_send_json_error(['message' => 'Invalid product IDs']);
    }

    $updated_ids = [];

    foreach ($all_ids as $product_id) {
        if ($product_id <= 0) continue;

        $status = ($product_id === $selected_id) ? 'enable' : 'disable';
        $option_key = 'wbgs_product_' . $product_id . '_data';
        $option_data = get_option($option_key);

        // Initialize if not found
        if (!is_array($option_data)) {
            $option_data = [];
        }

        // Update and save
        $option_data['status'] = $status;
        update_option($option_key, $option_data);

        $updated_ids[] = $product_id;
    }

    wp_send_json_success([
        'message' => 'Statuses updated successfully.',
        'updated_ids' => $updated_ids,
        'selected_enabled' => $selected_id,
    ]);
}

    public function wbgs_render_custom_option_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Custom Option Setting', 'smart-countdown-scarcity'); ?></h1>
        <?php if (isset($_POST['wbgs_custom_option_text'])) : 
            check_admin_referer('wbgs_custom_option_save');
            $custom_text = sanitize_text_field(wp_unslash($_POST['wbgs_custom_option_text']));
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




