<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Yoza Rent Car {{ $tahun }}</title>
    <style>
        @page {
            margin: 20mm 16mm 24mm;
            footer: html_pageFooter;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8.5px;
            color: #1C2536;
            line-height: 1.6;
            background: #fff;
        }

        /* ===== HEADER ===== */
        
        .header-body {
            width: 100%;
            border-collapse: collapse;
        }
        .header-body td { border: none; vertical-align: middle; padding: 0; }
        .header-body td.brand-cell { width: auto; }
        .header-body td.contact-cell { text-align: right; width: 45%; }

        .header-monogram {
            width: 54px;
            height: 54px;
            background: #1B3A6B;
            color: #C8A84B;
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            line-height: 54px;
            letter-spacing: 1px;
            display: inline-block;
            vertical-align: middle;
            margin-right: 10px;
        }
        .header-brand-inline {
            display: inline-block;
            vertical-align: middle;
        }
        .header-brand-inline h1 {
            font-size: 17px;
            font-weight: bold;
            color: #1B3A6B;
            margin: 0 0 2px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .header-brand-inline .tagline {
            font-size: 7.5px;
            color: #C8A84B;
            margin: 0;
            letter-spacing: 3px;
            text-transform: uppercase;
            font-weight: bold;
        }
        .header-contact {
            font-size: 7.5px;
            color: #6B7280;
            line-height: 1.85;
            text-align: right;
        }
        .header-contact .contact-title {
            color: #1B3A6B;
            font-size: 7px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: bold;
            display: block;
            margin-bottom: 2px;
        }
        .header-divider {
            border: none;
            border-top: 1px solid #D1D5DB;
            margin: 14px 0 0;
        }
        .header-doc {
            margin-top: 12px;
            background: #F4F6FA;
            border-left: 3px solid #1B3A6B;
            padding: 8px 14px;
        }
        .header-doc h2 {
            font-size: 12px;
            font-weight: bold;
            color: #1B3A6B;
            margin: 0 0 2px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }
        .header-doc p {
            font-size: 8px;
            color: #6B7280;
            margin: 0;
        }

        /* ===== SECTION TITLE ===== */
        .section-title {
            font-size: 8px;
            font-weight: bold;
            color: #1B3A6B;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin: 20px 0 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #1B3A6B;
        }

        /* ===== SUMMARY CARDS ===== */
        .summary-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .summary-table td {
            width: 25%;
            border: 1px solid #E5E7EB;
            padding: 10px 8px;
            text-align: center;
            vertical-align: middle;
        }
        .summary-table td:first-child { border-left: 3px solid #1B3A6B; }
        .sum-label {
            font-size: 6.5px;
            text-transform: uppercase;
            color: #9CA3AF;
            letter-spacing: 1.2px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .sum-value { font-size: 15px; font-weight: bold; margin: 2px 0; }
        .sum-sub   { font-size: 6.5px; color: #9CA3AF; margin-top: 2px; }

        .text-green { color: #15803D; }
        .text-amber { color: #B45309; }
        .text-blue  { color: #1B3A6B; }
        .text-red   { color: #B91C1C; }
        .text-gold  { color: #C8A84B; }
        .text-muted { color: #9CA3AF; }

        /* ===== DATA TABLE ===== */
        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; table-layout: fixed;}
        .data-table thead th {
            background: #1B3A6B;
            color: #fff;
            font-weight: bold;
            padding: 6px 5px;
            text-align: center;
            font-size: 6.5px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            border: 1px solid #1B3A6B;
        }
        .data-table tbody td {
            padding: 5px;
            border: 1px solid #E5E7EB;
            font-size: 7.5px;
            vertical-align: middle;
        }
        .data-table tbody tr:nth-child(even) td { background: #F8FAFC; }
        .data-table tfoot td {
            background: #F4F6FA;
            border: 1px solid #D1D5DB;
            font-size: 7.5px;
            font-weight: bold;
            padding: 5px;
            color: #1B3A6B;
        }

        /* ===== BADGES ===== */
        .badge {
            display: inline-block;
            padding: 1.5px 6px;
            font-size: 6.5px;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;

            white-space: normal;
            word-wrap: break-word;
            overflow-wrap: break-word;
            word-break: break-word;

        }
        .badge-selesai      { background: #D1FAE5; color: #065F46; }
        .badge-dikonfirmasi { background: #DBEAFE; color: #1E3A8A; }
        .badge-menunggu     { background: #FEF3C7; color: #78350F; }
        .badge-dibatalkan   { background: #FEE2E2; color: #7F1D1D; }
        .badge-kadaluarsa   { background: #F3F4F6; color: #374151; }
        .badge-pending      { background: #FFF7ED; color: #7C2D12; }

        /* ===== CHART TABLE ===== */
        .chart-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .chart-table thead th {
            background: #1B3A6B;
            color: #fff;
            padding: 6px 7px;
            font-size: 6.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            border: 1px solid #1B3A6B;
        }
        .chart-table tbody td {
            padding: 4px 7px;
            border: 1px solid #E5E7EB;
            font-size: 7.5px;
            vertical-align: middle;
        }
        .chart-table tbody tr:nth-child(even) td { background: #F8FAFC; }
        .chart-table tfoot td {
            background: #F4F6FA;
            border: 1px solid #D1D5DB;
            padding: 5px 7px;
            font-size: 8px;
            font-weight: bold;
            color: #1B3A6B;
        }
        .chart-bar-container {
            width: 100%;
            background: #EFF2F7;
            height: 16px;
            position: relative;
        }
        .chart-bar {
            height: 16px;
            text-align: right;

            padding-bottom: 2px;
            line-height: 12px; 

            padding-right: 5px;
            font-size: 6.5px;
            font-weight: bold;
            color: #fff;
            min-width: 2px;

            /* Tambahkan dua properti ini */
            white-space: nowrap; /* Mencegah teks turun ke baris baru */
            overflow: visible;   /* Memastikan teks tetap terlihat jika sedikit melebihi batas bar */
        }
        .bar-navy  { background: #1B3A6B; }
        .bar-gold  { background: #C8A84B; }
        .bar-green { background: #15803D; }
        .bar-amber { background: #B45309; }
        .bar-red   { background: #B91C1C; }
        .bar-slate { background: #475569; }
        .bar-teal  { background: #0F766E; }

        /* ===== SIGNATURE ===== */
        .signature-section { margin-top: 28px; width: 100%; }
        .signature-table   { width: 100%; border-collapse: collapse; }
        .signature-table td {
            border: none;
            width: 33%;
            text-align: center;
            padding: 0 10px;
            vertical-align: top;
            font-size: 7.5px;
        }
        .sig-role {
            font-size: 7px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6B7280;
            font-weight: bold;
            margin-bottom: 50px;
        }
        .sig-line     { border-top: 1px solid #374151; margin-top: 4px; padding-top: 4px; }
        .sig-name     { font-weight: bold; color: #1B3A6B; font-size: 8px; }

        /* ===== FOOTER ===== */
        .footer {
            text-align: center;
            font-size: 7px;
            color: #9CA3AF;
            border-top: 1px solid #E5E7EB;
            padding-top: 8px;
            margin-top: 20px;
        }

        /* ===== UTILITY ===== */
        .text-left   { text-align: left; }
        .text-center { text-align: center; }
        .text-right  { text-align: right; }
        .font-bold   { font-weight: bold; }
        .page-break  { page-break-before: always; }
        .nowrap      { white-space: nowrap; }
        .small       { font-size: 6.5px; color: #6B7280; }
    </style>
</head>
<body>

    {{-- ============================================================ --}}
    {{-- HEADER / KOP SURAT                                          --}}
    {{-- ============================================================ --}}
    <div class="header-rule-top"></div>
    <div class="header-rule-accent"></div>

    <table class="header-body">
        <tr>
            <td class="brand-cell">
                <div class="header-monogram">YR</div>
                <div class="header-brand-inline">
                    <h1>Yoza Rent Car</h1>
                    <p class="tagline">Rental Mobil Terpercaya</p>
                </div>
            </td>
        </tr>
    </table>

    <hr class="header-divider">

    <div class="header-doc">
        <h2>Laporan Penjualan</h2>
        <p>Periode: 1 Januari {{ $tahun }} &ndash; 31 Desember {{ $tahun }} &nbsp;|&nbsp; Diterbitkan: {{ now()->format('d/m/Y H:i') }} WIB</p>
    </div>


    {{-- ============================================================ --}}
    {{-- RINGKASAN EKSEKUTIF                                          --}}
    {{-- ============================================================ --}}
    <div class="section-title">Ringkasan Eksekutif</div>
    <table class="summary-table">
        <tr>
            <td>
                <div class="sum-label">Total Pendapatan</div>
                <div class="sum-value text-green">Rp {{ number_format($ringkasan['total_pendapatan'], 0, ',', '.') }}</div>
                <div class="sum-sub">Seluruh transaksi {{ $tahun }}</div>
            </td>
            <td>
                <div class="sum-label">Pemesanan Selesai</div>
                <div class="sum-value text-amber">{{ $ringkasan['total_selesai'] }}</div>
                <div class="sum-sub">Transaksi berhasil</div>
            </td>
            <td>
                <div class="sum-label">Menunggu Konfirmasi</div>
                <div class="sum-value text-blue">{{ $ringkasan['total_pending'] }}</div>
                <div class="sum-sub">Dalam proses</div>
            </td>
            <td>
                <div class="sum-label">Dibatalkan</div>
                <div class="sum-value text-red">{{ $ringkasan['total_dibatalkan'] }}</div>
                <div class="sum-sub">Transaksi batal</div>
            </td>
        </tr>
    </table>


    {{-- ============================================================ --}}
    {{-- DATA PEMESANAN                                               --}}
    {{-- ============================================================ --}}
    <div class="section-title">Data Pemesanan</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:3%;" class="text-center">No</th>
                <th style="width:16%" class="text-center">Pelanggan</th>
                <th style="width:9%" class="text-center">Mobil</th>
                <th style="width:13%" class="text-center">Periode Sewa</th>
                <th style="width:3%;" class="text-center">Hari</th>
                <th style="width:9%;" class="text-center">Supir</th>
                <th style="width:11%;" class="text-right">Total Harga</th>
                <th style="width:13%;" class="text-center">Status</th>
                <th style="width:9%;" class="text-center">Pembayaran</th>
                <th style="width:13%;" class="text-center">Tgl. Dibuat</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pemesanans as $p)
            <tr>
                <td class="text-center text-muted">{{ $p->id }}</td>
                <td>
                    <div class="font-bold" style="font-size:8px;">{{ $p->user->name }}</div>
                    <div class="small">{{ $p->user->email }}</div>
                </td>
                <td>{{ $p->mobil->nama }}</td>
                <td class="nowrap">
                    {{ $p->tanggal_mulai->format('d/m/Y') }}<br>
                    <span class="small">s/d {{ $p->tanggal_selesai->format('d/m/Y') }}</span>
                </td>
                <td class="text-center">{{ $p->durasi() }}</td>
                <td class="text-center">{{ $p->opsi_supir ? 'Dengan Supir' : 'Lepas Kunci' }}</td>
                <td class="text-right font-bold">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                <td class="text-center">
                    @php
                        $statusClass = match($p->status) {
                            'selesai'      => 'badge-selesai',
                            'dikonfirmasi' => 'badge-dikonfirmasi',
                            'menunggu'     => 'badge-menunggu',
                            'dibatalkan'   => 'badge-dibatalkan',
                            'kadaluarsa'   => 'badge-kadaluarsa',
                            default        => 'badge-pending'
                        };
                    @endphp
                    <span class="badge {{ $statusClass }}">{{ ucfirst($p->status) }}</span>
                </td>
                <td class="text-center">{{ $p->payment?->labelMetode() ?? '&mdash;' }}</td>
                <td class="text-center nowrap">
                    {{ $p->created_at->format('d/m/Y') }}<br>
                    <span class="small">{{ $p->created_at->format('H:i') }} WIB</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center" style="padding:20px; color:#9CA3AF;">
                    Tidak ada data pemesanan untuk periode ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>


    {{-- ============================================================ --}}
    {{-- GRAFIK PENDAPATAN PER BULAN                                  --}}
    {{-- ============================================================ --}}
    <div class="section-title">Pendapatan Per Bulan &mdash; {{ $tahun }}</div>
    @php
        $months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $maxPendapatan = $pendapatan->max() ?: 1;
    @endphp
    <table class="chart-table">
        <thead>
            <tr>
                <th style="width:13%;">Bulan</th>
                <th style="width:62%;">Grafik Pendapatan</th>
                <th style="width:25%;" class="text-right">Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($months as $i => $month)
                @php
                    $val      = $pendapatan->get($i + 1, 0);
                    $pct      = ($val / $maxPendapatan) * 100;
                    $isMax    = $val > 0 && $val == $maxPendapatan;
                    $barClass = $isMax ? 'bar-gold' : 'bar-navy';
                @endphp
            <tr>
                <td class="{{ $isMax ? 'font-bold text-gold' : '' }}">{{ $month }}</td>
                <td>
                    <div class="chart-bar-container">
                        <div class="chart-bar {{ $barClass }}" style="width:{{ $pct }}%;">
                            @if($pct > 18)
                                Rp {{ number_format($val / 1000000, 1) }} jt
                            @endif
                        </div>
                    </div>
                </td>
                <td class="text-right {{ $isMax ? 'font-bold text-gold' : '' }}">
                    Rp {{ number_format($val, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td class="font-bold">Total</td>
                <td></td>
                <td class="text-right">Rp {{ number_format($pendapatan->sum(), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>


    {{-- ============================================================ --}}
    {{-- DISTRIBUSI STATUS                                            --}}
    {{-- ============================================================ --}}
    <div class="section-title">Distribusi Status Pemesanan &mdash; {{ $tahun }}</div>
    @php
        $maxStatus        = $statusCounts->max() ?: 1;
        $totalStatus      = $statusCounts->sum() ?: 1;
        $statusBarClasses = ['bar-green','bar-navy','bar-amber','bar-red','bar-slate','bar-teal'];
    @endphp
    <table class="chart-table">
        <thead>
            <tr>
                <th style="width:18%;">Status</th>
                <th style="width:50%;">Distribusi</th>
                <th style="width:16%;" class="text-right">Jumlah</th>
                <th style="width:16%;" class="text-right">Persentase</th>
            </tr>
        </thead>
        <tbody>
            @foreach($statusLabels as $i => $label)
                @php
                    $val      = $statusCounts[$i];
                    $pct      = ($val / $maxStatus) * 100;
                    $pctTotal = ($val / $totalStatus) * 100;
                    $barClass = $statusBarClasses[$i] ?? 'bar-slate';
                @endphp
            <tr>
                <td class="font-bold">{{ $label }}</td>
                <td>
                    <div class="chart-bar-container">
                        <div class="chart-bar {{ $barClass }}" style="width:{{ $pct }}%;">
                            @if($pct > 18)
                                {{ $val }} pemesanan
                            @endif
                        </div>
                    </div>
                </td>
                <td class="text-right font-bold">{{ $val }}</td>
                <td class="text-right">{{ number_format($pctTotal, 1) }}%</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>Total</td>
                <td></td>
                <td class="text-right">{{ $statusCounts->sum() }}</td>
                <td class="text-right">100.0%</td>
            </tr>
        </tfoot>
    </table>

    {{-- ============================================================ --}}
    {{-- FOOTER                                                       --}}
    {{-- ============================================================ --}}
    <div class="footer">
        <strong>Yoza Rent Car</strong> &mdash; Jl. Raya No. 123, Kota Bandung, Jawa Barat 40123
        &nbsp;|&nbsp; Telp: (022) 1234-5678 &nbsp;|&nbsp; info@yozarentcar.co.id<br>
        Dokumen ini digenerate secara otomatis pada {{ now()->format('d/m/Y H:i') }} WIB.
        Laporan ini bersifat resmi dan hanya untuk keperluan internal perusahaan.
    </div>

</body>
</html>