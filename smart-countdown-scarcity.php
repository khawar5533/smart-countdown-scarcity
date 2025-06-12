<?php
/**
 * Plugin Name:       Smart Countdown Scarcity
 * Plugin URI:        https://example.com/my-plugin
 * Description:       Support WooCommerce product
 * Version:           1.0.0
 * Author:            webbuggs
 * Author URI:        https://example.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       smart-countdown-scarcity
 * Domain Path:       /languages
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
    }

    // Instantiate the class
    new WBGS_SmartCountdownScarcity();
}

//  Include additional files

require_once plugin_dir_path(__FILE__) . 'includes/page-settings-fields.php';
require_once plugin_dir_path(__FILE__) . 'includes/page-product-meta-fields.php';

