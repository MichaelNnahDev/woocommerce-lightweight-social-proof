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
 * Text Domain:       lightweight-sales-popup-for-woo
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'WCLSP_VERSION', '1.2.0' );
define( 'WCLSP_PATH', plugin_dir_path( __FILE__ ) );
define( 'WCLSP_URL', plugin_dir_url( __FILE__ ) );

// 1. Load classes
require_once WCLSP_PATH . 'includes/class-social-proof-ajax.php';
require_once WCLSP_PATH . 'includes/class-social-proof-admin.php';

// 2. Initialize Admin settings
if ( is_admin() && class_exists( 'WCLSP_Social_Proof_Admin' ) ) {
    WCLSP_Social_Proof_Admin::init();
}

// 3. WooCommerce compatibility check
add_action( 'admin_notices', 'wclsp_check_woocommerce_dependency' );

function wclsp_check_woocommerce_dependency() {
    if ( ! class_exists( 'WooCommerce' ) && current_user_can( 'activate_plugins' ) ) {
        ?>
        <div class="notice notice-error is-dismissible">
            <p><strong>Lightweight Sales Popup for Woo</strong> requires <strong>WooCommerce</strong> to be installed and active.</p>
        </div>
        <?php
    }
}
