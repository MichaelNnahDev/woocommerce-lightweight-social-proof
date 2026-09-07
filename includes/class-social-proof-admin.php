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

        // Visual & Styling Section
        add_settings_section(
            'wclsp_style_section',
            __( 'Visual & Styling', 'wc-lightweight-social-proof' ),
            '__return_empty_string',
            'wclsp-settings'
        );

        add_settings_field( 'bg_color', __( 'Background Color', 'wc-lightweight-social-proof' ), array( __CLASS__, 'field_color' ), 'wclsp-settings', 'wclsp_style_section', array(
            'id'      => 'bg_color',
            'tooltip' => __( 'Background color of the popup card.', 'wc-lightweight-social-proof' ),
            'desc'    => __( 'Supports dark and light themes. Default: #151515', 'wc-lightweight-social-proof' ),
        ) );

        add_settings_field( 'text_color', __( 'Text Color', 'wc-lightweight-social-proof' ), array( __CLASS__, 'field_color' ), 'wclsp-settings', 'wclsp_style_section', array(
            'id'      => 'text_color',
            'tooltip' => __( 'Base font color for order descriptions and product titles.', 'wc-lightweight-social-proof' ),
            'desc'    => __( 'Default: #ffffff', 'wc-lightweight-social-proof' ),
        ) );

        add_settings_field( 'accent_color', __( 'Accent / Highlight Color', 'wc-lightweight-social-proof' ), array( __CLASS__, 'field_color' ), 'wclsp-settings', 'wclsp_style_section', array(
            'id'      => 'accent_color',
            'tooltip' => __( 'Color for buyer names, border accents, and links.', 'wc-lightweight-social-proof' ),
            'desc'    => __( 'Default: #D4AF37 (Metallic Gold)', 'wc-lightweight-social-proof' ),
        ) );

        add_settings_field( 'badge_color', __( 'Verified Badge Color', 'wc-lightweight-social-proof' ), array( __CLASS__, 'field_color' ), 'wclsp-settings', 'wclsp_style_section', array(
            'id'      => 'badge_color',
            'tooltip' => __( 'Color of the "Verified Buyer" tag indicator.', 'wc-lightweight-social-proof' ),
            'desc'    => __( 'Default: #25D366 (Emerald Green)', 'wc-lightweight-social-proof' ),
        ) );

        add_settings_field( 'font_family', __( 'Font Family (CSS)', 'wc-lightweight-social-proof' ), array( __CLASS__, 'field_text' ), 'wclsp-settings', 'wclsp_style_section', array(
            'id'      => 'font_family',
            'tooltip' => __( 'Specify an external or custom font stack, or inherit from your active WordPress theme.', 'wc-lightweight-social-proof' ),
            'desc'    => __( 'Example: "Mulish", sans-serif or enter "inherit".', 'wc-lightweight-social-proof' ),
        ) );

        // Timing & Behavior Section
        add_settings_section(
            'wclsp_behavior_section',
            __( 'Timing & Behavior', 'wc-lightweight-social-proof' ),
            '__return_empty_string',
            'wclsp-settings'
        );

        add_settings_field( 'initial_delay', __( 'Initial Delay (seconds)', 'wc-lightweight-social-proof' ), array( __CLASS__, 'field_number' ), 'wclsp-settings', 'wclsp_behavior_section', array(
            'id'      => 'initial_delay',
            'min'     => 1,
            'max'     => 60,
            'tooltip' => __( 'Wait time before the first popup triggers after page load.', 'wc-lightweight-social-proof' ),
            'desc'    => __( 'Controls how many seconds to wait after DOM ready before presenting the first buyer alert.', 'wc-lightweight-social-proof' ),
        ) );

        add_settings_field( 'display_duration', __( 'Display Duration (seconds)', 'wc-lightweight-social-proof' ), array( __CLASS__, 'field_number' ), 'wclsp-settings', 'wclsp_behavior_section', array(
            'id'      => 'display_duration',
            'min'     => 2,
            'max'     => 30,
            'tooltip' => __( 'How long each popup remains on screen before fading out.', 'wc-lightweight-social-proof' ),
            'desc'    => __( 'Recommended: 5 to 8 seconds for comfortable reading.', 'wc-lightweight-social-proof' ),
        ) );

        add_settings_field( 'min_interval', __( 'Minimum Interval (seconds)', 'wc-lightweight-social-proof' ), array( __CLASS__, 'field_number' ), 'wclsp-settings', 'wclsp_behavior_section', array(
            'id'      => 'min_interval',
            'min'     => 5,
            'max'     => 300,
            'tooltip' => __( 'The minimum quiet pause between consecutive notifications.', 'wc-lightweight-social-proof' ),
            'desc'    => __( 'Subsequent popups cycle at randomized times between Min and Max interval to keep notifications feeling organic.', 'wc-lightweight-social-proof' ),
        ) );

        add_settings_field( 'max_interval', __( 'Maximum Interval (seconds)', 'wc-lightweight-social-proof' ), array( __CLASS__, 'field_number' ), 'wclsp-settings', 'wclsp_behavior_section', array(
            'id'      => 'max_interval',
            'min'     => 5,
            'max'     => 300,
            'tooltip' => __( 'The maximum delay between subsequent popups.', 'wc-lightweight-social-proof' ),
            'desc'    => __( 'Must be greater than or equal to Minimum Interval.', 'wc-lightweight-social-proof' ),
        ) );

        // Query & Cache Section
        add_settings_section(
            'wclsp_query_section',
            __( 'Query & Cache Settings', 'wc-lightweight-social-proof' ),
            '__return_empty_string',
            'wclsp-settings'
        );

        add_settings_field( 'order_hours', __( 'Order History Scope (hours)', 'wc-lightweight-social-proof' ), array( __CLASS__, 'field_number' ), 'wclsp-settings', 'wclsp_query_section', array(
            'id'      => 'order_hours',
            'min'     => 1,
            'max'     => 720,
            'tooltip' => __( 'How far back into your order history the engine queries.', 'wc-lightweight-social-proof' ),
            'desc'    => __( 'e.g., 48 fetches orders from the last 2 days. 168 fetches the last 7 days.', 'wc-lightweight-social-proof' ),
        ) );

        add_settings_field( 'cache_minutes', __( 'Transient Cache Lifetime (minutes)', 'wc-lightweight-social-proof' ), array( __CLASS__, 'field_number' ), 'wclsp-settings', 'wclsp_query_section', array(
            'id'      => 'cache_minutes',
            'min'     => 1,
            'max'     => 120,
            'tooltip' => __( 'Duration order queries are cached in WordPress transient memory.', 'wc-lightweight-social-proof' ),
            'desc'    => __( 'Caches recent orders to avoid repetitive database reads. Saving settings flushes this cache automatically.', 'wc-lightweight-social-proof' ),
        ) );
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

    public static function render_tooltip( $tooltip_text ) {
        if ( empty( $tooltip_text ) ) {
            return;
        }
        if ( function_exists( 'wc_help_tip' ) ) {
            echo wc_help_tip( $tooltip_text );
        } else {
            echo ' <span class="dashicons dashicons-editor-help" title="' . esc_attr( $tooltip_text ) . '" style="cursor:help; vertical-align:middle; color:#646970;"></span>';
        }
    }

    public static function field_color( $args ) {
        $opts    = self::get_options();
        $id      = esc_attr( $args['id'] );
        $val     = esc_attr( $opts[ $id ] );
        $tooltip = $args['tooltip'] ?? '';
        $desc    = $args['desc'] ?? '';

        // Color picker element (swatch)
        echo '<input type="color" id="' . $id . '_picker" value="' . $val . '" style="vertical-align:middle; width:46px; height:34px; padding:0; cursor:pointer;" oninput="document.getElementById(\'' . $id . '\').value = this.value.toUpperCase();"> ';

        // Editable text input for direct typing or pasting HEX codes
        echo '<input type="text" id="' . $id . '" name="wclsp_settings[' . $id . ']" value="' . $val . '" maxlength="7" placeholder="#000000" style="width:95px; vertical-align:middle; text-transform:uppercase; font-family:monospace; font-weight:600;" oninput="if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) { document.getElementById(\'' . $id . '_picker\').value = this.value; }">';

        self::render_tooltip( $tooltip );
        if ( ! empty( $desc ) ) {
            echo '<p class="description" style="margin-top:4px; font-size:12px; color:#646970;">' . esc_html( $desc ) . '</p>';
        }
    }

    public static function field_text( $args ) {
        $opts    = self::get_options();
        $id      = $args['id'];
        $val     = esc_attr( $opts[ $id ] );
        $tooltip = $args['tooltip'] ?? '';
        $desc    = $args['desc'] ?? '';

        echo '<input type="text" name="wclsp_settings[' . esc_attr( $id ) . ']" value="' . $val . '" class="regular-text" style="vertical-align:middle;">';
        self::render_tooltip( $tooltip );
        if ( ! empty( $desc ) ) {
            echo '<p class="description" style="margin-top:4px; font-size:12px; color:#646970;">' . esc_html( $desc ) . '</p>';
        }
    }

    public static function field_number( $args ) {
        $opts    = self::get_options();
        $id      = $args['id'];
        $val     = esc_attr( $opts[ $id ] );
        $min     = isset( $args['min'] ) ? ' min="' . intval( $args['min'] ) . '"' : '';
        $max     = isset( $args['max'] ) ? ' max="' . intval( $args['max'] ) . '"' : '';
        $tooltip = $args['tooltip'] ?? '';
        $desc    = $args['desc'] ?? '';

        echo '<input type="number" name="wclsp_settings[' . esc_attr( $id ) . ']" value="' . $val . '" class="small-text" style="vertical-align:middle;"' . $min . $max . '>';
        self::render_tooltip( $tooltip );
        if ( ! empty( $desc ) ) {
            echo '<p class="description" style="margin-top:4px; font-size:12px; color:#646970;">' . esc_html( $desc ) . '</p>';
        }
    }

    public static function render_settings_page() {
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'WooCommerce Lightweight Social Proof Settings', 'wc-lightweight-social-proof' ); ?></h1>
            <p class="description"><?php esc_html_e( 'Configure visual styling, interval animations, and transient cache parameters for recent order alerts.', 'wc-lightweight-social-proof' ); ?></p>
            <form action="options.php" method="post" style="margin-top: 15px;">
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
