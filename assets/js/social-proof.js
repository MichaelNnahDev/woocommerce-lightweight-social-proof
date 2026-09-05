document.addEventListener('DOMContentLoaded', () => {
    const popup = document.getElementById('wclsp-sales-popup');
    if (!popup || typeof wclspData === 'undefined') return;

    const els = {
        img: document.getElementById('wclsp-popup-img'),
        name: document.getElementById('wclsp-buyer-name'),
        loc: document.getElementById('wclsp-buyer-loc'),
        prod: document.getElementById('wclsp-prod-name'),
        time: document.getElementById('wclsp-time-ago'),
        close: document.querySelector('.wclsp-close-btn')
    };

    let salesData = [];
    let currentIndex = 0;
    let isPaused = false;

    const fetchUrl = `${wclspData.ajaxUrl}?action=wclsp_get_recent_sales&nonce=${wclspData.nonce}`;

    fetch(fetchUrl)
        .then(res => res.json())
        .then(response => {
            if (response.success && response.data.length > 0) {
                salesData = response.data;
                const initialDelay = wclspData.initialDelay || 6000;
                setTimeout(showPopup, initialDelay);
            }
        })
        .catch(err => console.error('WCLSP: Sales notification fetch failed', err));

    function showPopup() {
        if (isPaused || salesData.length === 0) return;

        const sale = salesData[currentIndex];

        els.name.textContent = sale.name;
        els.loc.textContent = sale.location;
        els.prod.textContent = sale.product;
        els.time.textContent = sale.time;

        if (sale.image) {
            els.img.src = sale.image;
            els.img.parentElement.style.display = 'block';
        } else {
            els.img.parentElement.style.display = 'none';
        }

        popup.classList.add('active');

        const duration = wclspData.displayDuration || 6000;
        setTimeout(() => {
            popup.classList.remove('active');
        }, duration);

        currentIndex = (currentIndex + 1) % salesData.length;

        const minInterval = wclspData.minInterval || 15000;
        const maxInterval = wclspData.maxInterval || 30000;
        const nextInterval = Math.floor(Math.random() * (maxInterval - minInterval + 1) + minInterval);

        setTimeout(showPopup, nextInterval);
    }

    if (els.close) {
        els.close.addEventListener('click', () => {
            popup.classList.remove('active');
            isPaused = true;
        });
    }
});
