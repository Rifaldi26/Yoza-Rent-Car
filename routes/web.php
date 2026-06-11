<?php

use App\Models\Page;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Admin\DashboardController      as AdminDashboard;
use App\Http\Controllers\Admin\MobilController          as AdminMobil;
use App\Http\Controllers\Admin\PemesananController      as AdminPemesanan;
use App\Http\Controllers\Admin\UserController           as AdminUser;
use App\Http\Controllers\Admin\LaporanController        as AdminLaporan;
use App\Http\Controllers\Admin\PembukuanController      as AdminPembukuan;
use App\Http\Controllers\Admin\NotifikasiController     as AdminNotifikasi;
use App\Http\Controllers\Admin\ChatController           as AdminChat;
use App\Http\Controllers\Admin\PageController           as AdminPage;
use App\Http\Controllers\User\DashboardController       as UserDashboard;
use App\Http\Controllers\User\MobilController           as UserMobil;
use App\Http\Controllers\User\PemesananController       as UserPemesanan;
use App\Http\Controllers\User\PaymentController;
use App\Http\Controllers\User\FavoritController;
use App\Http\Controllers\User\NotifikasiController      as UserNotifikasi;
use App\Http\Controllers\User\ChatController            as UserChat;
use App\Http\Controllers\User\ProfilController;
use App\Http\Controllers\Admin\LaporanController;
use Illuminate\Support\Facades\Route;

// ══════════════════════════════════════════════════════════════
// PUBLIC — tidak butuh login
// ══════════════════════════════════════════════════════════════
Route::get('/', [UserMobil::class, 'index'])->name('home');
Route::get('/mobil/{mobil}', [UserMobil::class, 'show'])->name('mobil.show');

// ── Google OAuth ──────────────────────────────────────────────
Route::get('auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');

// ══════════════════════════════════════════════════════════════
// USER — butuh login + email verified
// ══════════════════════════════════════════════════════════════
Route::middleware(['auth', 'verified', 'email.verified.custom'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [UserDashboard::class, 'index'])->name('dashboard');

    // Profil
    Route::get('/profil', [ProfilController::class, 'edit'])->name('profil.edit');
    Route::patch('/profil', [ProfilController::class, 'update'])->name('profil.update');
    Route::delete('/profil', [ProfilController::class, 'destroy'])->name('profil.destroy');

    // ── Mobil (user) ──────────────────────────────────────────
    Route::get('/mobil', [UserMobil::class, 'index'])->name('user.mobil.index');

    // ── Pemesanan ─────────────────────────────────────────────
    // PENTING: route statis sebelum route dinamis {pemesanan}
    Route::get('/pemesanan/create', [UserPemesanan::class, 'create'])->name('pemesanan.create');
    Route::post('/pemesanan', [UserPemesanan::class, 'store'])->name('pemesanan.store');
    Route::get('/pemesanan', [UserPemesanan::class, 'index'])->name('pemesanan.index');
    Route::get('/pemesanan/{pemesanan}', [UserPemesanan::class, 'show'])->name('pemesanan.show');
    Route::patch('/pemesanan/{pemesanan}/batal', [UserPemesanan::class, 'cancel'])->name('pemesanan.cancel');

    // ── Payment ───────────────────────────────────────────────
    Route::get('/pemesanan/{pemesanan}/bayar', [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::post('/pemesanan/{pemesanan}/pilih-metode', [PaymentController::class, 'pilihMetode'])->name('payment.pilih-metode');
    Route::get('/pemesanan/{pemesanan}/setelah-wa', [PaymentController::class, 'setelahWa'])->name('payment.setelah-wa');
    Route::get('/pemesanan/{pemesanan}/invoice', [PaymentController::class, 'invoice'])->name('payment.invoice');

    // ── Favorit ───────────────────────────────────────────────
    Route::get('/favorit', [FavoritController::class, 'index'])->name('favorit.index');
    Route::post('/favorit/{mobil}/toggle', [FavoritController::class, 'toggle'])->name('favorit.toggle');

    // ── Notifikasi ────────────────────────────────────────────
    // Statis sebelum dinamis
    Route::get('/notifikasi/unread-count', [UserNotifikasi::class, 'unreadCount'])->name('notifikasi.unread-count');
    Route::delete('/notifikasi/hapus-semua', [UserNotifikasi::class, 'hapusSemua'])->name('notifikasi.hapus-semua');
    Route::get('/notifikasi', [UserNotifikasi::class, 'index'])->name('notifikasi.index');
    Route::patch('/notifikasi/{notifikasi}/baca', [UserNotifikasi::class, 'baca'])->name('notifikasi.baca');
    Route::delete('/notifikasi/{notifikasi}', [UserNotifikasi::class, 'destroy'])->name('notifikasi.destroy');

    // ── Chat (user) ───────────────────────────────────────────
    // Statis sebelum dinamis
    Route::get('/chat/unread-count', [UserChat::class, 'unreadCount'])->name('chat.unread-count');
    Route::get('/chat', [UserChat::class, 'index'])->name('chat.index');
    Route::get('/chat/{lawan}/pesan', [UserChat::class, 'riwayat'])->name('chat.riwayat');
    Route::post('/chat/{lawan}/kirim', [UserChat::class, 'kirim'])->name('chat.kirim');
});

// ══════════════════════════════════════════════════════════════
// ADMIN — butuh login + role admin
// ══════════════════════════════════════════════════════════════
Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // ── Manajemen Mobil ───────────────────────────────────────
    Route::get('/mobil', [AdminMobil::class, 'index'])->name('mobil.index');
    Route::get('/mobil/create', [AdminMobil::class, 'create'])->name('mobil.create');
    Route::post('/mobil', [AdminMobil::class, 'store'])->name('mobil.store');
    Route::get('/mobil/{mobil}/edit', [AdminMobil::class, 'edit'])->name('mobil.edit');
    Route::put('/mobil/{mobil}', [AdminMobil::class, 'update'])->name('mobil.update');
    Route::delete('/mobil/{mobil}', [AdminMobil::class, 'destroy'])->name('mobil.destroy');
    Route::patch('/mobil/{mobil}/toggle-status', [AdminMobil::class, 'toggleStatus'])->name('mobil.toggle-status');

    // ── Manajemen Pemesanan ───────────────────────────────────
    Route::get('/pemesanan', [AdminPemesanan::class, 'index'])->name('pemesanan.index');
    Route::get('/pemesanan/{pemesanan}', [AdminPemesanan::class, 'show'])->name('pemesanan.show');
    Route::patch('/pemesanan/{pemesanan}/konfirmasi', [AdminPemesanan::class, 'konfirmasi'])->name('pemesanan.konfirmasi');
    Route::patch('/pemesanan/{pemesanan}/tolak', [AdminPemesanan::class, 'tolak'])->name('pemesanan.tolak');
    Route::patch('/pemesanan/{pemesanan}/selesai', [AdminPemesanan::class, 'selesai'])->name('pemesanan.selesai');
    Route::patch('/pemesanan/{pemesanan}/konfirmasi-bayar', [AdminPemesanan::class, 'konfirmasiPembayaran'])->name('pemesanan.konfirmasi-bayar');
    Route::get('/pemesanan/{pemesanan}/invoice', [AdminPemesanan::class, 'invoice'])->name('pemesanan.invoice');

    // ── Manajemen User ────────────────────────────────────────
    Route::get('/user', [AdminUser::class, 'index'])->name('user.index');
    Route::get('/user/{user}', [AdminUser::class, 'show'])->name('user.show');

    // ── Laporan ───────────────────────────────────────────────
    Route::get('/laporan', [AdminLaporan::class, 'index'])->name('laporan.index');
    Route::get('/laporan/chart-data', [AdminLaporan::class, 'chartData'])->name('laporan.chart-data');
    Route::get('/laporan/export-excel', [LaporanController::class, 'exportExcel'])->name('laporan.export-excel');

    // ── Pembukuan ─────────────────────────────────────────────
    // Statis sebelum dinamis
    Route::get('/pembukuan', [AdminPembukuan::class, 'index'])->name('pembukuan.index');
    Route::get('/pembukuan/jurnal', [AdminPembukuan::class, 'jurnal'])->name('pembukuan.jurnal');
    Route::get('/pembukuan/laporan', [AdminPembukuan::class, 'laporan'])->name('pembukuan.laporan');
    Route::get('/pembukuan/export', [AdminPembukuan::class, 'export'])->name('pembukuan.export');
    Route::post('/pembukuan/pengeluaran', [AdminPembukuan::class, 'pengeluaran'])->name('pembukuan.pengeluaran');
    Route::get('/pembukuan/{account}/edit', [AdminPembukuan::class, 'edit'])->name('pembukuan.edit');
    Route::put('/pembukuan/{account}', [AdminPembukuan::class, 'update'])->name('pembukuan.update');

    // ── Notifikasi (admin) ────────────────────────────────────
    Route::get('/notifikasi/unread-count', [AdminNotifikasi::class, 'unreadCount'])->name('notifikasi.unread-count');
    Route::delete('/notifikasi/hapus-semua', [AdminNotifikasi::class, 'hapusSemua'])->name('notifikasi.hapus-semua');
    Route::get('/notifikasi', [AdminNotifikasi::class, 'index'])->name('notifikasi.index');
    Route::patch('/notifikasi/{notifikasi}/baca', [AdminNotifikasi::class, 'baca'])->name('notifikasi.baca');

    // ── Chat (admin) ──────────────────────────────────────────
    Route::get('/chat/unread-count', [AdminChat::class, 'unreadCount'])->name('chat.unread-count');
    Route::get('/chat', [AdminChat::class, 'index'])->name('chat.index');
    Route::get('/chat/{lawan}/pesan', [AdminChat::class, 'riwayat'])->name('chat.riwayat');
    Route::post('/chat/{lawan}/kirim', [AdminChat::class, 'kirim'])->name('chat.kirim');

    // ── Manajemen Halaman (CMS) ───────────────────────────────
    Route::get('/pages', [AdminPage::class, 'index'])->name('pages.index');
    Route::get('/pages/{slug}/edit', [AdminPage::class, 'edit'])->name('pages.edit');
    Route::put('/pages/{slug}', [AdminPage::class, 'update'])->name('pages.update');
});

// Redirect halaman login/register ke home — modal akan terbuka via ?modal=login
Route::get('login',    fn() => redirect('/?modal=login'))->name('login');

// Route untuk Syarat & Ketentuan dan Kebijakan Privasi
Route::get('/syarat-dan-ketentuan', function () {
    $page = Page::where('slug', 'terms')->first() ?? new Page([
        'title'      => 'Syarat dan Ketentuan', 
        'content'    => 'Konten belum tersedia.', 
        'updated_at' => now()]);
    return view('pages.terms', compact('page'));
})->name('terms');

Route::get('/kebijakan-privasi', function () {
    $page = Page::where('slug', 'privacy')->first() ?? new Page([
        'title'      => 'Pemberitahuan Privasi', 
        'content'    => 'Konten belum tersedia.', 
        'updated_at' => now()]);
    return view('pages.privacy', compact('page'));
})->name('privacy');

use App\Http\Controllers\LocaleController;

// Tambahkan route ini:
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])
    ->name('locale.switch')
    ->where('locale', 'id|en');

// ── Breeze auth routes (login, register, dll) ─────────────────
require __DIR__.'/auth.php';

// Menyiapkan Form di Panel Admin