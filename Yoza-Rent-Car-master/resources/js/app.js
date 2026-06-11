import './bootstrap';
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// ── Language Toggle ───────────────────────────────────────────
window.setLang = function(lang) {
    document.getElementById('lang-id')?.classList.toggle('bg-blue-600', lang === 'id');
    document.getElementById('lang-id')?.classList.toggle('text-white', lang === 'id');
    document.getElementById('lang-id')?.classList.toggle('text-gray-500', lang !== 'id');
    document.getElementById('lang-en')?.classList.toggle('bg-blue-600', lang === 'en');
    document.getElementById('lang-en')?.classList.toggle('text-white', lang === 'en');
    document.getElementById('lang-en')?.classList.toggle('text-gray-500', lang !== 'en');
    localStorage.setItem('yozarentcar_lang', lang);
    // Kirim ke server jika ada route locale
    // fetch(`/lang/${lang}`).then(() => location.reload());
};

// Restore lang preference
document.addEventListener('DOMContentLoaded', () => {
    const saved = localStorage.getItem('yozarentcar_lang') || 'id';
    window.setLang(saved);
});

// ── Notifikasi Badge Polling ──────────────────────────────────
function pollNotifBadge() {
    const badge = document.getElementById('admin-notif-badge');
    if (!badge) return;

    const route = badge.dataset.route;
    if (!route) return;

    const update = async () => {
        try {
            const res  = await fetch(route, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            const count = data.count || 0;

            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.classList.remove('hidden');
                badge.classList.add('inline-flex');
            } else {
                badge.classList.add('hidden');
                badge.classList.remove('inline-flex');
            }
        } catch (_) {}
    };

    update(); // langsung saat load
    setInterval(update, 15000); // polling tiap 15 detik
}

document.addEventListener('DOMContentLoaded', pollNotifBadge);

// ── Auth Modal ──────────────────────────────────────────────
window.authModal = function() {
    return {
        isOpen: false,
        tab: 'login',

        open(tab = 'login') {
            this.tab = tab;
            this.isOpen = true;
            document.body.style.overflow = 'hidden';
        },

        close() {
            this.isOpen = false;
            document.body.style.overflow = '';
        }
    }
}