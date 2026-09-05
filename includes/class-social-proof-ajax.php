<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WCLSP_Social_Proof_Ajax {

    public static function init() {
        add_action( 'wp_ajax_wclsp_get_recent_sales', array( __CLASS__, 'get_sales_ajax' ) );
        add_action( 'wp_ajax_nopriv_wclsp_get_recent_sales', array( __CLASS__, 'get_sales_ajax' ) );
    }

    public static function get_sales_ajax() {
        check_ajax_referer( 'wclsp_sales_nonce', 'nonce' );

        $data = self::fetch_recent_orders();
        wp_send_json_success( $data );
    }

    public static function fetch_recent_orders() {
        $cached_data = get_transient( 'wclsp_social_proof_cache' );
        if ( false !== $cached_data ) {
            return $cached_data;
        }

        $options     = WCLSP_Social_Proof_Admin::get_options();
        $order_hours = intval($options['order_hours'] );
        $cache_mins  = intval($options['cache_minutes'] );

        $args = array(
            'limit'        => 30,
            'status'       => array( 'completed', 'on-hold', 'processing' ),
            'orderby'      => 'date',
            'order'        => 'DESC',
            'date_created' => '>=' . gmdate( 'Y-m-d H:i:s', strtotime( "-{$order_hours} hours" ) ),
        );

        $orders     = wc_get_orders( $args );$sales_data = array();

        if ( ! empty( $orders ) ) {
            foreach ( $orders as$order ) {
                $first_name =$order->get_billing_first_name();
                $city       =$order->get_billing_city();
                $order_date =$order->get_date_created();

                if ( ! $order_date ) {
                    continue;
                }

                $items =$order->get_items();
                if ( empty( $items ) ) {
                    continue;
                }

                $item    = reset($items );
                $product =$item->get_product();
                if ( ! $product ) {
                    continue;
                }

                $image_id  = $product->get_image_id();$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'thumbnail' ) : wc_placeholder_img_src();

                if ( ! empty( $first_name ) && ! empty( $city ) ) {$sales_data[] = array(
                        'name'     => esc_html( ucfirst( sanitize_text_field( $first_name ) ) ),
                        'location' => esc_html( ucwords( sanitize_text_field( $city ) ) ),
                        'product'  => esc_html( $product->get_name() ),
                        'image'    => esc_url( $image_url ),
                        'time'     => human_time_diff( $order_date->getTimestamp(), time() ) . ' ago',
                    );
                }
            }
        }

        set_transient( 'wclsp_social_proof_cache', $sales_data,$cache_mins * MINUTE_IN_SECONDS );

        return $sales_data;
    }
}
