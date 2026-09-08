/**
 * WooCommerce Lightweight Social Proof - Frontend JS
 */
document.addEventListener('DOMContentLoaded', function () {
    if (typeof wclspData === 'undefined') {
        return;
    }

    const popup = document.getElementById('wclsp-sales-popup');
    if (!popup) {
        return;
    }

    const imgEl        = document.getElementById('wclsp-popup-img');
    const buyerName    = document.getElementById('wclsp-buyer-name');
    const countryBadge = document.getElementById('wclsp-country-badge');
    const locPhrase    = document.getElementById('wclsp-location-phrase');
    const prodLink     = document.getElementById('wclsp-prod-link');
    const timeAgo      = document.getElementById('wclsp-time-ago');
    const closeBtn     = document.querySelector('.wclsp-close-btn');

    let salesData = [];
    let currentIndex = 0;
    let timer = null;

    if (closeBtn) {
        closeBtn.addEventListener('click', function () {
            popup.classList.remove('wclsp-show');
            if (timer) clearTimeout(timer);
        });
    }

    function renderItem(item) {
        if (!item || !popup) return;

        // Image
        if (imgEl && item.image) {
            imgEl.src = item.image;
        }

        // Buyer Name (e.g. Fatima F.)
        if (buyerName) {
            buyerName.textContent = item.buyer_name || 'Someone';
        }

        // Country shortcode / flag (e.g. NG)
        if (countryBadge) {
            if (item.country_code) {
                countryBadge.textContent = (item.country_flag ? item.country_flag + ' ' : '') + item.country_code;
                countryBadge.style.display = 'inline-block';
            } else {
                countryBadge.style.display = 'none';
            }
        }

        // City Phrase (e.g. from Lagos)
        if (locPhrase) {
            if (item.city) {
                locPhrase.innerHTML = ' <em>from ' + item.city + '</em> ';
            } else {
                locPhrase.innerHTML = ' ';
            }
        }

        // Product smart title & link
        if (prodLink) {
            prodLink.textContent = item.product_title || '';
            prodLink.href = item.product_url || '#';
        }

        // Time ago
        if (timeAgo) {
            timeAgo.textContent = item.time_ago || 'Just now';
        }

        // Trigger smooth slide-in
        popup.classList.add('wclsp-show');

        // Hide after display duration
        setTimeout(function () {
            popup.classList.remove('wclsp-show');
            scheduleNext();
        }, parseInt(wclspData.displayDuration, 10) || 6000);
    }

    function scheduleNext() {
        if (!salesData.length) return;

        const min = parseInt(wclspData.minInterval, 10) || 15000;
        const max = parseInt(wclspData.maxInterval, 10) || 30000;
        const interval = Math.floor(Math.random() * (max - min + 1)) + min;

        timer = setTimeout(function () {
            currentIndex = (currentIndex + 1) % salesData.length;
            renderItem(salesData[currentIndex]);
        }, interval);
    }

    // Fetch orders
    fetch(wclspData.ajaxUrl + '?action=wclsp_get_recent_sales&nonce=' + wclspData.nonce)
        .then(function (res) { return res.json(); })
        .then(function (res) {
            if (res && res.success && Array.isArray(res.data) && res.data.length > 0) {
                salesData = res.data;
                const delay = parseInt(wclspData.initialDelay, 10) || 6000;
                setTimeout(function () {
                    renderItem(salesData[0]);
                }, delay);
            }
        })
        .catch(function (err) {
            console.error('WCLSP Error:', err);
        });
});
