<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WCLSP_Social_Proof_Admin' ) ) :

class WCLSP_Social_Proof_Admin {

    public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'add_settings_page' ) );
        add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
    }

    public static function get_defaults() {
        return array(
            'bg_color'         => '#151515',
            'text_color'       => '#ffffff',
            'accent_color'     => '#D4AF37',
            'badge_color'      => '#25D366',
            'font_family'      => 'inherit',
            'initial_delay'    => 6,
            'display_duration' => 6,
            'min_interval'     => 15,
            'max_interval'     => 30,
            'order_hours'      => 48,
            'cache_minutes'    => 5,
        );
    }

    public static function get_options() {
        $defaults = self::get_defaults();
        $saved    = get_option( 'wclsp_settings', array() );
        return wp_parse_args( $saved, $defaults );
    }

    public static function add_settings_page() {
        add_submenu_page(
            'woocommerce',
            __( 'Social Proof Settings', 'wc-lightweight-social-proof' ),
            __( 'Social Proof', 'wc-lightweight-social-proof' ),
            'manage_woocommerce',
            'wclsp-settings',
            array( __CLASS__, 'render_settings_page' )
        );
    }

    public static function register_settings() {
        register_setting( 'wclsp_settings_group', 'wclsp_settings', array(
            'sanitize_callback' => array( __CLASS__, 'sanitize_settings' ),
        ) );

        add_settings_section(
            'wclsp_style_section',
            __( 'Visual & Styling', 'wc-lightweight-social-proof' ),
            '__return_empty_string',
            'wclsp-settings'
        );

        add_settings_field( 'bg_color', __( 'Background Color', 'wc-lightweight-social-proof' ), array( __CLASS__, 'field_color' ), 'wclsp-settings', 'wclsp_style_section', array( 'id' => 'bg_color' ) );
        add_settings_field( 'text_color', __( 'Text Color', 'wc-lightweight-social-proof' ), array( __CLASS__, 'field_color' ), 'wclsp-settings', 'wclsp_style_section', array( 'id' => 'text_color' ) );
        add_settings_field( 'accent_color', __( 'Accent / Highlight Color', 'wc-lightweight-social-proof' ), array( __CLASS__, 'field_color' ), 'wclsp-settings', 'wclsp_style_section', array( 'id' => 'accent_color' ) );
        add_settings_field( 'badge_color', __( 'Verified Badge Color', 'wc-lightweight-social-proof' ), array( __CLASS__, 'field_color' ), 'wclsp-settings', 'wclsp_style_section', array( 'id' => 'badge_color' ) );
        add_settings_field( 'font_family', __( 'Font Family (CSS)', 'wc-lightweight-social-proof' ), array( __CLASS__, 'field_text' ), 'wclsp-settings', 'wclsp_style_section', array( 'id' => 'font_family', 'desc' => 'e.g. "Mulish", sans-serif or leave as inherit' ) );

        add_settings_section(
            'wclsp_behavior_section',
            __( 'Timing & Behavior', 'wc-lightweight-social-proof' ),
            '__return_empty_string',
            'wclsp-settings'
        );

        add_settings_field( 'initial_delay', __( 'Initial Delay (seconds)', 'wc-lightweight-social-proof' ), array( __CLASS__, 'field_number' ), 'wclsp-settings', 'wclsp_behavior_section', array( 'id' => 'initial_delay', 'min' => 1, 'max' => 60 ) );
        add_settings_field( 'display_duration', __( 'Display Duration (seconds)', 'wc-lightweight-social-proof' ), array( __CLASS__, 'field_number' ), 'wclsp-settings', 'wclsp_behavior_section', array( 'id' => 'display_duration', 'min' => 2, 'max' => 30 ) );
        add_settings_field( 'min_interval', __( 'Minimum Interval (seconds)', 'wc-lightweight-social-proof' ), array( __CLASS__, 'field_number' ), 'wclsp-settings', 'wclsp_behavior_section', array( 'id' => 'min_interval', 'min' => 5, 'max' => 300 ) );
        add_settings_field( 'max_interval', __( 'Maximum Interval (seconds)', 'wc-lightweight-social-proof' ), array( __CLASS__, 'field_number' ), 'wclsp-settings', 'wclsp_behavior_section', array( 'id' => 'max_interval', 'min' => 5, 'max' => 300 ) );

        add_settings_section(
            'wclsp_query_section',
            __( 'Query & Cache Settings', 'wc-lightweight-social-proof' ),
            '__return_empty_string',
            'wclsp-settings'
        );

        add_settings_field( 'order_hours', __( 'Order History Scope (hours)', 'wc-lightweight-social-proof' ), array( __CLASS__, 'field_number' ), 'wclsp-settings', 'wclsp_query_section', array( 'id' => 'order_hours', 'min' => 1, 'max' => 720 ) );
        add_settings_field( 'cache_minutes', __( 'Transient Cache Lifetime (minutes)', 'wc-lightweight-social-proof' ), array( __CLASS__, 'field_number' ), 'wclsp-settings', 'wclsp_query_section', array( 'id' => 'cache_minutes', 'min' => 1, 'max' => 120 ) );
    }

    public static function sanitize_settings( $input ) {
        $sanitized = array();
        $defaults  = self::get_defaults();

        $clean_hex = function( $color, $default ) {
            if ( ! empty( $color ) && preg_match( '/^#([a-fA-F0-9]{3}){1,2}$/', $color ) ) {
                return $color;
            }
            return $default;
        };

        $sanitized['bg_color']         = $clean_hex( $input['bg_color'] ?? '', $defaults['bg_color'] );
        $sanitized['text_color']       = $clean_hex( $input['text_color'] ?? '', $defaults['text_color'] );
        $sanitized['accent_color']     = $clean_hex( $input['accent_color'] ?? '', $defaults['accent_color'] );
        $sanitized['badge_color']      = $clean_hex( $input['badge_color'] ?? '', $defaults['badge_color'] );
        $sanitized['font_family']      = sanitize_text_field( $input['font_family'] ?? $defaults['font_family'] );
        $sanitized['initial_delay']    = absint( $input['initial_delay'] ?? $defaults['initial_delay'] );
        $sanitized['display_duration'] = absint( $input['display_duration'] ?? $defaults['display_duration'] );
        $sanitized['min_interval']     = absint( $input['min_interval'] ?? $defaults['min_interval'] );
        $sanitized['max_interval']     = absint( $input['max_interval'] ?? $defaults['max_interval'] );
        $sanitized['order_hours']      = absint( $input['order_hours'] ?? $defaults['order_hours'] );
        $sanitized['cache_minutes']    = absint( $input['cache_minutes'] ?? $defaults['cache_minutes'] );

        delete_transient( 'wclsp_social_proof_cache' );

        return $sanitized;
    }

    public static function field_color( $args ) {
        $opts = self::get_options();
        $id   = $args['id'];
        $val  = esc_attr( $opts[ $id ] );
        echo '<input type="color" name="wclsp_settings[' . esc_attr( $id ) . ']" value="' . $val . '" style="vertical-align:middle; width:50px; height:34px; padding:0; cursor:pointer;"> ';
        echo '<input type="text" value="' . $val . '" style="width:90px; vertical-align:middle;" readonly>';
    }

    public static function field_text( $args ) {
        $opts = self::get_options();
        $id   = $args['id'];
        $val  = esc_attr( $opts[ $id ] );
        echo '<input type="text" name="wclsp_settings[' . esc_attr( $id ) . ']" value="' . $val . '" class="regular-text">';
        if ( ! empty( $args['desc'] ) ) {
            echo '<p class="description">' . esc_html( $args['desc'] ) . '</p>';
        }
    }

    public static function field_number( $args ) {
        $opts = self::get_options();
        $id   = $args['id'];
        $val  = esc_attr( $opts[ $id ] );
        $min  = isset( $args['min'] ) ? ' min="' . intval( $args['min'] ) . '"' : '';
        $max  = isset( $args['max'] ) ? ' max="' . intval( $args['max'] ) . '"' : '';
        echo '<input type="number" name="wclsp_settings[' . esc_attr( $id ) . ']" value="' . $val . '" class="small-text"' . $min . $max . '>';
    }

    public static function render_settings_page() {
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'WooCommerce Lightweight Social Proof Settings', 'wc-lightweight-social-proof' ); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields( 'wclsp_settings_group' );
                do_settings_sections( 'wclsp-settings' );
                submit_button( __( 'Save Changes', 'wc-lightweight-social-proof' ) );
                ?>
            </form>
        </div>
        <?php
    }
}

endif;
