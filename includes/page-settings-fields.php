<?php

if (!class_exists('WBGS_SmartCountdownScarcitySetting')) {

    class WBGS_SmartCountdownScarcitySetting {

        public function __construct() {
            add_action('admin_menu', [ $this, 'add_settings_menu' ]);
            add_action('admin_init', [ $this, 'register_settings' ]);
        }

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


        }

        public function render_settings_page() {
            ?>
            <div class="wrap">
                <h1>Smart Countdown Scarcity Settings</h1>
                <form action="options.php" method="post">
                    <?php
                    settings_fields('wbgs_plugin_settings');
                    do_settings_sections('wbgs_settings');
                    submit_button();
                    ?>
                </form>
            </div>
            <?php
        }
    }

    new WBGS_SmartCountdownScarcitySetting();
}


