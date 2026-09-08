<?php
/**
 * Plugin Name:       WooCommerce Sales Popup – Lightweight Live Sales Notification & Social Proof
 * Plugin URI:        https://wclsp.michaelnnah.com/
 * Description:       Fast, zero-bloat sales notification popup for WooCommerce. Displays recent orders with smart item bundling and customizable viewport clearance.
 * Version:           1.2.0
 * Author:            Michael Nnah
 * Author URI:        https://michaelnnah.com/
 * Text Domain:       wc-lightweight-social-proof
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'WCLSP_VERSION', '1.2.0' );
define( 'WCLSP_PATH', plugin_dir_path( __FILE__ ) );
define( 'WCLSP_URL', plugin_dir_url( __FILE__ ) );

// Load classes
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

    if ( class_exists( 'WCLSP_Social_Proof_Admin' ) ) {
        WCLSP_Social_Proof_Admin::init();
    }
}

// Check if current frontend view is eligible
function wclsp_should_run() {
    if ( is_admin() || wp_doing_ajax() ) {
        return false;
    }
    return true;
}

// Enqueue frontend scripts & styles unconditionally on frontend
add_action( 'wp_enqueue_scripts', function() {
    if ( ! wclsp_should_run() ) {
        return;
    }

    $options = class_exists( 'WCLSP_Social_Proof_Admin' ) 
        ? WCLSP_Social_Proof_Admin::get_options() 
        : array();

    // Cache buster via filemtime
    $css_path = WCLSP_PATH . 'assets/css/social-proof.css';
    $css_ver  = file_exists( $css_path ) ? filemtime( $css_path ) : WCLSP_VERSION;

    wp_enqueue_style(
        'wclsp-styles',
        WCLSP_URL . 'assets/css/social-proof.css',
        array(),
        $css_ver
    );

    $bg          = esc_attr( $options['bg_color'] ?? '#151515' );
    $text        = esc_attr( $options['text_color'] ?? '#ffffff' );
    $accent      = esc_attr( $options['accent_color'] ?? '#D4AF37' );
    $badge       = esc_attr( $options['badge_color'] ?? '#25D366' );
    $font        = esc_attr( $options['font_family'] ?? 'inherit' );
    $pos_desk    = esc_attr( $options['position_desktop'] ?? 'bottom-left' );
    $offset_desk = intval( $options['bottom_offset_desk'] ?? 24 ) . 'px';
    $offset_mob  = intval( $options['bottom_offset_mob'] ?? 75 ) . 'px';
    $scale_mob   = ( floatval( $options['mobile_scale'] ?? 90 ) / 100 );

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

    $js_path = WCLSP_PATH . 'assets/js/social-proof.js';
    $js_ver  = file_exists( $js_path ) ? filemtime( $js_path ) : WCLSP_VERSION;

    wp_enqueue_script(
        'wclsp-script',
        WCLSP_URL . 'assets/js/social-proof.js',
        array(),
        $js_ver,
        true
    );

    wp_localize_script( 'wclsp-script', 'wclspData', array(
        'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
        'nonce'           => wp_create_nonce( 'wclsp_sales_nonce' ),
        'initialDelay'    => intval( $options['initial_delay'] ?? 6 ) * 1000,
        'displayDuration' => intval( $options['display_duration'] ?? 6 ) * 1000,
        'minInterval'     => intval( $options['min_interval'] ?? 15 ) * 1000,
        'maxInterval'     => intval( $options['max_interval'] ?? 30 ) * 1000,
    ));
}, 20 );

// Render popup markup in footer
add_action( 'wp_footer', function() {
    if ( wclsp_should_run() ) {
        load_template( WCLSP_PATH . 'templates/popup-markup.php', false );
    }
}, 99 );
