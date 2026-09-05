<?php
/**
 * Plugin Name: WooCommerce Lightweight Social Proof
 * Plugin URI:  https://github.com/your-username/woocommerce-lightweight-social-proof
 * Description: High-performance, zero-dependency sales popup notification system for WooCommerce.
 * Version:     1.1.0
 * Author:      Michael Nnah
 * License:     GPL-2.0+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wc-lightweight-social-proof
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'WCLSP_VERSION', '1.1.0' );
define( 'WCLSP_PATH', plugin_dir_path( __FILE__ ) );
define( 'WCLSP_URL', plugin_dir_url( __FILE__ ) );

// Load backend logic & admin
require_once WCLSP_PATH . 'includes/class-social-proof-ajax.php';
require_once WCLSP_PATH . 'includes/class-social-proof-admin.php';

// Initialize hooks
add_action( 'plugins_loaded', function() {
    if ( class_exists( 'WooCommerce' ) ) {
        WCLSP_Social_Proof_Ajax::init();
        if ( is_admin() ) {
            WCLSP_Social_Proof_Admin::init();
        }
    }
});

// Enqueue frontend scripts & styles conditionally
add_action( 'wp_enqueue_scripts', function() {
    if ( is_front_page() || is_shop() || is_product_taxonomy() ) {
        $options = WCLSP_Social_Proof_Admin::get_options();

        wp_enqueue_style(
            'wclsp-styles',
            WCLSP_URL . 'assets/css/social-proof.css',
            array(),
            WCLSP_VERSION
        );

        // Inject user custom properties into the stylesheet
        $custom_css = "
            :root {
                --wclsp-bg: {$options['bg_color']};
                --wclsp-text: {$options['text_color']};
                --wclsp-accent: {$options['accent_color']};
                --wclsp-badge: {$options['badge_color']};
                --wclsp-font: {$options['font_family']};
            }
        ";
        wp_add_inline_style( 'wclsp-styles', $custom_css );

        wp_enqueue_script(
            'wclsp-script',
            WCLSP_URL . 'assets/js/social-proof.js',
            array(),
            WCLSP_VERSION,
            true
        );

        wp_localize_script( 'wclsp-script', 'wclspData', array(
            'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
            'nonce'           => wp_create_nonce( 'wclsp_sales_nonce' ),
            'initialDelay'    => intval( $options['initial_delay'] ) * 1000,             'displayDuration' => intval($options['display_duration'] ) * 1000,
            'minInterval'     => intval( $options['min_interval'] ) * 1000,             'maxInterval'     => intval($options['max_interval'] ) * 1000,
        ));
    }
});

// Render popup markup in footer
add_action( 'wp_footer', function() {
    if ( is_front_page() || is_shop() || is_product_taxonomy() ) {
        load_template( WCLSP_PATH . 'templates/popup-markup.php', false );
    }
});
