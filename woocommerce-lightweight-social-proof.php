<?php
/**
 * Plugin Name: WooCommerce Lightweight Social Proof
 * Plugin URI:  https://github.com/MichaelNnahDev/woocommerce-lightweight-social-proof
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

// Load classes safely
require_once WCLSP_PATH . 'includes/class-social-proof-ajax.php';
require_once WCLSP_PATH . 'includes/class-social-proof-admin.php';

// Safe Bootstrap
add_action( 'plugins_loaded', 'wclsp_bootstrap_plugin' );

function wclsp_bootstrap_plugin() {
    if ( ! class_exists( 'WooCommerce' ) ) {
        return;
    }

    if ( class_exists( 'WCLSP_Social_Proof_Ajax' ) ) {
        WCLSP_Social_Proof_Ajax::init();
    }

    if ( is_admin() && class_exists( 'WCLSP_Social_Proof_Admin' ) ) {
        WCLSP_Social_Proof_Admin::init();
    }
}

// Enqueue frontend scripts & styles conditionally
add_action( 'wp_enqueue_scripts', function() {
    if ( is_front_page() || is_shop() || is_product_taxonomy() ) {
        if ( ! class_exists( 'WCLSP_Social_Proof_Admin' ) ) {
            return;
        }

        $options = WCLSP_Social_Proof_Admin::get_options();

        wp_enqueue_style(
            'wclsp-styles',
            WCLSP_URL . 'assets/css/social-proof.css',
            array(),
            WCLSP_VERSION
        );

        $bg          = esc_attr( $options['bg_color'] );
        $text        = esc_attr( $options['text_color'] );
        $accent      = esc_attr( $options['accent_color'] );
        $badge       = esc_attr( $options['badge_color'] );
        $font        = esc_attr( $options['font_family'] );
        $pos_desk    = esc_attr( $options['position_desktop'] );
        $offset_desk = intval( $options['bottom_offset_desk'] ) . 'px';
        $offset_mob  = intval( $options['bottom_offset_mob'] ) . 'px';
        $scale_mob   = ( floatval( $options['mobile_scale'] ) / 100 );

        $left_desk   = ( $pos_desk === 'bottom-left' ) ? '24px' : 'auto';
        $right_desk  = ( $pos_desk === 'bottom-right' ) ? '24px' : 'auto';

        $custom_css = "
            :root {
                --wclsp-bg: {$bg};
                --wclsp-text: {$text};
                --wclsp-accent: {$accent};
                --wclsp-badge: {$badge};
                --wclsp-font: {$font};
                --wclsp-desk-bottom: {$offset_desk};
                --wclsp-desk-left: {$left_desk};
                --wclsp-desk-right: {$right_desk};
                --wclsp-mob-bottom: {$offset_mob};
                --wclsp-mob-scale: {$scale_mob};
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
            'initialDelay'    => intval( $options['initial_delay'] ) * 1000,
            'displayDuration' => intval( $options['display_duration'] ) * 1000,
            'minInterval'     => intval( $options['min_interval'] ) * 1000,
            'maxInterval'     => intval( $options['max_interval'] ) * 1000,
        ));
    }
});

// Render popup markup in footer
add_action( 'wp_footer', function() {
    if ( is_front_page() || is_shop() || is_product_taxonomy() ) {
        load_template( WCLSP_PATH . 'templates/popup-markup.php', false );
    }
});
