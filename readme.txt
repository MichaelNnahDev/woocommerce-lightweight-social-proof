=== Lightweight Sales Popup for Woo – Live Sales Notification & Social Proof ===
Contributors: michaelnnahdev
Donate link: https://portfolio.michaelnnah.com/
Tags: sales-popup, sales-notification, social-proof, recent-sales, ecommerce
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Zero-dependency, high-performance live sales notification popup system for Woo stores.

== Description ==

Boost store trust and conversions with clean, non-intrusive recent order notifications. Designed specifically for stores requiring ultra-fast execution, zero external JavaScript libraries, and total layout customization.

= Key Features =

* **Native Woo Integration**: Automatically queries recent orders using standard WooCommerce CRUD APIs.
* **Custom Screen Placement**: Select between Bottom-Left and Bottom-Right display positions.
* **Smart Mobile Clearance**: Configure dedicated viewport offsets to clear sticky checkout bars, cart drawers, and floating WhatsApp buttons.
* **Aesthetic Customization**: Direct HEX color inputs, verified badge tints, and typography font inheritance.
* **Performance Optimized**: Sub-3KB pure Vanilla JavaScript frontend engine with native transient caching to eliminate unnecessary database queries.

== Installation ==

= From the WordPress Dashboard =
1. Navigate to **Plugins** > **Add New**.
2. Search for `Lightweight Sales Popup for Woo` or upload the plugin zip file.
3. Click **Install Now**, then **Activate**.
4. Go to **WooCommerce** > **Social Proof** to configure display timing, colors, and layout offsets.

= Manual FTP / SFTP Installation =
1. Upload the unzipped `woocommerce-lightweight-social-proof` directory to your `/wp-content/plugins/` directory.
2. Activate the plugin via the **Plugins** screen in your WordPress dashboard.
3. Access configuration settings under **WooCommerce** > **Social Proof**.

== Frequently Asked Questions ==

= Does this plugin track or expose personal customer data? =
No. The plugin only displays buyer first names and shipping locations from eligible orders. No sensitive billing, street address, or payment data is ever exposed or transmitted.

= Will this slow down my store database? =
No. Recent order lookups are cached via WordPress native transient memory, ensuring subsequent page visits require zero heavy SQL operations.

= Can I use this without jQuery? =
Yes. The entire frontend engine is written in pure vanilla JavaScript and requires no external libraries or runtime frameworks.

== Screenshots ==

1. Frontend social proof notification banner floating cleanly above mobile navigation.
2. Position and layout controls inside the WooCommerce settings dashboard.
3. Visual styling and color customizer controls.

== Changelog ==

= 1.2.0 =
* Renamed header to comply with WordPress.org trademark directory standards.
* Added desktop placement options (Bottom-Left / Bottom-Right).
* Added independent bottom-offset controls for desktop and mobile devices.
* Added mobile display scaling parameter (60% to 110%).
* Added direct HEX color code input and synchronized color pickers.
* Improved transient cache invalidation routines.

= 1.0.0 =
* Initial public release.
