/**
 * Notification badge polling.
 *
 * Mem-polling endpoint "unread count" secara berkala dan memperbarui
 * tampilan badge notifikasi (teks + visibilitas) tanpa reload halaman.
 *
 * Cara pakai di Blade — tandai elemen badge dengan `data-notif-badge`
 * dan sertakan endpoint lewat `data-route`:
 *
 *   <span data-notif-badge data-route="{{ route('admin.notifikasi.unread-count') }}"
 *         class="hidden ...">
 *   </span>
 *
 * Endpoint pada `data-route` wajib mengembalikan JSON: { "count": number }.
 * Komponen ini otomatis dipakai untuk semua elemen yang cocok di halaman
 * (admin maupun user), jadi tidak perlu menulis ulang logika polling
 * setiap kali ada badge baru.
 */

const POLL_INTERVAL_MS = 15000;

async function updateBadge(badge) {
    const url = badge.dataset.route;
    if (!url) return;

    try {
        const response = await fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });
        const data = await response.json();
        const count = data.count ?? 0;

        badge.textContent = count > 99 ? '99+' : String(count);
        badge.classList.toggle('hidden', count <= 0);
        badge.classList.toggle('grid', count > 0);
    } catch {
        // Polling gagal (mis. koneksi putus) — badge tetap menampilkan
        // nilai terakhir yang berhasil diambil, tidak perlu mengganggu user.
    }
}

export function initNotificationBadges() {
    const badges = document.querySelectorAll('[data-notif-badge]');

    badges.forEach((badge) => {
        updateBadge(badge);
        setInterval(() => updateBadge(badge), POLL_INTERVAL_MS);
    });
}
