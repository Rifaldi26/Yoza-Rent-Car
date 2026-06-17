<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Yoza Rent Car {{ $tahun }}</title>
    <style>
        @page {
            margin: 18mm 15mm 22mm;
            footer: html_pageFooter;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            color: #1F2937;
            line-height: 1.55;
        }

        /* ===== HEADER / KOP SURAT ===== */
        .header {
            border-bottom: 3px solid #1E40AF;
            padding-bottom: 14px;
            margin-bottom: 18px;
            position: relative;
        }
        .header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .header-logo {
            width: 70px;
            height: 70px;
            background: #1E40AF;
            border-radius: 50%;
            text-align: center;
            line-height: 70px;
            color: #fff;
            font-size: 28px;
            font-weight: bold;
        }
        .header-title {
            text-align: right;
        }
        .header-title h1 {
            font-size: 20px;
            font-weight: bold;
            color: #1E3A8A;
            margin: 0 0 2px;
            letter-spacing: 1px;
        }
        .header-title .tagline {
            font-size: 10px;
            color: #2563EB;
            margin: 0;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .header-info {
            margin-top: 8px;
            font-size: 8px;
            color: #6B7280;
        }
        .header-info table {
            border: none;
            margin: 0;
        }
        .header-info td {
            border: none;
            padding: 1px 4px;
            font-size: 8px;
        }
        .header-sub {
            margin-top: 10px;
            border-top: 1px solid #DBEAFE;
            padding-top: 8px;
            text-align: center;
        }
        .header-sub h2 {
            font-size: 13px;
            font-weight: bold;
            color: #1E40AF;
            margin: 0;
        }
        .header-sub p {
            font-size: 9px;
            color: #4B5563;
            margin: 2px 0 0;
        }

        /* ===== SECTION TITLE ===== */
        .section-title {
            font-size: 10px;
            font-weight: bold;
            color: #fff;
            background: #1E40AF;
            padding: 5px 10px;
            margin: 16px 0 8px;
            border-radius: 3px;
            letter-spacing: 0.5px;
        }
        .section-title .section-icon {
            margin-right: 6px;
        }

        /* ===== RINGKASAN / SUMMARY CARDS ===== */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .summary-table td {
            width: 25%;
            border: 1px solid #E5E7EB;
            padding: 8px 6px;
            text-align: center;
            background: #FAFBFC;
        }
        .summary-table .sum-icon {
            font-size: 16px;
            margin-bottom: 2px;
        }
        .summary-table .sum-label {
            font-size: 7px;
            text-transform: uppercase;
            color: #9CA3AF;
            letter-spacing: 1px;
            font-weight: bold;
        }
        .summary-table .sum-value {
            font-size: 13px;
            font-weight: bold;
            margin-top: 3px;
        }
        .summary-table .sum-sub {
            font-size: 7px;
            color: #9CA3AF;
            margin-top: 1px;
        }
        .text-green { color: #059669; }
        .text-yellow { color: #D97706; }
        .text-blue { color: #2563EB; }
        .text-red { color: #DC2626; }

        /* ===== DATA TABLE ===== */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .data-table thead th {
            background: #1E40AF;
            color: #fff;
            font-weight: bold;
            padding: 5px 4px;
            text-align: left;
            font-size: 7px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #1E40AF;
        }
        .data-table tbody td {
            padding: 4px 4px;
            border: 1px solid #E5E7EB;
            font-size: 7.5px;
        }
        .data-table tbody tr:nth-child(even) td {
            background: #F9FAFB;
        }
        .data-table tbody tr:hover td {
            background: #EFF6FF;
        }

        /* Status badges */
        .badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 10px;
            font-size: 7px;
            font-weight: bold;
            letter-spacing: 0.3px;
        }
        .badge-selesai { background: #D1FAE5; color: #065F46; }
        .badge-dikonfirmasi { background: #DBEAFE; color: #1E40AF; }
        .badge-menunggu { background: #FEF3C7; color: #92400E; }
        .badge-dibatalkan { background: #FEE2E2; color: #991B1B; }
        .badge-kadaluarsa { background: #F3F4F6; color: #4B5563; }
        .badge-pending { background: #FFF7ED; color: #9A3412; }

        /* ===== CHART BARS ===== */
        .chart-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .chart-table thead th {
            background: #1E40AF;
            color: #fff;
            padding: 5px 6px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #1E40AF;
        }
        .chart-table tbody td {
            padding: 3px 6px;
            border: 1px solid #E5E7EB;
            font-size: 8px;
            vertical-align: middle;
        }
        .chart-table tbody tr:nth-child(even) td {
            background: #F9FAFB;
        }
        .chart-bar-container {
            width: 100%;
            background: #F3F4F6;
            border-radius: 3px;
            overflow: hidden;
            height: 18px;
            position: relative;
        }
        .chart-bar {
            height: 18px;
            border-radius: 3px;
            text-align: right;
            line-height: 18px;
            padding-right: 5px;
            font-size: 7px;
            font-weight: bold;
            color: #fff;
            min-width: 2px;
        }
        .chart-bar.bar-green { background: #059669; }
        .chart-bar.bar-blue { background: #2563EB; }
        .chart-bar.bar-yellow { background: #D97706; }
        .chart-bar.bar-red { background: #DC2626; }
        .chart-bar.bar-gray { background: #6B7280; }
        .chart-bar.bar-purple { background: #7C3AED; }

        /* ===== FOOTER ===== */
        .footer {
            text-align: center;
            font-size: 7px;
            color: #9CA3AF;
            border-top: 1px solid #E5E7EB;
            padding-top: 6px;
            margin-top: 16px;
        }
        .footer strong {
            color: #6B7280;
        }

        /* ===== PAGE NUMBER ===== */
        #pageFooter {
            text-align: center;
            font-size: 7px;
            color: #9CA3AF;
            padding-top: 4px;
        }

        /* ===== UTILITY ===== */
        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .page-break { page-break-before: always; }
        .text-muted { color: #9CA3AF; }
        .mb-0 { margin-bottom: 0; }
        .mt-2 { margin-top: 8px; }
        .nowrap { white-space: nowrap; }
    </style>
</head>
<body>

    {{-- ============================================================ --}}
    {{-- HEADER / KOP SURAT                                          --}}
    {{-- ============================================================ --}}
    <div class="header">
        <div class="header-top">
            <div class="header-logo">YR</div>
            <div class="header-title">
                <h1>YOZA RENT CAR</h1>
                <p class="tagline">Rental Mobil Terpercaya</p>
                <div class="header-info">
                    <table>
                        <tr>
                            <td>📍</td>
                            <td>Jl. Raya No. 123, Kota Bandung, Jawa Barat 40123</td>
                        </tr>
                        <tr>
                            <td>📞</td>
                            <td>(022) 1234-5678 &nbsp;|&nbsp; 📧 info@yozarentcar.co.id</td>
                        </tr>
                        <tr>
                            <td>🌐</td>
                            <td>www.yozarentcar.co.id</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="header-sub">
            <h2>LAPORAN PENJUALAN TAHUN {{ $tahun }}</h2>
            <p>Periode: 1 Januari {{ $tahun }} – 31 Desember {{ $tahun }}</p>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- RINGKASAN / SUMMARY EXECUTIVE                                --}}
    {{-- ============================================================ --}}
    <div class="section-title"><span class="section-icon">📊</span> RINGKASAN EKSEKUTIF</div>
    <table class="summary-table">
        <tr>
            <td>
                <div class="sum-icon">💰</div>
                <div class="sum-label">Total Pendapatan</div>
                <div class="sum-value text-green">Rp {{ number_format($ringkasan['total_pendapatan'], 0, ',', '.') }}</div>
                <div class="sum-sub">Seluruh transaksi {{ $tahun }}</div>
            </td>
            <td>
                <div class="sum-icon">✅</div>
                <div class="sum-label">Pemesanan Selesai</div>
                <div class="sum-value text-yellow">{{ $ringkasan['total_selesai'] }}</div>
                <div class="sum-sub">Transaksi berhasil</div>
            </td>
            <td>
                <div class="sum-icon">⏳</div>
                <div class="sum-label">Pending</div>
                <div class="sum-value text-blue">{{ $ringkasan['total_pending'] }}</div>
                <div class="sum-sub">Menunggu konfirmasi</div>
            </td>
            <td>
                <div class="sum-icon">❌</div>
                <div class="sum-label">Dibatalkan</div>
                <div class="sum-value text-red">{{ $ringkasan['total_dibatalkan'] }}</div>
                <div class="sum-sub">Transaksi batal</div>
            </td>
        </tr>
    </table>

    {{-- ============================================================ --}}
    {{-- DATA PEMESANAN                                               --}}
    {{-- ============================================================ --}}
    <div class="section-title"><span class="section-icon">📋</span> DATA PEMESANAN</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:3%;">#</th>
                <th style="width:12%;">Pelanggan</th>
                <th style="width:11%;">Mobil</th>
                <th style="width:11%;">Periode</th>
                <th style="width:4%;">Hari</th>
                <th style="width:7%;">Supir</th>
                <th style="width:11%;">Total Harga</th>
                <th style="width:7%;">Status</th>
                <th style="width:9%;">Pembayaran</th>
                <th style="width:8%;">Tgl Dibuat</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pemesanans as $p)
            <tr>
                <td class="text-center">{{ $p->id }}</td>
                <td>
                    <div class="font-bold" style="font-size:8px;">{{ $p->user->name }}</div>
                    <div style="color:#6B7280; font-size:6.5px;">{{ $p->user->email }}</div>
                </td>
                <td>{{ $p->mobil->nama }}</td>
                <td class="nowrap">
                    {{ $p->tanggal_mulai->format('d/m/Y') }}<br>
                    <span style="color:#6B7280; font-size:6.5px;">s/d {{ $p->tanggal_selesai->format('d/m/Y') }}</span>
                </td>
                <td class="text-center">{{ $p->durasi() }}</td>
                <td class="text-center">{{ $p->opsi_supir ? 'Dengan' : 'Lepas' }}</td>
                <td class="text-right font-bold">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                <td class="text-center">
                    @php
                        $statusClass = match($p->status) {
                            'selesai' => 'badge-selesai',
                            'dikonfirmasi' => 'badge-dikonfirmasi',
                            'menunggu' => 'badge-menunggu',
                            'dibatalkan' => 'badge-dibatalkan',
                            'kadaluarsa' => 'badge-kadaluarsa',
                            default => 'badge-pending'
                        };
                    @endphp
                    <span class="badge {{ $statusClass }}">{{ ucfirst($p->status) }}</span>
                </td>
                <td class="text-center">{{ $p->payment?->labelMetode() ?? '-' }}</td>
                <td class="text-center nowrap">{{ $p->created_at->format('d/m/Y') }}<br><span style="color:#6B7280; font-size:6.5px;">{{ $p->created_at->format('H:i') }}</span></td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center" style="padding:20px; color:#9CA3AF; font-size:9px;">
                    Tidak ada data pemesanan untuk periode ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ============================================================ --}}
    {{-- GRAFIK PENDAPATAN PER BULAN                                  --}}
    {{-- ============================================================ --}}
    <div class="section-title"><span class="section-icon">📈</span> PENDAPATAN PER BULAN {{ $tahun }}</div>
    @php
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $maxPendapatan = $pendapatan->max() ?: 1;
    @endphp
    <table class="chart-table">
        <thead>
            <tr>
                <th style="width:12%;">Bulan</th>
                <th style="width:65%;">Grafik Pendapatan</th>
                <th style="width:23%;" class="text-right">Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($months as $i => $month)
                @php
                    $val = $pendapatan->get($i + 1, 0);
                    $pct = $maxPendapatan > 0 ? ($val / $maxPendapatan) * 100 : 0;
                    $barClass = ($val > 0 && $val == $maxPendapatan) ? 'bar-green' : 'bar-blue';
                @endphp
            <tr>
                <td class="font-bold">{{ $month }}</td>
                <td>
                    <div class="chart-bar-container">
                        <div class="chart-bar {{ $barClass }}" style="width:{{ $pct }}%;">
                            @if($pct > 20)
                                Rp{{ number_format($val / 1000000, 1) }}jt
                            @endif
                        </div>
                    </div>
                </td>
                <td class="text-right">Rp {{ number_format($val, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr style="font-weight:bold; background:#EFF6FF;">
                <td>TOTAL</td>
                <td></td>
                <td class="text-right" style="color:#1E40AF; font-size:9px;">
                    Rp {{ number_format($pendapatan->sum(), 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

    {{-- ============================================================ --}}
    {{-- DISTRIBUSI STATUS                                            --}}
    {{-- ============================================================ --}}
    <div class="section-title"><span class="section-icon">🎯</span> DISTRIBUSI STATUS PEMESANAN {{ $tahun }}</div>
    @php
        $maxStatus = $statusCounts->max() ?: 1;
        $totalStatus = $statusCounts->sum() ?: 1;
        $statusBarClasses = ['bar-green', 'bar-blue', 'bar-yellow', 'bar-red', 'bar-gray', 'bar-purple'];
    @endphp
    <table class="chart-table">
        <thead>
            <tr>
                <th style="width:15%;">Status</th>
                <th style="width:55%;">Grafik Distribusi</th>
                <th style="width:15%;" class="text-right">Jumlah</th>
                <th style="width:15%;" class="text-right">Persentase</th>
            </tr>
        </thead>
        <tbody>
            @foreach($statusLabels as $i => $label)
                @php
                    $val = $statusCounts[$i];
                    $pct = $maxStatus > 0 ? ($val / $maxStatus) * 100 : 0;
                    $pctTotal = ($val / $totalStatus) * 100;
                    $barClass = $statusBarClasses[$i] ?? 'bar-gray';
                @endphp
            <tr>
                <td class="font-bold">{{ $label }}</td>
                <td>
                    <div class="chart-bar-container">
                        <div class="chart-bar {{ $barClass }}" style="width:{{ $pct }}%;">
                            @if($pct > 20)
                                {{ $val }} pemesanan
                            @endif
                        </div>
                    </div>
                </td>
                <td class="text-right font-bold">{{ $val }}</td>
                <td class="text-right">{{ number_format($pctTotal, 1) }}%</td>
            </tr>
            @endforeach
            <tr style="font-weight:bold; background:#EFF6FF;">
                <td>TOTAL</td>
                <td></td>
                <td class="text-right" style="color:#1E40AF;">{{ $statusCounts->sum() }}</td>
                <td class="text-right" style="color:#1E40AF;">100%</td>
            </tr>
        </tbody>
    </table>

    {{-- ============================================================ --}}
    {{-- FOOTER                                                        --}}
    {{-- ============================================================ --}}
    <div class="footer">
        <p>
            <strong>Yoza Rent Car</strong> — Jl. Raya No. 123, Kota Bandung, Jawa Barat 40123
            &nbsp;|&nbsp; Telp: (022) 1234-5678 &nbsp;|&nbsp; Email: info@yozarentcar.co.id
        </p>
        <p>
            Laporan ini digenerate secara otomatis pada {{ now()->format('d/m/Y H:i') }} WIB
            &nbsp;|&nbsp; Dokumen resmi perusahaan
        </p>
    </div>

</body>
</html>

<htmlpagefooter name="pageFooter">
    <table style="width:100%; border-top:1px solid #E5E7EB; padding-top:3px; font-size:7px; color:#9CA3AF;">
        <tr>
            <td style="text-align:left; width:33%;">
                Yoza Rent Car &copy; {{ $tahun }}
            </td>
            <td style="text-align:center; width:33%;">
                Laporan Penjualan {{ $tahun }}
            </td>
            <td style="text-align:right; width:33%;">
                Halaman {PAGE_NUM} dari {PAGE_COUNT}
            </td>
        </tr>
    </table>
</htmlpagefooter>