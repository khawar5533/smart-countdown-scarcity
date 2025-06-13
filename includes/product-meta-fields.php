<?php
if (!class_exists('WBGS_Productmeta')) {
  class WBGS_Productmeta{
    public function __construct() {
        add_action('woocommerce_product_options_general_product_data', [$this, 'wbgs_add_text_field']);
        add_action('woocommerce_process_product_meta', [$this, 'wbgs_save_field']);
    }
    public function wbgs_add_text_field() {
        echo '<div class="options_group">';
        
        woocommerce_wp_text_input([
            'id' => 'wbgs_end_date',
            'label' => __('End Time', 'woocommerce'),
            'description' => __('Enter the sale end time', 'smart-countdown-scarcity'),
            'desc_tip' => 'true',
            'type' => 'datetime-local'
        ]);
        woocommerce_wp_text_input([
            'id' => 'wbgs_stock_alert',
            'label' => __('Stock Alert', 'woocommerce'),
            'description' => __('Enter the stock alert', 'smart-countdown-scarcity'),
            'desc_tip' => 'true',
            'type' => 'text'
        ]);
          woocommerce_wp_text_input([
            'id' => 'wbgs_banner_image',
            'label' => __('Banner Image', 'woocommerce'),
            'description' => __('Upload banner image', 'smart-countdown-scarcity'),
            'desc_tip' => 'true',
        ]);
        ?>
        <p class="form-field">
        <button type="button" class="upload_image_button button"><?php _e('Upload Banner Image'); ?></button>
        </p>
        <?php

        echo '</div>';
    }
    public function wbgs_save_field($post_id) {
        $end_date = isset($_POST['wbgs_end_date']) ? sanitize_text_field($_POST['wbgs_end_date']) : '';
        //save stock alert
        $stock_alert = isset($_POST['wbgs_stock_alert']) ? sanitize_text_field($_POST['wbgs_stock_alert']) : '';
        //save banner image
        $banner_image = isset($_POST['wbgs_banner_image']) ? sanitize_text_field($_POST['wbgs_banner_image']) : '';
        if($end_date){
            $timestamp = strtotime($end_date); // Convert to Unix timestamp
            $formatted_datetime = date('Y-m-d H:i:s', $timestamp); // Format it
            update_post_meta($post_id, 'wbgs_end_date', $formatted_datetime);
        }
        
          update_post_meta($post_id, 'wbgs_stock_alert', $stock_alert);
          update_post_meta($post_id, 'wbgs_banner_image', $banner_image);
       
    }
  }
  new WBGS_Productmeta();
}

