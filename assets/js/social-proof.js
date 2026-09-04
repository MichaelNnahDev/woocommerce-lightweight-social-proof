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
                setTimeout(showPopup, 6000);
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

        setTimeout(() => {
            popup.classList.remove('active');
        }, 6000);

        currentIndex = (currentIndex + 1) % salesData.length;

        const nextInterval = Math.floor(Math.random() * (30000 - 15000 + 1) + 15000);
        setTimeout(showPopup, nextInterval);
    }

    if (els.close) {
        els.close.addEventListener('click', () => {
            popup.classList.remove('active');
            isPaused = true;
        });
    }
});
