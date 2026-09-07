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

    const imgEl      = document.getElementById('wclsp-popup-img');
    const buyerName  = document.getElementById('wclsp-buyer-name');
    const buyerLoc   = document.getElementById('wclsp-buyer-loc');
    const prodName   = document.getElementById('wclsp-prod-name');
    const timeAgo    = document.getElementById('wclsp-time-ago');
    const closeBtn   = document.querySelector('.wclsp-close-btn');

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

        if (imgEl && item.image) {
            imgEl.src = item.image;
        }
        if (buyerName && item.buyer_name) {
            buyerName.textContent = item.buyer_name;
        }
        if (buyerLoc) {
            buyerLoc.textContent = item.buyer_location ? ' in ' + item.buyer_location : '';
        }
        if (prodName && item.product_name) {
            prodName.textContent = item.product_name;
        }
        if (timeAgo && item.time_ago) {
            timeAgo.textContent = item.time_ago;
        }

        popup.classList.add('wclsp-show');

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
