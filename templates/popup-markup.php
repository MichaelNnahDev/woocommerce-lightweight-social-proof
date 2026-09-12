<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div id="wclsp-sales-popup" class="wclsp-sales-popup" aria-live="polite" role="region">
    <div class="wclsp-popup-inner">
        <button type="button" class="wclsp-close-btn" aria-label="<?php esc_attr_e( 'Close notification', 'lightweight-sales-popup-for-woo' ); ?>">&times;</button>
        <div class="wclsp-prod-img">
            <img id="wclsp-popup-img" src="" alt="<?php esc_attr_e( 'Product', 'lightweight-sales-popup-for-woo' ); ?>">
        </div>
        <div class="wclsp-popup-text">
            <p class="wclsp-buyer-line">
                <span id="wclsp-buyer-name" class="wclsp-accent-text"></span>
                <span id="wclsp-country-badge" class="wclsp-country-tag"></span>
                <span id="wclsp-location-phrase"></span>
                <span class="wclsp-action-text"><?php esc_html_e( 'just bought', 'lightweight-sales-popup-for-woo' ); ?></span>
            </p>
            <p class="wclsp-purchased-line">
                <a href="#" id="wclsp-prod-link" class="wclsp-accent-text"></a>
            </p>
            <div class="wclsp-meta-line">
                <span id="wclsp-time-ago"></span>
                <span class="wclsp-verified-badge">&#10003; <?php esc_html_e( 'Verified', 'lightweight-sales-popup-for-woo' ); ?></span>
            </div>
        </div>
    </div>
</div>
