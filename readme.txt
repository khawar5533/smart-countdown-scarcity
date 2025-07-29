=== Smart Countdown Scarcity ===
Contributors: webbuggs  
Tags: countdown timer, scarcity, urgency, sale banner, woocommerce  
Requires at least: 6.7  
Tested up to: 6.8  
Requires PHP: 8.0  
Stable tag: 1.0.0  
License: GPLv2 or later  
License URI: https://www.gnu.org/licenses/gpl-2.0.html  

Display time-limited, product-specific sale banners on WooCommerce products to create urgency and increase conversions.

== Description ==

**Smart Countdown Scarcity** helps boost conversions and reduce cart abandonment by displaying dynamic, time-sensitive sale banners with countdown timers on WooCommerce product pages. The plugin automatically detects when a product is on sale and shows a countdown timer with discount information, creating a sense of urgency that encourages customers to complete purchases quickly.

**Key Features**

- Automatically detect and display banners for products currently on sale.
- Dynamic countdown timers synced with WooCommerce sale schedule.
- Customize banner appearance, messaging, and styling.
- Show banners on product pages, headers, footers, or any area via shortcode.
- Add multiple banners on a single page using shortcodes.
- Use shortcode in posts, pages, templates, or widgets: `[smart_countdown_banner]`
- Backend form to assign sale messages and select products via radio buttons.

== Requirements ==

- WordPress 6.7 or newer.
- WooCommerce 9.9.4 or newer.
- PHP 8.0 or higher.

== Installation ==

1. **Install and activate the plugin**

   - In your WordPress dashboard, go to **Plugins > Add New**.
   - Search for “Smart Countdown Scarcity”.
   - Click **Install Now**, then **Activate**.

2. **Configure your settings**

   - Navigate to **Countdown Settings** in the admin panel.
   - Customize the appearance and text of the sale banner using the form.
   - Assign sale banners to specific products.
   - Use the shortcode `[smart_countdown_banner]` anywhere on your site (including header, footer, posts, or pages).

== Usage ==

- Use the shortcode `[smart_countdown_banner]` in:
  - Classic Editor or Block Editor (Gutenberg).
  - Template files via `<?php echo do_shortcode('[smart_countdown_banner]'); ?>`
  - Widgets, headers, footers, or anywhere shortcode is supported.
- You can use the shortcode multiple times on a page to show multiple banners.

== Screenshots ==

1. Countdown banner displayed on a WooCommerce product page.
2. Admin panel with customization options.
3. Multiple banners shown on top and footer via shortcode.

== External Service Usage ==

This plugin **does not use** any external services or send data to third-party servers.

== Changelog ==

= 1.0.0 =
* Initial release.
* Added support for shortcode to show banners in multiple locations.
* Enhanced compatibility with WordPress block editor and WooCommerce.
