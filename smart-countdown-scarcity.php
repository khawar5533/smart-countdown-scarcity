<?php
/**
 * Plugin Name:       Smart Countdown Scarcity
 * Plugin URI:        https://www.webbuggs.com/
 * Description:       Display time-limited, product-specific sale banners on WooCommerce product page, showing discount details and countdown timers only during active sale periods to boost urgency and drive conversions.
 * Version:           1.0.0
 * Requires at least: 6.8
 * Author:            Webbuggs
 * Author URI:        https://www.webbuggs.com/
 * Text Domain:       smart-countdown-scarcity
 * License:           GPLv2 or later
 */

defined('ABSPATH') || exit;

// Activation hook to set a one-time flag
register_activation_hook(__FILE__, 'wbgs_plugin_activate');
function wbgs_plugin_activate() {
    update_option('wbgs_show_activation_notice', true);
}

if (!class_exists('WBGS_SmartCountdownScarcity')) {
    class WBGS_SmartCountdownScarcity {

        public function __construct() {
           //load script and css files
            add_action('admin_enqueue_scripts',[$this,'wbgs_enqueue_admin_scripts']);
            add_action('wp_enqueue_scripts', [$this, 'wbgs_enqueue_frontend_styles']);
            // Check dependencies when all plugins are loaded
            add_action('plugins_loaded', [$this, 'wbgs_check_woocommerce_dependency']);
        }

        // Check WooCommerce dependency
        public function wbgs_check_woocommerce_dependency() {
            if ($this->wbgs_is_woocommerce_active()) {
                $this->wbgs_init_plugin();
            } else {
                add_action('admin_notices', [$this, 'wbgs_woocommerce_missing_notice']);
                deactivate_plugins(plugin_basename(__FILE__));
            }
        }

        // Check if WooCommerce is active
        private function wbgs_is_woocommerce_active() {
            include_once ABSPATH . 'wp-admin/includes/plugin.php';
            return is_plugin_active('woocommerce/woocommerce.php');
        }

        // Initialize plugin features
        public function wbgs_init_plugin() {
            // Show activation notice only once
            if (get_option('wbgs_show_activation_notice')) {
                add_action('admin_notices', function () {
                    echo '<div class="notice notice-success is-dismissible"><p>Smart Countdown Scarcity is running because WooCommerce is active.</p></div>';
                });
                delete_option('wbgs_show_activation_notice');
            }

        }

        // WooCommerce not found notice
        public function wbgs_woocommerce_missing_notice() {
            echo '<div class="notice notice-error is-dismissible"><p><strong>Smart Countdown Scarcity</strong> requires WooCommerce to be installed and active.</p></div>';
        }
        //include the css using js and css for admin side
        public function wbgs_enqueue_admin_scripts() {

            // Enqueue media uploader
            wp_enqueue_media();
            // Enqueue your JS file
            wp_enqueue_script(
                'wbgs-admin-js',
                plugin_dir_url(__FILE__) . 'assets/js/wbgs-main.js',
                ['jquery'],
                true
            );
            // Localize the script to pass PHP data to JS
             wp_localize_script('wbgs-admin-js', 'wbgs_data', [
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce'   => wp_create_nonce('wbgs_nonce')
            ]);
            // Enqueue CSS
            wp_enqueue_style(
                'wbgs-admin-css',
                plugin_dir_url(__FILE__) . 'assets/css/wbgs-styles.css',
                [],
                null // version (you can specify a version if needed)
            );
        
        
        }
  //include the css using js and css for front side
        public function wbgs_enqueue_frontend_styles() {
            wp_enqueue_style(
                'wbgs-frontend-css',
                plugin_dir_url(__FILE__) . 'assets/css/wbgs-frontend.css',
                [],
                null // You can specify a version like '1.0.0'
            );
        }
    }

    // Instantiate the class
    new WBGS_SmartCountdownScarcity();
}

//  Include additional files

require_once plugin_dir_path(__FILE__) . 'includes/page-settings-fields.php';
require_once plugin_dir_path(__FILE__) . 'includes/page-salealert-front.php';
require_once plugin_dir_path(__FILE__) . 'helper/wbgs-utils.php';


