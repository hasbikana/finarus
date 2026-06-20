
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

Alpine.start();

window.Finarus = {
    csrf() {
        return document.querySelector('meta[name="csrf-token"]')?.content || '';
    },

    async api(url, options = {}) {
        const defaults = {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': this.csrf(),
            },
        };
        const merged = { ...defaults, ...options };
        if (merged.body && typeof merged.body === 'object' && !(merged.body instanceof FormData)) {
            merged.body = JSON.stringify(merged.body);
        }
        return fetch(url, merged);
    },

    toast(message, type = 'success') {
        window.dispatchEvent(new CustomEvent('finarus-toast', {
            detail: { message, type }
        }));
    },

    reload(delay = 500) {
        setTimeout(() => location.reload(), delay);
    },

    formatRupiah(n) {
        return 'Rp ' + Number(n).toLocaleString('id-ID');
    },

    chartColors: {
        blue: 'hsl(221.2, 83.2%, 53.3%)',
        green: 'hsl(142.1, 76.2%, 36.3%)',
        red: 'hsl(0, 84.2%, 60.2%)',
        orange: 'hsl(24.6, 95%, 53.1%)',
        purple: 'hsl(262.1, 83.3%, 57.8%)',
        yellow: 'hsl(47.9, 95.8%, 53.1%)',
    },

    isDarkMode() {
        return document.documentElement.classList.contains('dark');
    },

    onThemeChange(callback) {
        const observer = new MutationObserver(() => callback());
        observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
    },
};
