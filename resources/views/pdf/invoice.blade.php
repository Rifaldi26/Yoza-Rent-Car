<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Invoice #{{ str_pad($pemesanan->id, 6, '0', STR_PAD_LEFT) }}</title>
<style>
    @page {
        margin-top: 20mm;
        margin-right: 16mm;
        margin-bottom: 24mm;
        margin-left: 16mm;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: 'DejaVu Sans', sans-serif;
        font-size: 9px;
        color: #1C2536;
        line-height: 1.6;
        background: #fff;
    }

    /* ===== HEADER (sama dengan laporan/pdf.blade.php) ===== */
    .header-body { width: 100%; border-collapse: collapse; }
    .header-body td { border: none; vertical-align: middle; padding: 0; }
    .header-body td.brand-cell { width: auto; }
    .header-body td.invoice-cell { text-align: right; width: 40%; vertical-align: top; }

    @include('pdf.partials.brand-style')

    .invoice-label {
        font-size: 18px;
        font-weight: bold;
        color: #1B3A6B;
        letter-spacing: 2px;
        text-transform: uppercase;
    }
    .invoice-number {
        font-size: 9px;
        color: #6B7280;
        margin-top: 3px;
        font-family: monospace;
    }
    .invoice-date { font-size: 8px; color: #9CA3AF; margin-top: 2px; }

    /* (header-divider, header-doc dipindah ke pdf.partials.brand-style) */

    /* (section-title dipindah ke pdf.partials.brand-style) */

    /* ===== INFO PELANGGAN & ARMADA (gaya summary-table) ===== */
    .info-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    .info-table td {
        width: 50%;
        border: 1px solid #E5E7EB;
        padding: 10px 12px;
        vertical-align: top;
    }
    .info-table td:first-child { border-left: 3px solid #1B3A6B; border-right: none; }
    .info-label {
        font-size: 6.5px;
        text-transform: uppercase;
        color: #9CA3AF;
        letter-spacing: 1.2px;
        font-weight: bold;
        margin-bottom: 5px;
    }
    .info-name { font-size: 11px; font-weight: bold; color: #1B3A6B; }
    .info-sub { font-size: 8px; color: #6B7280; margin-top: 2px; }

    /* ===== DATA TABLE (sama dengan laporan) ===== */
    .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    .data-table thead th {
        background: #1B3A6B;
        color: #fff;
        font-weight: bold;
        padding: 7px 8px;
        text-align: left;
        font-size: 7px;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        border: 1px solid #1B3A6B;
    }
    .data-table thead th:last-child { text-align: right; }
    .data-table tbody td {
        padding: 7px 8px;
        border: 1px solid #E5E7EB;
        font-size: 8.5px;
        vertical-align: top;
    }
    .data-table tbody tr:nth-child(even) td { background: #F8FAFC; }
    .data-table tbody td:last-child { text-align: right; font-weight: bold; }
    .data-table tfoot td {
        background: #F4F6FA;
        border: 1px solid #D1D5DB;
        font-size: 11px;
        font-weight: bold;
        padding: 9px 8px;
        color: #1B3A6B;
    }
    .data-table tfoot td:last-child { text-align: right; color: #C8A84B; font-size: 11px; }
    .text-muted { color: #9CA3AF; font-size: 7.5px; }
    .font-mono { font-family: monospace; }

    /* (badge & variannya dipindah ke pdf.partials.brand-style) */

    /* ===== INFO PEMBAYARAN (gaya summary-table) ===== */
    .payment-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    .payment-table td {
        border: 1px solid #E5E7EB;
        padding: 9px 8px;
        text-align: center;
        vertical-align: middle;
    }
    .payment-table td:first-child { border-left: 3px solid #1B3A6B; }

    /* ===== CATATAN ===== */
    .note-box {
        margin-top: 14px;
        background: #FFF7ED;
        border-left: 3px solid #B45309;
        padding: 8px 14px;
    }
    .note-box .note-title {
        font-size: 6.5px;
        font-weight: bold;
        color: #B45309;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 3px;
    }
    .note-box p { font-size: 8.5px; color: #1C2536; margin: 0; }

    /* ===== QR / REFERENSI ===== */
    .ref-table { width: 100%; border-collapse: collapse; margin-top: 14px; }
    .ref-table td { border: none; vertical-align: top; padding: 0; }
    .ref-table td.ref-cell { text-align: right; width: 30%; }
    .qr-area {
        width: 44px; height: 44px;
        border: 1.5px solid #1B3A6B;
        display: inline-block;
        text-align: center;
        line-height: 1.3;
        padding-top: 8px;
        font-size: 6px;
        color: #1B3A6B;
        font-weight: bold;
    }
    .ref-caption { font-size: 7px; color: #9CA3AF; margin-top: 4px; }

    /* ===== WATERMARK ===== */
    .watermark {
        position: fixed;
        top: 50%; left: 50%;
        margin-left: -250px;
        margin-top: -60px;
        width: 500px;
        font-size: 70px;
        font-weight: bold;
        color: rgba(185, 28, 28, 0.08);
        text-align: center;
        text-transform: uppercase;
        letter-spacing: 4px;
        z-index: 0;
    }

    /* (footer dipindah ke pdf.partials.brand-style) */

    /* ===== Jaring pengaman jarak halaman ===== */
    .page { padding: 4mm 2mm; }
</style>
</head>
<body>

@if($pemesanan->status === 'dibatalkan')
<div class="watermark">Dibatalkan</div>
@endif

<div class="page">

    {{-- ============================================================ --}}
    {{-- HEADER                                                       --}}
    {{-- ============================================================ --}}
    <table class="header-body">
        <tr>
            <td class="brand-cell">
                <div class="header-monogram">YR</div>
                <div class="header-brand-inline">
                    <h1>Yoza Rent Car</h1>
                    <p class="tagline">Rental Mobil Terpercaya</p>
                </div>
            </td>
            <td class="invoice-cell">
                <div class="invoice-label">Invoice</div>
                <div class="invoice-number">#{{ str_pad($pemesanan->id, 6, '0', STR_PAD_LEFT) }}</div>
                <div class="invoice-date">{{ $pemesanan->created_at->format('d/m/Y H:i') }} WIB</div>
            </td>
        </tr>
    </table>

    <hr class="header-divider">

    <div class="header-doc">
        <h2>Invoice Pemesanan Sewa Mobil</h2>
        <p>Diterbitkan secara otomatis oleh sistem Yoza Rent Car pada {{ now()->format('d/m/Y H:i') }} WIB</p>
    </div>

    {{-- ============================================================ --}}
    {{-- INFO PELANGGAN & ARMADA                                      --}}
    {{-- ============================================================ --}}
    <div class="section-title">Informasi Pemesanan</div>
    <table class="info-table">
        <tr>
            <td>
                <div class="info-label">Ditagihkan Kepada</div>
                <div class="info-name">{{ $pemesanan->user->name }}</div>
                <div class="info-sub">{{ $pemesanan->user->email }}</div>
                @if($pemesanan->no_hp)
                    <div class="info-sub">{{ $pemesanan->no_hp }}</div>
                @elseif($pemesanan->user->no_hp)
                    <div class="info-sub">{{ $pemesanan->user->no_hp }}</div>
                @endif
            </td>
            <td>
                <div class="info-label">Detail Armada</div>
                <div class="info-name">{{ $pemesanan->mobil->nama }}</div>
                <div class="info-sub">{{ $pemesanan->mobil->merek }} &bull; {{ $pemesanan->mobil->tahun }}</div>
                <div class="info-sub font-mono">{{ $pemesanan->mobil->plat_nomor }}</div>
            </td>
        </tr>
    </table>

    {{-- ============================================================ --}}
    {{-- RINCIAN BIAYA                                                --}}
    {{-- ============================================================ --}}
    <div class="section-title">Rincian Biaya</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:28%;">Deskripsi</th>
                <th style="width:20%;">Periode</th>
                <th style="width:10%;" class="text-center">Durasi</th>
                <th style="width:20%;">Harga Satuan</th>
                <th style="width:22%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>Sewa {{ $pemesanan->mobil->nama }}</strong><br>
                    <span class="text-muted">Self-Drive</span>
                </td>
                <td class="text-muted" style="vertical-align:top;">
                    {{ $pemesanan->tanggal_mulai->format('d/m/Y') }}<br>
                    s/d {{ $pemesanan->tanggal_selesai->format('d/m/Y') }}
                </td>
                <td>{{ $pemesanan->durasi() }} hari</td>
                <td>Rp {{ number_format($pemesanan->mobil->harga_per_hari, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($pemesanan->durasi() * $pemesanan->mobil->harga_per_hari, 0, ',', '.') }}</td>
            </tr>

            @if($pemesanan->opsi_supir && $pemesanan->biaya_supir)
            <tr>
                <td>
                    <strong>Jasa Supir</strong><br>
                    <span class="text-muted">Layanan supir profesional</span>
                </td>
                <td class="text-muted" style="vertical-align:top;">
                    {{ $pemesanan->tanggal_mulai->format('d/m/Y') }}<br>
                    s/d {{ $pemesanan->tanggal_selesai->format('d/m/Y') }}
                </td>
                <td>{{ $pemesanan->durasi() }} hari</td>
                <td>Rp {{ number_format($pemesanan->mobil->biaya_supir_per_hari, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($pemesanan->biaya_supir, 0, ',', '.') }}</td>
            </tr>
            @endif
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">Total Pembayaran</td>
                <td>Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- ============================================================ --}}
    {{-- INFO PEMBAYARAN                                              --}}
    {{-- ============================================================ --}}
    @if($pemesanan->payment)
    @php
        $payBadge = match($pemesanan->payment->status) {
            'dikonfirmasi'         => 'badge-dikonfirmasi',
            'menunggu_konfirmasi'  => 'badge-menunggu',
            default                => 'badge-pending',
        };
    @endphp
    <div class="section-title">Informasi Pembayaran</div>
    <table class="payment-table">
        <tr>
            <td>
                <div class="info-label">Metode</div>
                <div class="info-sub" style="font-weight:bold; color:#1C2536; margin-top:4px;">
                    {{ $pemesanan->payment->labelMetode() }}
                </div>
            </td>
            <td>
                <div class="info-label">Status</div>
                <div style="margin-top:4px;">
                    <span class="badge {{ $payBadge }}">{{ str_replace('_', ' ', strtoupper($pemesanan->payment->status)) }}</span>
                </div>
            </td>
            @if($pemesanan->payment->paid_at)
            <td>
                <div class="info-label">Dikonfirmasi</div>
                <div class="info-sub" style="font-weight:bold; color:#1C2536; margin-top:4px;">
                    {{ $pemesanan->payment->paid_at->format('d/m/Y H:i') }}
                </div>
            </td>
            @endif
            @if($pemesanan->payment->wa_sent_at)
            <td>
                <div class="info-label">WA Dikirim</div>
                <div class="info-sub" style="font-weight:bold; color:#1C2536; margin-top:4px;">
                    {{ $pemesanan->payment->wa_sent_at->format('d/m/Y H:i') }}
                </div>
            </td>
            @endif
        </tr>
    </table>
    @endif

    {{-- ============================================================ --}}
    {{-- CATATAN                                                      --}}
    {{-- ============================================================ --}}
    @if($pemesanan->catatan)
    <div class="note-box">
        <div class="note-title">Catatan</div>
        <p>{{ $pemesanan->catatan }}</p>
    </div>
    @endif

    {{-- ============================================================ --}}
    {{-- REFERENSI                                                    --}}
    {{-- ============================================================ --}}
    <table class="ref-table">
        <tr>
            <td>
                <div style="font-size:11px; font-weight:bold; color:#1B3A6B;">
                    Terima kasih telah menggunakan Yoza Rent Car!
                </div>
            </td>
            <td class="ref-cell">
                <div class="qr-area">REF<br>#{{ str_pad($pemesanan->id, 6, '0', STR_PAD_LEFT) }}</div>
                <div class="ref-caption">Nomor Referensi</div>
            </td>
        </tr>
    </table>

    {{-- ============================================================ --}}
    {{-- FOOTER                                                       --}}
    {{-- ============================================================ --}}
    <div class="footer">
        <strong>Yoza Rent Car</strong> &mdash; Dokumen ini diterbitkan secara otomatis oleh sistem Yoza Rent Car.<br>
        Jika ada pertanyaan, hubungi kami melalui fitur chat di aplikasi.
        &nbsp;|&nbsp; &copy; {{ date('Y') }} Yoza Rent Car. Semua hak dilindungi.
    </div>

</div>
</body>
</html>