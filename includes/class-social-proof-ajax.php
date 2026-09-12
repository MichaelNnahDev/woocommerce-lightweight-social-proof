<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WCLSP_Social_Proof_Ajax {

    public static function init() {
        add_action( 'wp_ajax_wclsp_get_recent_sales', array( __CLASS__, 'get_recent_sales' ) );
        add_action( 'wp_ajax_nopriv_wclsp_get_recent_sales', array( __CLASS__, 'get_recent_sales' ) );
    }

    public static function get_recent_sales() {
        check_ajax_referer( 'wclsp_sales_nonce', 'nonce' );

        $options     = class_exists( 'WCLSP_Social_Proof_Admin' ) ? WCLSP_Social_Proof_Admin::get_options() : array();
        $order_hours = intval( $options['order_hours'] ?? 48 );
        $cache_mins  = intval( $options['cache_minutes'] ?? 5 );
        $cache_key   = 'wclsp_social_proof_cache';

        // Retrieve user-chosen statuses or default to on-hold
        $statuses = ! empty( $options['order_statuses'] ) && is_array( $options['order_statuses'] )
            ? $options['order_statuses']
            : array( 'wc-on-hold' );

        $cached_data = get_transient( $cache_key );
        if ( false !== $cached_data && is_array( $cached_data ) && ! empty( $cached_data ) ) {
            wp_send_json_success( $cached_data );
        }

        $lookback_time = gmdate( 'Y-m-d H:i:s', time() - ( $order_hours * HOUR_IN_SECONDS ) );

        // 1. Query within lookback window
        $orders = wc_get_orders( array(
            'limit'        => 15,
            'status'       => $statuses,
            'date_created' => '>=' . $lookback_time,
            'orderby'      => 'date',
            'order'        => 'DESC',
            'return'       => 'objects',
        ) );

        // 2. Fallback to newest orders of selected statuses if none exist within hours
        if ( empty( $orders ) ) {
            $orders = wc_get_orders( array(
                'limit'   => 10,
                'status'  => $statuses,
                'orderby' => 'date',
                'order'   => 'DESC',
                'return'  => 'objects',
            ) );
        }

        $sales_data = array();

        foreach ( $orders as $order ) {
            if ( ! is_a( $order, 'WC_Order' ) ) {
                continue;
            }

            // Buyer Name formatting: "First L."
            $first_name = trim( $order->get_billing_first_name() );
            $last_name  = trim( $order->get_billing_last_name() );

            if ( empty( $first_name ) ) {
                $buyer_name = __( 'Someone', 'lightweight-sales-popup-for-woo' );
            } elseif ( ! empty( $last_name ) ) {
                $buyer_name = $first_name . ' ' . mb_substr( $last_name, 0, 1 ) . '.';
            } else {
                $buyer_name = $first_name;
            }

            // Location
            $city         = trim( $order->get_shipping_city() ?: $order->get_billing_city() );
            $country_code = strtoupper( trim( $order->get_shipping_country() ?: $order->get_billing_country() ) );

            // Items
            $items       = $order->get_items();
            $items_count = count( $items );
            if ( $items_count === 0 ) {
                continue;
            }

            $first_item = reset( $items );
            $product    = $first_item->get_product();
            if ( ! $product ) {
                continue;
            }

            $first_title = $first_item->get_name();
            if ( $items_count > 1 ) {
                $extra_items = $items_count - 1;
                $product_label = sprintf(
                    _n( '%1$s and %2$d other item', '%1$s and %2$d other items', $extra_items, 'lightweight-sales-popup-for-woo' ),
                    $first_title,
                    $extra_items
                );
            } else {
                $product_label = $first_title;
            }

            // Product Image
            $image_id  = $product->get_image_id();
            $image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'thumbnail' ) : wc_placeholder_img_src( 'thumbnail' );

            // Human Time Difference
            $order_date = $order->get_date_created();
            $now        = time();
            $diff       = $order_date ? ( $now - $order_date->getTimestamp() ) : 0;

            if ( $diff < 3600 ) {
                $mins     = max( 1, round( $diff / 60 ) );
                $time_ago = sprintf( _n( '%d minute ago', '%d minutes ago', $mins, 'lightweight-sales-popup-for-woo' ), $mins );
            } elseif ( $diff < 86400 ) {
                $hours    = round( $diff / 3600 );
                $time_ago = sprintf( _n( '%d hour ago', '%d hours ago', $hours, 'lightweight-sales-popup-for-woo' ), $hours );
            } elseif ( $diff < ( 86400 * 7 ) ) {
                $days     = round( $diff / 86400 );
                $time_ago = sprintf( _n( '%d day ago', '%d days ago', $days, 'lightweight-sales-popup-for-woo' ), $days );
            } else {
                $time_ago = __( 'Recently', 'lightweight-sales-popup-for-woo' );
            }

            $sales_data[] = array(
                'id'            => $order->get_id(),
                'buyer_name'    => esc_html( $buyer_name ),
                'city'          => esc_html( $city ),
                'country_code'  => esc_html( $country_code ),
                'product_title' => esc_html( $product_label ),
                'product_url'   => esc_url( $product->get_permalink() ),
                'image'         => esc_url( $image_url ),
                'time_ago'      => esc_html( $time_ago ),
            );
        }

        if ( ! empty( $sales_data ) && $cache_mins > 0 ) {
            set_transient( $cache_key, $sales_data, $cache_mins * MINUTE_IN_SECONDS );
        }

        wp_send_json_success( $sales_data );
    }
}
