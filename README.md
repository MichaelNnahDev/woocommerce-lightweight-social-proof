# WooCommerce Lightweight Social Proof Engine

A lightweight, zero-dependency sales notification popup plugin for WooCommerce stores. It displays real, recent orders with accurate relative timestamps while using transient caching to prevent database strain.

## Key Features

- **UTC Timezone Accuracy:** Matches WooCommerce order creation timestamps directly against UTC `time()` to eliminate negative or skewed "minutes ago" calculations across any server timezone.
- **Low Database Overhead:** Employs a 5-minute WordPress transient cache (`wclsp_social_proof_cache`) so repetitive page loads do not hammer the database with repeated queries.
- **Conditional Asset Loading:** Styles and scripts are only queued on targeted storefront pages (`is_front_page()`, `is_shop()`, `is_product_taxonomy()`).
- **Security Hardened:** All AJAX requests are nonce-protected (`check_ajax_referer`) and buyer display fields are sanitized and escaped before rendering.
- **Modern Glassmorphic UI:** Sleek, responsive popup UI designed with onyx glassmorphism and subtle gold accent highlights.

## Repository Structure

```text
woocommerce-lightweight-social-proof/
├── assets/
│   ├── css/
│   │   └── social-proof.css
│   └── js/
│       └── social-proof.js
├── includes/
│   └── class-social-proof-ajax.php
├── templates/
│   └── popup-markup.php
├── .gitignore
├── LICENSE
├── README.md
└── woocommerce-lightweight-social-proof.php
```

## Installation

### Method 1: Git Clone (Development)
Navigate to your WordPress plugin directory and clone the repository:
```bash
cd /path/to/wordpress/wp-content/plugins/
git clone https://github.com/your-username/woocommerce-lightweight-social-proof.git
```

### Method 2: Manual Zip Installation
1. Download or archive this repository as a `.zip` file.
2. In WordPress Admin, navigate to **Plugins > Add New > Upload Plugin**.
3. Select the `.zip` archive and click **Install Now**.
4. Click **Activate Plugin**.

### Method 3: WP-CLI
```bash
wp plugin activate woocommerce-lightweight-social-proof
```

## Configuration & Behavior

- **Trigger Delay:** The initial popup displays 6 seconds after DOM content is loaded.
- **Display Duration:** Each notification remains visible for 6 seconds before fading out.
- **Interval Timing:** Subsequent popups cycle at randomized delays between 15 and 30 seconds to maintain an organic browsing experience.
- **Order Scope:** Queries up to 30 recent orders with `completed`, `on-hold`, or `processing` status within the last 48 hours.
- **Cache Lifetime:** Order payload is cached in WordPress transient memory (`wclsp_social_proof_cache`) for **5 minutes** (`5 * MINUTE_IN_SECONDS`). This keeps timestamps accurate without repetitive SQL queries. Clear it instantly via WP-CLI:
  ```bash
  wp transient delete wclsp_social_proof_cache
  ```
- **Styling & Customization:** Located in `assets/css/social-proof.css`:
  - **Color Scheme:** Deep onyx background (`rgba(21, 21, 21, 0.95)`) paired with metallic gold accents (`#D4AF37`) and emerald verification indicators (`#25D366`).
  - **Visual Effects:** 10px backdrop Gaussian blur with cubic-bezier slide transitions.
  - **Mobile Behavior:** Automatically converts from a pinned bottom-left card on desktop to a centered, full-width bottom sheet (above standard mobile navbars) under 768px.

## License

This project is open-source and licensed under the [GPL-2.0-or-later](LICENSE).
