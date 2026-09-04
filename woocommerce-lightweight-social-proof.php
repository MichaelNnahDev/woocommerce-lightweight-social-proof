<?php
/**
 * Plugin Name: WooCommerce Lightweight Social Proof
 * Plugin URI:  https://github.com/your-username/woocommerce-lightweight-social-proof
 * Description: High-performance, zero-dependency sales popup notification system for WooCommerce.
 * Version:     1.0.0
 * Author:      Your Name
 * License:     GPL-2.0+
 * Text Domain: wc-lightweight-social-proof
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'WCLSP_VERSION', '1.0.0' );
define( 'WCLSP_PATH', plugin_dir_path( __FILE__ ) );
define( 'WCLSP_URL', plugin_dir_url( __FILE__ ) );

// Load backend logic
require_once WCLSP_PATH . 'includes/class-social-proof-ajax.php';

// Initialize backend handler
add_action( 'plugins_loaded', function() {
    if ( class_exists( 'WooCommerce' ) ) {
        WCLSP_Social_Proof_Ajax::init();
    }
});

// Enqueue frontend scripts & styles conditionally
add_action( 'wp_enqueue_scripts', function() {
    if ( is_front_page() || is_shop() || is_product_taxonomy() ) {
        wp_enqueue_style(
            'wclsp-styles',
            WCLSP_URL . 'assets/css/social-proof.css',
            array(),
            WCLSP_VERSION
        );

        wp_enqueue_script(
            'wclsp-script',
            WCLSP_URL . 'assets/js/social-proof.js',
            array(),
            WCLSP_VERSION,
            true
        );

        wp_localize_script( 'wclsp-script', 'wclspData', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'wclsp_sales_nonce' ),
        ));
    }
});

// Render the popup markup in footer
add_action( 'wp_footer', function() {
    if ( is_front_page() || is_shop() || is_product_taxonomy() ) {
        load_template( WCLSP_PATH . 'templates/popup-markup.php', false );
    }
});
