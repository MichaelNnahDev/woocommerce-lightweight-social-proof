<?php
/**
 * Plugin Name:       Lightweight Sales Popup for Woo
 * Plugin URI:        https://wclsp.michaelnnah.com
 * Description:       Ultra-lightweight, zero-bloat live sales popup and social proof notification engine for Woo stores.
 * Version:           1.2.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Michael Nnah
 * Author URI:        https://michaelnnah.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wc-lightweight-social-proof
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'WCLSP_VERSION', '1.2.0' );
define( 'WCLSP_PATH', plugin_dir_path( __FILE__ ) );
define( 'WCLSP_URL', plugin_dir_url( __FILE__ ) );

// Safe Bootstrap
add_action( 'plugins_loaded', 'wclsp_bootstrap_plugin' );

function wclsp_bootstrap_plugin() {
    // Check if WooCommerce is installed and active
    if ( ! class_exists( 'WooCommerce' ) && ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        add_action( 'admin_notices', 'wclsp_missing_woocommerce_notice' );
        return;
    }

    // 1. Load the AJAX / frontend class
    require_once WCLSP_PATH . 'includes/class-social-proof-ajax.php';

    // 2. Load and instantiate Admin settings
    require_once WCLSP_PATH . 'includes/class-social-proof-admin.php';
    if ( is_admin() && class_exists( 'WCLSP_Admin' ) ) {
        new WCLSP_Admin();
    }
}

/**
 * Notice displayed if WooCommerce is inactive or missing.
 */
function wclsp_missing_woocommerce_notice() {
    if ( ! current_user_can( 'activate_plugins' ) ) {
        return;
    }
    ?>
    <div class="notice notice-error is-dismissible">
        <p><strong>Lightweight Sales Popup for Woo</strong> requires <strong>WooCommerce</strong> to be installed and activated.</p>
    </div>
    <?php
}
