<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
@page {
    size: A4;
    margin: 10mm;
}

* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: 'DejaVu Sans', Arial, sans-serif;
    font-size: 12px;
    color: #18213a;
    background: #fff;
    line-height: 1.5;
}

/* ── Layout ── */

.page {
    padding: 20px 28px;
    max-width: 760px;
}
/* ── Header ── */
.header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
.brand { display: flex; align-items: center; gap: 10px; }
.brand-icon {
    width: 36px; height: 36px;
    background: #3b6fd4;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
}
.brand-icon svg { width: 20px; height: 20px; }
.brand-name { font-size: 18px; font-weight: 700; color: #18213a; }
.brand-sub { font-size: 9px; color: #7a8499; letter-spacing: 2px; text-transform: uppercase; }
.invoice-meta { text-align: right; }
.invoice-label { font-size: 22px; font-weight: 700; color: #3b6fd4; letter-spacing: -0.5px; }
.invoice-number { font-size: 11px; color: #7a8499; margin-top: 2px; font-family: monospace; }
.invoice-date { font-size: 10px; color: #7a8499; margin-top: 2px; }

/* ── Divider ── */
.divider { height: 2px; background: linear-gradient(to right, #3b6fd4, #e5e9f2); margin: 10px 0; border-radius: 2px; }
.divider-thin { height: 1px; background: #e5e9f2; margin: 16px 0; }

/* ── Info Grid ── */
.info-grid { display: flex; gap: 0; margin-bottom: 16px; }
.info-box { flex: 1; padding: 10px 12px; background: #f4f6fb; border-radius: 10px; }
.info-box:first-child { margin-right: 12px; }
.info-box-title { font-size: 9px; font-weight: 700; color: #7a8499; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; }
.info-box-name { font-size: 13px; font-weight: 700; color: #18213a; }
.info-box-sub { font-size: 10px; color: #7a8499; margin-top: 2px; }

/* ── Status Badge ── */
.badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.badge-success { background: #e8f5ee; color: #1e7145; border: 1px solid #b7dfcc; }
.badge-warning { background: #fef9ec; color: #92660a; border: 1px solid #f5dfa0; }
.badge-info    { background: #eef2fb; color: #2e5bb8; border: 1px solid #c5d5f5; }
.badge-danger  { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

/* ── Detail Tabel ── */
table,
.payment-section,
.info-grid,
.footer {
    page-break-inside: avoid;
}

.detail-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
.detail-table thead tr { background: #1E3A5F; }
.detail-table thead th {
    padding: 6px 10px;
    text-align: left;
    font-size: 10px;
    font-weight: 700;
    color: #fff;
    text-transform: uppercase;
    letter-spacing: 0.8px;
}
.detail-table thead th:last-child { text-align: right; }
.detail-table tbody tr:nth-child(even) { background: #f9fafc; }
.detail-table tbody td { padding: 6px 10px; font-size: 11px; color: #18213a; border-bottom: 1px solid #f1f4fa; }
.detail-table tbody td:last-child { text-align: right; font-weight: 600; }
.detail-table tfoot td { padding: 10px 14px; font-size: 11px; }
.detail-table tfoot tr.subtotal td { color: #7a8499; border-top: 1px solid #e5e9f2; }
.detail-table tfoot tr.total-row td {
    font-size: 14px;
    font-weight: 700;
    color: #18213a;
    border-top: 2px solid #3b6fd4;
    padding-top: 12px;
}
.detail-table tfoot tr.total-row td:last-child { color: #3b6fd4; }
.text-right { text-align: right; }
.text-muted { color: #7a8499; }
.font-mono { font-family: monospace; }

/* ── Payment Info ── */
.payment-section { background: #f4f6fb; border-radius: 10px; padding: 10px; margin-bottom: 10px; }
.payment-title { font-size: 10px; font-weight: 700; color: #7a8499; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; }
.payment-grid { display: flex; gap: 0; }
.payment-item { flex: 1; }
.payment-item-label { font-size: 9px; color: #7a8499; text-transform: uppercase; letter-spacing: 0.5px; }
.payment-item-value { font-size: 11px; font-weight: 600; color: #18213a; margin-top: 2px; }

/* ── Watermark (untuk yang dibatalkan) ── */
.watermark {
    position: fixed;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%) rotate(-35deg);
    font-size: 80px;
    font-weight: 900;
    color: rgba(220, 38, 38, 0.08);
    white-space: nowrap;
    pointer-events: none;
    z-index: 0;
    letter-spacing: 4px;
    text-transform: uppercase;
}

/* ── Footer ── */
.footer {
    margin-top: 14px;
    padding-top: 16px;
    border-top: 1px solid #e5e9f2;
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
}
.footer-left { font-size: 8px; color: #aab0bf; line-height: 1.7; }
.footer-right { text-align: right; font-size: 9px; color: #aab0bf; }
.footer-thanks { font-size: 12px; font-weight: 700; color: #3b6fd4; }

/* ── QR placeholder ── */
.qr-area {
    width: 42px; height: 42px;
    border: 1.5px solid #e5e9f2;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    background: #f9fafc;
}
.qr-label { font-size: 8px; color: #aab0bf; text-align: center; }
</style>
</head>
<body>

@if($pemesanan->status === 'dibatalkan')
<div class="watermark">Dibatalkan</div>
@endif

<div class="page">

    {{-- ── Header ──────────────────────────────────────────── --}}
    <div class="header">
        <div class="brand">
            <div class="brand-icon">
                <svg fill="none" viewBox="0 0 24 24" stroke="#fff" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/>
                </svg>
            </div>
            <div>
                <div class="brand-name">Yoza Rent Car</div>
                <div class="brand-sub">Rental Mobil Terpercaya</div>
            </div>
        </div>
    </div>

    <div class="divider"></div>

    {{-- ── Info Pelanggan & Armada ─────────────────────────── --}}
    <div class="info-grid">
        <div class="info-box">
            <div class="info-box-title">Ditagihkan kepada</div>
            <div class="info-box-name">{{ $pemesanan->user->name }}</div>
            <div class="info-box-sub">{{ $pemesanan->user->email }}</div>
            @if($pemesanan->user->no_hp)
                <div class="info-box-sub">{{ $pemesanan->user->no_hp }}</div>
            @endif
        </div>
        <div class="info-box">
            <div class="info-box-title">Detail Pemesanan</div>
            <div class="info-box-name">{{ $pemesanan->mobil->nama }}</div>
            <div class="info-box-sub">{{ $pemesanan->mobil->merek }} &bull; {{ $pemesanan->mobil->tahun }}</div>
            <div class="info-box-sub font-mono">{{ $pemesanan->mobil->plat_nomor }}</div>
        </div>
    </div>

    {{-- ── Rincian Biaya ────────────────────────────────────── --}}
    <table class="detail-table">
        <thead>
            <tr>
                <th>Deskripsi</th>
                <th>Periode</th>
                <th>Durasi</th>
                <th>Harga Satuan</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>Sewa {{ $pemesanan->mobil->nama }}</strong><br>
                    <span class="text-muted" style="font-size:10px;">Self-Drive</span>
                </td>
                <td style="font-size:10px;">
                    {{ $pemesanan->tanggal_mulai->format('d M Y') }}<br>
                    s/d {{ $pemesanan->tanggal_selesai->format('d M Y') }}
                </td>
                <td>{{ $pemesanan->durasi() }} hari</td>
                <td>Rp {{ number_format($pemesanan->mobil->harga_per_hari, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($pemesanan->durasi() * $pemesanan->mobil->harga_per_hari, 0, ',', '.') }}</td>
            </tr>

            @if($pemesanan->opsi_supir && $pemesanan->biaya_supir)
            <tr>
                <td>
                    <strong>Jasa Supir</strong><br>
                    <span class="text-muted" style="font-size:10px;">Layanan supir profesional</span>
                </td>
                <td style="font-size:10px;">
                    {{ $pemesanan->tanggal_mulai->format('d M Y') }}<br>
                    s/d {{ $pemesanan->tanggal_selesai->format('d M Y') }}
                </td>
                <td>{{ $pemesanan->durasi() }} hari</td>
                <td>Rp {{ number_format($pemesanan->mobil->biaya_supir_per_hari, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($pemesanan->biaya_supir, 0, ',', '.') }}</td>
            </tr>
            @endif
        </tbody>
        <tfoot>
            @php
                $biayaSewa  = $pemesanan->durasi() * $pemesanan->mobil->harga_per_hari;
                $biayaSupir = $pemesanan->biaya_supir ?? 0;
            @endphp
            <tr class="total-row">
                <td colspan="4" class="text-right">Total Pembayaran</td>
                <td class="text-right">Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- ── Info Pembayaran ─────────────────────────────────── --}}
    @if($pemesanan->payment)
    <div class="payment-section">
        <div class="payment-title">Informasi Pembayaran</div>
        <div class="payment-grid">
            <div class="payment-item">
                <div class="payment-item-label">Metode</div>
                <div class="payment-item-value">
                    {{ $pemesanan->payment->labelMetode() }}
                </div>
            </div>
            <div class="payment-item">
                <div class="payment-item-label">Status</div>
                <div class="payment-item-value">
                    @if($pemesanan->payment->status === 'dikonfirmasi')
                        <span class="badge badge-success">DIKONFIRMASI</span>
                    @elseif($pemesanan->payment->status === 'menunggu_konfirmasi')
                        <span class="badge badge-warning">MENUNGGU KONFIRMASI</span>
                    @else
                        <span class="badge badge-info">{{ strtoupper($pemesanan->payment->status) }}</span>
                    @endif
                </div>
            </div>
            @if($pemesanan->payment->paid_at)
            <div class="payment-item">
                <div class="payment-item-label">Dikonfirmasi</div>
                <div class="payment-item-value">
                    {{ $pemesanan->payment->paid_at->format('d M Y, H:i') }}
                </div>
            </div>
            @endif
            @if($pemesanan->payment->wa_sent_at)
            <div class="payment-item">
                <div class="payment-item-label">WA Dikirim</div>
                <div class="payment-item-value">
                    {{ $pemesanan->payment->wa_sent_at->format('d M Y, H:i') }}
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- ── Catatan Pelanggan ────────────────────────────────── --}}
    @if($pemesanan->catatan)
    <div style="background:#fffbec; border:1px solid #f5dfa0; border-radius:8px; padding:12px 16px; margin-bottom:20px;">
        <div style="font-size:9px; font-weight:700; color:#92660a; text-transform:uppercase; letter-spacing:1px; margin-bottom:4px;">Catatan</div>
        <div style="font-size:11px; color:#18213a;">{{ $pemesanan->catatan }}</div>
    </div>
    @endif

    {{-- ── Footer ──────────────────────────────────────────── --}}
    <div class="footer">
        <div class="footer-left">
            <div class="footer-thanks">Terima kasih telah menggunakan Yoza Rent Car!</div>
            <div style="margin-top:6px;">
                Dokumen ini diterbitkan secara otomatis oleh sistem Yoza Rent Car.<br>
                Jika ada pertanyaan, hubungi kami melalui fitur chat di aplikasi.<br>
                &copy; {{ date('Y') }} Yoza Rent Car. Semua hak dilindungi.
            </div>
        </div>
        <div class="footer-right">
            <div class="qr-area">
                <div class="qr-label">REF<br>#{{ str_pad($pemesanan->id, 6, '0', STR_PAD_LEFT) }}</div>
            </div>
            <div style="margin-top:6px;">Nomor Referensi</div>
        </div>
    </div>

</div>
</body>
</html>