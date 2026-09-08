=== WooCommerce Sales Popup – Lightweight Live Sales Notification & Social Proof ===
Contributors: michaelnnahdev
Donate link: https://portfolio.michaelnnah.com/
Tags: sales-popup, sales-notification, woocommerce, social-proof, recent-sales
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Zero-dependency, high-performance sales notification popup system for WooCommerce stores.

== Description ==

Boost store trust and conversions with clean, non-intrusive recent order notifications. Designed specifically for stores requiring ultra-fast execution, zero external JS libraries, and total layout customization.

= Key Features =

* Native WooCommerce Integration: Automatically queries recent completed orders using standard WooCommerce CRUD methods.
* Custom Positioning: Choose between Bottom Left and Bottom Right placement.
* Mobile Clearance & Scaling: Adjust viewport offsets to cleanly clear floating WhatsApp buttons and sticky mobile menus.
* Full Design Customization: Direct HEX color inputs, background styling, and typography inheritance.
* Performance Focused: Zero external framework dependencies; transient caching eliminates unnecessary database hits.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/woocommerce-lightweight-social-proof` directory, or install the plugin directly through the WordPress plugins screen.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Navigate to WooCommerce > Social Proof to configure layout offsets, display timing, and visual styling.

== Frequently Asked Questions ==

= Does this plugin track personal customer data? =

No. The plugin only displays customer first names and shipping cities from completed orders, with no sensitive billing details.

= Will this slow down my store? =

No. Recent order lookups are cached via WordPress transients, making queries virtually instantaneous.

== Screenshots ==

1. Frontend social proof notification banner floating above bottom navigation.
2. Position and layout controls inside WooCommerce settings dashboard.

== Changelog ==

= 1.2.0 =
* Added desktop position controls (bottom-left / bottom-right).
* Added bottom-offset adjustments for desktop and mobile viewports.
* Added mobile scale slider (60% to 110%).
* Added direct HEX color code input and paste support.

= 1.0.0 =
* Initial release.
