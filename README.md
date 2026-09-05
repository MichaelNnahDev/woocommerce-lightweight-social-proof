# WooCommerce Lightweight Social Proof Engine

A lightweight, zero-dependency sales notification popup plugin for WooCommerce stores. It displays real, recent orders with accurate relative timestamps while using transient caching to prevent database strain.

## Key Features

- **Built-in Admin Dashboard:** Fully configurable via **WooCommerce > Social Proof** with color pickers, font controls, and timing adjustments.
- **UTC Timezone Accuracy:** Matches WooCommerce order creation timestamps directly against UTC `time()` to eliminate negative or skewed "minutes ago" calculations across any server timezone.
- **Low Database Overhead:** Employs a customizable WordPress transient cache (`wclsp_social_proof_cache`) so repetitive page loads do not hammer the database.
- **Dynamic CSS Variables:** Styles and colors update instantly without recompiling or loading external bloat.
- **Conditional Asset Loading:** Styles and scripts are only queued on targeted storefront pages (`is_front_page()`, `is_shop()`, `is_product_taxonomy()`).
- **Security Hardened:** All AJAX requests are nonce-protected (`check_ajax_referer`) and buyer display fields are sanitized and escaped before rendering.

## Repository Structure

```text
woocommerce-lightweight-social-proof/
├── assets/
│   ├── css/
│   │   └── social-proof.css
│   └── js/
│       └── social-proof.js
├── includes/
│   ├── class-social-proof-admin.php
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
git clone [https://github.com/your-username/woocommerce-lightweight-social-proof.git](https://github.com/your-username/woocommerce-lightweight-social-proof.git)
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

All settings can be configured in WP Admin under **WooCommerce > Social Proof**:

- **Visual & Styling:**
  - **Background Color:** Default `#151515` (deep onyx).
  - **Text Color:** Default `#ffffff`.
  - **Accent / Highlight Color:** Default `#D4AF37` (gold).
  - **Verified Badge Color:** Default `#25D366` (emerald).
  - **Font Family:** Inherits theme typography or custom fonts (e.g., `'Mulish', sans-serif`).
- **Timing & Behavior:**
  - **Initial Delay:** Seconds before the first popup triggers (default: 6s).
  - **Display Duration:** Seconds each popup stays visible (default: 6s).
  - **Interval Range:** Min and Max delay between subsequent popups (default: 15s to 30s).
- **Query & Cache Settings:**
  - **Order History Scope:** Hours of past order history to include (default: 48h).
  - **Transient Cache Lifetime:** Cache duration in minutes (default: 5m). Saving settings automatically purges stale transients.

## License

This project is open-source and licensed under the [GPL-2.0-or-later](LICENSE).
