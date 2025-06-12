<?php
/**
 * Plugin Name:       Smart Countdown Scarcity
 * Plugin URI:        https://example.com/my-plugin
 * Description:       Support woocommerce product
 * Version:           1.0.0
 * Author:            webbuggs
 * Author URI:        https://example.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       smart-countdown-scarcity
 * Domain Path:       /languages
 */
 defined('ABSPATH') || exit;

 if(!class_exists('WBGS_SmartCountdownScarcity')){
    class WBGS_SmartCountdownScarcity{
         public function __construct() {
         // Check dependencies when all plugins are loaded
            add_action( 'plugins_loaded', [ $this, 'wbgs_check_woocommerce_dependency' ] );

         }
      // For check woocommerce dependency
         public function wbgs_check_woocommerce_dependency() {
         if ( $this->wbgs_is_woocommerce_active() ) {
            $this->wbgs_init_plugin();
         } else {
            add_action( 'admin_notices', [ $this, 'wbgs_woocommerce_missing_notice' ] );
            // Deactivate this plugin if WooCommerce is not active
            deactivate_plugins( plugin_basename( __FILE__ ) );
         }
      }
      // Check woocommerce plugin is active
      private function wbgs_is_woocommerce_active() {
         include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
         return is_plugin_active( 'woocommerce/woocommerce.php' );
      }
      //Smart Countdown Scarcity is running because WooCommerce is active
      public function wbgs_init_plugin() {
         // Your plugin’s actual functionality goes here
         add_action( 'admin_notices', function() {
            echo '<div class="notice notice-success"><p>Smart Countdown Scarcity is running because WooCommerce is active.</p></div>';
         } );
      }
      // For notices missing wooCommerce 
      public function wbgs_woocommerce_missing_notice() {
         echo '<div class="notice notice-error"><p><strong>Smart Countdown Scarcity</strong> requires WooCommerce to be installed and active.</p></div>';
      }
      
    }

    new WBGS_SmartCountdownScarcity();

 }