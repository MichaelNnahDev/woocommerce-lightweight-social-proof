<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WCLSP_Social_Proof_Admin' ) ) :

class WCLSP_Social_Proof_Admin {

    public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'add_settings_page' ) );
        add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
        add_action( 'admin_init', array( __CLASS__, 'handle_dismiss_optin' ) );
    }

    public static function handle_dismiss_optin() {
        if ( isset( $_GET['wclsp_dismiss_optin'] ) && check_admin_referer( 'wclsp_dismiss_optin_nonce' ) ) {
            if ( current_user_can( 'manage_woocommerce' ) ) {
                update_user_meta( get_current_user_id(), 'wclsp_optin_dismissed', 1 );
                wp_safe_redirect( remove_query_arg( array( 'wclsp_dismiss_optin', '_wpnonce' ) ) );
                exit;
            }
        }
    }

    public static function get_defaults() {
        return array(
            'bg_color'           => '#151515',
            'text_color'         => '#ffffff',
            'accent_color'       => '#D4AF37',
            'badge_color'        => '#25D366',
            'font_family'        => 'inherit',
            'position_desktop'   => 'bottom-left',
            'bottom_offset_desk' => 24,
            'bottom_offset_mob'  => 75,
            'mobile_scale'       => 90,
            'initial_delay'      => 6,
            'display_duration'   => 6,
            'min_interval'       => 15,
            'max_interval'       => 30,
            'order_hours'        => 48,
            'order_statuses'     => array( 'wc-on-hold' ),
            'cache_minutes'      => 5,
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

        // 1. Visual & Styling
        add_settings_section( 'wclsp_style_section', __( 'Visual & Styling', 'wc-lightweight-social-proof' ), '__return_empty_string', 'wclsp-settings' );
        add_settings_field( 'bg_color', __( 'Background Color', 'wc-lightweight-social-proof' ), array( __CLASS__, 'field_color' ), 'wclsp-settings', 'wclsp_style_section', array( 'id' => 'bg_color', 'desc' => 'Default: #151515' ) );
        add_settings_field( 'text_color', __( 'Text Color', 'wc-lightweight-social-proof' ), array( __CLASS__, 'field_color' ), 'wclsp-settings', 'wclsp_style_section', array( 'id' => 'text_color', 'desc' => 'Default: #ffffff' ) );
        add_settings_field( 'accent_color', __( 'Accent / Highlight Color', 'wc-lightweight-social-proof' ), array( __CLASS__, 'field_color' ), 'wclsp-settings', 'wclsp_style_section', array( 'id' => 'accent_color', 'desc' => 'Default: #D4AF37 (Metallic Gold)' ) );
        add_settings_field( 'badge_color', __( 'Verified Badge Color', 'wc-lightweight-social-proof' ), array( __CLASS__, 'field_color' ), 'wclsp-settings', 'wclsp_style_section', array( 'id' => 'badge_color', 'desc' => 'Default: #25D366' ) );
        add_settings_field( 'font_family', __( 'Font Family (CSS)', 'wc-lightweight-social-proof' ), array( __CLASS__, 'field_text' ), 'wclsp-settings', 'wclsp_style_section', array( 'id' => 'font_family', 'desc' => 'e.g. "Mulish", sans-serif or "inherit"' ) );

        // 2. Position & Layout
        add_settings_section( 'wclsp_position_section', __( 'Position & Layout', 'wc-lightweight-social-proof' ), '__return_empty_string', 'wclsp-settings' );
        add_settings_field( 'position_desktop', __( 'Desktop Position', 'wc-lightweight-social-proof' ), array( __CLASS__, 'field_select' ), 'wclsp-settings', 'wclsp_position_section', array(
            'id'      => 'position_desktop',
            'options' => array(
                'bottom-left'  => __( 'Bottom Left', 'wc-lightweight-social-proof' ),
                'bottom-right' => __( 'Bottom Right', 'wc-lightweight-social-proof' ),
            ),
        ) );
        add_settings_field( 'bottom_offset_desk', __( 'Desktop Bottom Offset (px)', 'wc-lightweight-social-proof' ), array( __CLASS__, 'field_number' ), 'wclsp-settings', 'wclsp_position_section', array( 'id' => 'bottom_offset_desk', 'min' => 0, 'max' => 300 ) );
        add_settings_field( 'bottom_offset_mob', __( 'Mobile Bottom Offset (px)', 'wc-lightweight-social-proof' ), array( __CLASS__, 'field_number' ), 'wclsp-settings', 'wclsp_position_section', array( 'id' => 'bottom_offset_mob', 'min' => 0, 'max' => 300 ) );
        add_settings_field( 'mobile_scale', __( 'Mobile Size Scale (%)', 'wc-lightweight-social-proof' ), array( __CLASS__, 'field_number' ), 'wclsp-settings', 'wclsp_position_section', array( 'id' => 'mobile_scale', 'min' => 60, 'max' => 110 ) );

        // 3. Timing & Behavior
        add_settings_section( 'wclsp_behavior_section', __( 'Timing & Behavior', 'wc-lightweight-social-proof' ), '__return_empty_string', 'wclsp-settings' );
        add_settings_field( 'initial_delay', __( 'Initial Delay (seconds)', 'wc-lightweight-social-proof' ), array( __CLASS__, 'field_number' ), 'wclsp-settings', 'wclsp_behavior_section', array( 'id' => 'initial_delay', 'min' => 1, 'max' => 60 ) );
        add_settings_field( 'display_duration', __( 'Display Duration (seconds)', 'wc-lightweight-social-proof' ), array( __CLASS__, 'field_number' ), 'wclsp-settings', 'wclsp_behavior_section', array( 'id' => 'display_duration', 'min' => 2, 'max' => 30 ) );
        add_settings_field( 'min_interval', __( 'Minimum Interval (seconds)', 'wc-lightweight-social-proof' ), array( __CLASS__, 'field_number' ), 'wclsp-settings', 'wclsp_behavior_section', array( 'id' => 'min_interval', 'min' => 5, 'max' => 300 ) );
        add_settings_field( 'max_interval', __( 'Maximum Interval (seconds)', 'wc-lightweight-social-proof' ), array( __CLASS__, 'field_number' ), 'wclsp-settings', 'wclsp_behavior_section', array( 'id' => 'max_interval', 'min' => 5, 'max' => 300 ) );

        // 4. Query & Cache
        add_settings_section( 'wclsp_query_section', __( 'Query & Cache Settings', 'wc-lightweight-social-proof' ), '__return_empty_string', 'wclsp-settings' );
        add_settings_field( 'order_hours', __( 'Order History Scope (hours)', 'wc-lightweight-social-proof' ), array( __CLASS__, 'field_number' ), 'wclsp-settings', 'wclsp_query_section', array( 'id' => 'order_hours', 'min' => 1, 'max' => 720 ) );
        add_settings_field( 'order_statuses', __( 'Included Order Statuses', 'wc-lightweight-social-proof' ), array( __CLASS__, 'field_statuses' ), 'wclsp-settings', 'wclsp_query_section', array( 'id' => 'order_statuses' ) );
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

        $sanitized['bg_color']           = $clean_hex( $input['bg_color'] ?? '', $defaults['bg_color'] );
        $sanitized['text_color']         = $clean_hex( $input['text_color'] ?? '', $defaults['text_color'] );
        $sanitized['accent_color']       = $clean_hex( $input['accent_color'] ?? '', $defaults['accent_color'] );
        $badge_color                     = $input['badge_color'] ?? $defaults['badge_color'];
        $sanitized['badge_color']        = $clean_hex( $badge_color, $defaults['badge_color'] );
        $sanitized['font_family']        = sanitize_text_field( $input['font_family'] ?? $defaults['font_family'] );

        $valid_positions                 = array( 'bottom-left', 'bottom-right' );
        $sanitized['position_desktop']   = in_array( $input['position_desktop'] ?? '', $valid_positions, true ) ? $input['position_desktop'] : $defaults['position_desktop'];
        $sanitized['bottom_offset_desk'] = absint( $input['bottom_offset_desk'] ?? $defaults['bottom_offset_desk'] );
        $sanitized['bottom_offset_mob']  = absint( $input['bottom_offset_mob'] ?? $defaults['bottom_offset_mob'] );
        $sanitized['mobile_scale']       = absint( $input['mobile_scale'] ?? $defaults['mobile_scale'] );

        $sanitized['initial_delay']      = absint( $input['initial_delay'] ?? $defaults['initial_delay'] );
        $sanitized['display_duration']   = absint( $input['display_duration'] ?? $defaults['display_duration'] );
        $sanitized['min_interval']       = absint( $input['min_interval'] ?? $defaults['min_interval'] );
        $sanitized['max_interval']       = absint( $input['max_interval'] ?? $defaults['max_interval'] );
        $sanitized['order_hours']        = absint( $input['order_hours'] ?? $defaults['order_hours'] );

        $allowed_statuses  = array( 'wc-on-hold', 'wc-pending', 'wc-processing', 'wc-completed' );
        $selected_statuses = array();
        if ( ! empty( $input['order_statuses'] ) && is_array( $input['order_statuses'] ) ) {
            foreach ( $input['order_statuses'] as $status ) {
                if ( in_array( $status, $allowed_statuses, true ) ) {
                    $selected_statuses[] = sanitize_text_field( $status );
                }
            }
        }
        $sanitized['order_statuses'] = ! empty( $selected_statuses ) ? $selected_statuses : array( 'wc-on-hold' );
        $sanitized['cache_minutes']  = absint( $input['cache_minutes'] ?? $defaults['cache_minutes'] );

        delete_transient( 'wclsp_social_proof_cache' );

        return $sanitized;
    }

    public static function field_color( $args ) {
        $opts = self::get_options();
        $id   = esc_attr( $args['id'] );
        $val  = esc_attr( $opts[ $id ] ?? '#000000' );
        $desc = $args['desc'] ?? '';

        echo '<input type="color" id="' . $id . '_picker" value="' . $val . '" style="vertical-align:middle; width:44px; height:34px; padding:0; cursor:pointer;" oninput="document.getElementById(\'' . $id . '\').value = this.value.toUpperCase();"> ';
        echo '<input type="text" id="' . $id . '" name="wclsp_settings[' . $id . ']" value="' . $val . '" maxlength="7" placeholder="#000000" style="width:95px; vertical-align:middle; text-transform:uppercase; font-family:monospace; font-weight:600;" oninput="if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) { document.getElementById(\'' . $id . '_picker\').value = this.value; }">';

        if ( ! empty( $desc ) ) {
            echo '<p class="description" style="margin-top:4px; font-size:12px; color:#646970;">' . esc_html( $desc ) . '</p>';
        }
    }

    public static function field_select( $args ) {
        $opts    = self::get_options();
        $id      = esc_attr( $args['id'] );
        $val     = esc_attr( $opts[ $id ] ?? 'bottom-left' );
        $options = $args['options'] ?? array();

        echo '<select id="' . $id . '" name="wclsp_settings[' . $id . ']" style="vertical-align:middle;">';
        foreach ( $options as $key => $label ) {
            echo '<option value="' . esc_attr( $key ) . '" ' . selected( $val, $key, false ) . '>' . esc_html( $label ) . '</option>';
        }
        echo '</select>';
    }

    public static function field_text( $args ) {
        $opts = self::get_options();
        $id   = esc_attr( $args['id'] );
        $val  = esc_attr( $opts[ $id ] ?? '' );
        echo '<input type="text" name="wclsp_settings[' . $id . ']" value="' . $val . '" class="regular-text">';
        if ( ! empty( $args['desc'] ) ) {
            echo '<p class="description" style="margin-top:4px; font-size:12px; color:#646970;">' . esc_html( $args['desc'] ) . '</p>';
        }
    }

    public static function field_number( $args ) {
        $opts = self::get_options();
        $id   = esc_attr( $args['id'] );
        $val  = esc_attr( $opts[ $id ] ?? '' );
        $min  = isset( $args['min'] ) ? ' min="' . intval( $args['min'] ) . '"' : '';
        $max  = isset( $args['max'] ) ? ' max="' . intval( $args['max'] ) . '"' : '';
        echo '<input type="number" name="wclsp_settings[' . $id . ']" value="' . $val . '" class="small-text"' . $min . $max . '>';
    }

    public static function field_statuses( $args ) {
        $opts      = self::get_options();
        $selected  = (array) ( $opts['order_statuses'] ?? array( 'wc-on-hold' ) );
        $statuses  = array(
            'wc-on-hold'    => __( 'On hold (Default)', 'wc-lightweight-social-proof' ),
            'wc-pending'    => __( 'Pending payment', 'wc-lightweight-social-proof' ),
            'wc-processing' => __( 'Processing', 'wc-lightweight-social-proof' ),
            'wc-completed'  => __( 'Completed', 'wc-lightweight-social-proof' ),
        );

        echo '<fieldset style="display:flex; flex-direction:column; gap:6px;">';
        foreach ( $statuses as $status_key => $label ) {
            $checked = in_array( $status_key, $selected, true ) ? 'checked' : '';
            echo '<label style="display:inline-flex; align-items:center; gap:8px;">';
            echo '<input type="checkbox" name="wclsp_settings[order_statuses][]" value="' . esc_attr( $status_key ) . '" ' . $checked . '> ';
            echo esc_html( $label );
            echo '</label>';
        }
        echo '<p class="description" style="margin-top:4px; font-size:12px; color:#646970;">Select which order stages are eligible to trigger social proof notifications.</p>';
        echo '</fieldset>';
    }

    public static function render_settings_page() {
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            return;
        }

        $is_dismissed = get_user_meta( get_current_user_id(), 'wclsp_optin_dismissed', true );
        $current_user = wp_get_current_user();
        $site_domain  = wp_parse_url( home_url(), PHP_URL_HOST );
        $dismiss_url  = wp_nonce_url( add_query_arg( 'wclsp_dismiss_optin', '1' ), 'wclsp_dismiss_optin_nonce' );
        ?>
        <div class="wrap" style="max-width: 900px;">
            <h1><?php esc_html_e( 'WooCommerce Sales Popup Settings', 'wc-lightweight-social-proof' ); ?></h1>

            <?php if ( ! $is_dismissed ) : ?>
                <!-- Optional, 100% WordPress.org Compliant Opt-In Card -->
                <div class="wclsp-optin-card" style="margin: 20px 0 25px; padding: 22px 24px; background: #ffffff; border: 1px solid #c7d2fe; border-left: 4px solid #4f46e5; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); position: relative;">
                    
                    <a href="<?php echo esc_url( $dismiss_url ); ?>" title="<?php esc_attr_e( 'Dismiss this notice', 'wc-lightweight-social-proof' ); ?>" style="position: absolute; top: 14px; right: 16px; text-decoration: none; color: #94a3b8; font-size: 18px; font-weight: 700; line-height: 1;">&times;</a>

                    <div style="display: flex; gap: 14px; align-items: flex-start;">
                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; background: #eef2ff; color: #4f46e5; border-radius: 8px; font-size: 20px; flex-shrink: 0;">⚡</span>
                        <div style="flex-grow: 1;">
                            <h2 style="margin: 0 0 6px; font-size: 16px; font-weight: 700; color: #0f172a;">
                                <?php esc_html_e( 'Get Free Styling Presets & Feature Updates', 'wc-lightweight-social-proof' ); ?>
                            </h2>
                            <p style="margin: 0 0 14px; font-size: 13px; line-height: 1.5; color: #475569;">
                                <?php esc_html_e( 'Optional: Join the developer updates list to receive curated CSS styling presets, conversion optimization guides, and early access to new feature releases.', 'wc-lightweight-social-proof' ); ?>
                            </p>

                            <!-- Self-Hosted Endpoint Form -->
                            <form method="POST" action="https://michaelnnah.com/api/wclsp-leads.php" target="_blank" style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">
                                <input type="text" name="name" value="<?php echo esc_attr( $current_user->display_name ); ?>" placeholder="<?php esc_attr_e( 'Your Name', 'wc-lightweight-social-proof' ); ?>" required style="height: 36px; padding: 0 12px; font-size: 13px; border: 1px solid #cbd5e1; border-radius: 6px; width: 170px;">
                                
                                <input type="email" name="email" value="<?php echo esc_attr( $current_user->user_email ); ?>" placeholder="<?php esc_attr_e( 'Your Email', 'wc-lightweight-social-proof' ); ?>" required style="height: 36px; padding: 0 12px; font-size: 13px; border: 1px solid #cbd5e1; border-radius: 6px; width: 220px;">
                                
                                <input type="hidden" name="domain" value="<?php echo esc_attr( $site_domain ); ?>">
                                <input type="hidden" name="plugin_version" value="1.2.0">

                                <button type="submit" class="button button-primary" style="height: 36px; line-height: 34px; padding: 0 16px; background: #4f46e5; border-color: #4f46e5; font-weight: 600;">
                                    <?php esc_html_e( 'Send Free Presets', 'wc-lightweight-social-proof' ); ?>
                                </button>
                                
                                <a href="<?php echo esc_url( $dismiss_url ); ?>" class="button button-secondary" style="height: 36px; line-height: 34px; padding: 0 14px; color: #64748b;">
                                    <?php esc_html_e( 'No thanks, skip', 'wc-lightweight-social-proof' ); ?>
                                </a>
                            </form>
                            
                            <p style="margin: 8px 0 0; font-size: 11px; color: #94a3b8;">
                                <?php esc_html_e( '🔒 We respect your privacy. No spam. You can unsubscribe at any time.', 'wc-lightweight-social-proof' ); ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

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
