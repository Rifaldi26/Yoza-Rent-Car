<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: 'DejaVu Sans', Arial, sans-serif;
    font-size: 11px;
    color: #18213a;
    background: #fff;
    line-height: 1.5;
}
.page { padding: 32px 40px; }

/* ── Header ── */
.header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.brand-name { font-size: 20px; font-weight: 700; color: #18213a; }
.brand-sub { font-size: 9px; color: #7a8499; letter-spacing: 2px; text-transform: uppercase; }
.report-meta { text-align: right; }
.report-title { font-size: 16px; font-weight: 700; color: #3b6fd4; }
.report-period { font-size: 10px; color: #7a8499; margin-top: 2px; }
.report-generated { font-size: 9px; color: #aab0bf; margin-top: 2px; }

.divider { height: 2px; background: linear-gradient(to right, #3b6fd4, #e5e9f2); margin: 16px 0; border-radius: 2px; }

/* ── Summary Cards ── */
.summary-grid { display: flex; gap: 12px; margin-bottom: 24px; }
.summary-card {
    flex: 1; padding: 14px 16px;
    border-radius: 8px; border: 1px solid;
}
.card-blue   { background: #eef2fb; border-color: #c5d5f5; }
.card-green  { background: #e8f5ee; border-color: #b7dfcc; }
.card-red    { background: #fef2f2; border-color: #fecaca; }
.card-gray   { background: #f4f6fb; border-color: #e5e9f2; }
.card-label  { font-size: 9px; font-weight: 700; color: #7a8499; text-transform: uppercase; letter-spacing: 1px; }
.card-value  { font-size: 16px; font-weight: 700; color: #18213a; margin-top: 4px; }
.card-value-green { color: #1e7145; }
.card-value-red   { color: #b91c1c; }
.card-value-blue  { color: #2e5bb8; }

/* ── Section ── */
.section-title {
    font-size: 11px; font-weight: 700; color: #18213a;
    text-transform: uppercase; letter-spacing: 1px;
    margin-bottom: 10px; margin-top: 20px;
    padding-bottom: 6px; border-bottom: 1.5px solid #e5e9f2;
}

/* ── Tables ── */
.tbl { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
.tbl thead th {
    background: #1E3A5F; color: #fff;
    padding: 8px 12px; text-align: left;
    font-size: 9px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.8px;
}
.tbl thead th.text-right { text-align: right; }
.tbl tbody tr:nth-child(even) { background: #f9fafc; }
.tbl tbody td { padding: 8px 12px; font-size: 10px; border-bottom: 1px solid #f1f4fa; color: #18213a; }
.tbl tbody td.text-right { text-align: right; }
.tbl tbody td.text-muted { color: #7a8499; }
.tbl tbody td.font-mono { font-family: monospace; font-size: 9px; }
.tbl tfoot td {
    padding: 8px 12px; font-size: 11px; font-weight: 700;
    border-top: 2px solid #e5e9f2; color: #18213a;
}
.tbl tfoot td.text-right { text-align: right; }
.tbl tfoot td.green { color: #1e7145; }
.tbl tfoot td.red   { color: #b91c1c; }
.tbl tfoot td.blue  { color: #2e5bb8; }

/* ── Laba Rugi ── */
.lr-section { margin-bottom: 16px; }
.lr-header {
    background: #f4f6fb; padding: 8px 12px;
    font-weight: 700; font-size: 10px;
    border-radius: 6px 6px 0 0;
    color: #18213a;
}
.lr-row { display: flex; justify-content: space-between; padding: 6px 12px; border-bottom: 1px solid #f1f4fa; }
.lr-row:last-child { border-bottom: none; }
.lr-row-label { font-size: 10px; color: #18213a; }
.lr-row-value { font-size: 10px; font-weight: 600; color: #18213a; }
.lr-subtotal {
    display: flex; justify-content: space-between; padding: 8px 12px;
    background: #f9fafc; border-top: 1.5px solid #e5e9f2;
    font-weight: 700; font-size: 10px;
}
.lr-total {
    display: flex; justify-content: space-between;
    padding: 10px 12px; border-top: 2px solid #3b6fd4;
    font-weight: 700; font-size: 13px;
}

/* ── Arus Kas chart bar ── */
.kas-row { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; }
.kas-month { width: 30px; font-size: 9px; color: #7a8499; text-align: right; flex-shrink: 0; }
.kas-bar-wrap { flex: 1; background: #f1f4fa; border-radius: 4px; height: 14px; position: relative; }
.kas-bar-in { height: 14px; border-radius: 4px; background: #3b6fd4; }
.kas-bar-out { height: 6px; border-radius: 4px; background: #ef4444; margin-top: -10px; opacity: 0.6; }
.kas-val { font-size: 9px; color: #18213a; font-weight: 600; width: 80px; text-align: right; flex-shrink: 0; }

/* ── Footer ── */
.footer {
    margin-top: 28px; padding-top: 12px;
    border-top: 1px solid #e5e9f2;
    display: flex; justify-content: space-between;
    font-size: 9px; color: #aab0bf;
}
</style>
</head>
<body>
<div class="page">

    {{-- ── Header ────────────────────────────────────────────── --}}
    <div class="header">
        <div>
            <div class="brand-name">Yoza Rent Car</div>
            <div class="brand-sub">Laporan Penjualan Internal</div>
        </div>
        <div class="report-meta">
            <div class="report-title">Laporan Akuntansi</div>
            <div class="report-period">Periode: Tahun {{ $tahun }}</div>
            <div class="report-generated">Dicetak: {{ now()->format('d M Y, H:i') }}</div>
        </div>
    </div>

    <div class="divider"></div>

    {{-- ── Summary Cards ───────────────────────────────────────── --}}
    @php
        $totalPend = $pendapatan->sum('total');
        $totalPeng = $pengeluaran->sum('total');
        $laba      = $totalPend - $totalPeng;
    @endphp
    <div class="summary-grid">
        <div class="summary-card card-blue">
            <div class="card-label">Total Pendapatan</div>
            <div class="card-value card-value-blue">
                Rp {{ number_format($totalPend, 0, ',', '.') }}
            </div>
        </div>
        <div class="summary-card card-red">
            <div class="card-label">Total Pengeluaran</div>
            <div class="card-value card-value-red">
                Rp {{ number_format($totalPeng, 0, ',', '.') }}
            </div>
        </div>
        <div class="summary-card {{ $laba >= 0 ? 'card-green' : 'card-red' }}">
            <div class="card-label">{{ $laba >= 0 ? 'Laba Bersih' : 'Rugi Bersih' }}</div>
            <div class="card-value {{ $laba >= 0 ? 'card-value-green' : 'card-value-red' }}">
                Rp {{ number_format(abs($laba), 0, ',', '.') }}
            </div>
        </div>
    </div>

    {{-- ── Laporan Laba Rugi ──────────────────────────────────── --}}
    <div class="section-title">Laporan Laba Rugi</div>

    {{-- Pendapatan --}}
    <div class="lr-section">
        <div class="lr-header">Pendapatan</div>
        @foreach($pendapatan as $row)
        <div class="lr-row">
            <span class="lr-row-label">{{ $row['nama'] }}</span>
            <span class="lr-row-value">Rp {{ number_format($row['total'], 0, ',', '.') }}</span>
        </div>
        @endforeach
        <div class="lr-subtotal">
            <span>Total Pendapatan</span>
            <span style="color:#2e5bb8;">Rp {{ number_format($totalPend, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- Pengeluaran --}}
    <div class="lr-section">
        <div class="lr-header">Pengeluaran</div>
        @foreach($pengeluaran as $row)
        <div class="lr-row">
            <span class="lr-row-label">{{ $row['nama'] }}</span>
            <span class="lr-row-value">Rp {{ number_format($row['total'], 0, ',', '.') }}</span>
        </div>
        @endforeach
        <div class="lr-subtotal">
            <span>Total Pengeluaran</span>
            <span style="color:#b91c1c;">Rp {{ number_format($totalPeng, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- Laba/Rugi Bersih --}}
    <div class="lr-total">
        <span>{{ $laba >= 0 ? 'Laba' : 'Rugi' }} Bersih Tahun {{ $tahun }}</span>
        <span style="color:{{ $laba >= 0 ? '#1e7145' : '#b91c1c' }};">
            Rp {{ number_format(abs($laba), 0, ',', '.') }}
        </span>
    </div>

    {{-- ── Arus Kas Per Bulan ─────────────────────────────────── --}}
    <div class="section-title" style="margin-top:24px;">Arus Kas Bulanan</div>

    @php
        $maxKas = $arusKas->max(fn($r) => max($r['masuk'], 1));
        $months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    @endphp

    <table class="tbl" style="margin-bottom:16px;">
        <thead>
            <tr>
                <th>Bulan</th>
                <th class="text-right">Kas Masuk</th>
                <th class="text-right">Kas Keluar</th>
                <th class="text-right">Neto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($arusKas as $row)
            @if($row['masuk'] > 0 || $row['keluar'] > 0)
            <tr>
                <td>{{ $months[$row['bulan'] - 1] }} {{ $tahun }}</td>
                <td class="text-right" style="color:#2e5bb8;">
                    Rp {{ number_format($row['masuk'], 0, ',', '.') }}
                </td>
                <td class="text-right" style="color:#b91c1c;">
                    Rp {{ number_format($row['keluar'], 0, ',', '.') }}
                </td>
                <td class="text-right" style="color:{{ $row['neto'] >= 0 ? '#1e7145' : '#b91c1c' }}; font-weight:700;">
                    Rp {{ number_format($row['neto'], 0, ',', '.') }}
                </td>
            </tr>
            @endif
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>Total</td>
                <td class="text-right blue">Rp {{ number_format($arusKas->sum('masuk'), 0, ',', '.') }}</td>
                <td class="text-right red">Rp {{ number_format($arusKas->sum('keluar'), 0, ',', '.') }}</td>
                <td class="text-right {{ $arusKas->sum('neto') >= 0 ? 'green' : 'red' }}">
                    Rp {{ number_format($arusKas->sum('neto'), 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

    {{-- ── Jurnal Harian (ringkasan) ──────────────────────────── --}}
    <div class="section-title">Entri Jurnal ({{ $entries->count() }} entri)</div>

    <table class="tbl">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Ref</th>
                <th>Akun</th>
                <th>Keterangan</th>
                <th class="text-right">Debit</th>
                <th class="text-right">Kredit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entries->take(50) as $entry)
            <tr>
                <td class="text-muted" style="white-space:nowrap;">
                    {{ $entry->date->format('d M Y') }}
                </td>
                <td class="font-mono text-muted">
                    @if($entry->pemesanan_id)
                        #{{ $entry->pemesanan_id }}
                    @else
                        EXP
                    @endif
                </td>
                <td>{{ $entry->account->nama ?? '-' }}</td>
                <td class="text-muted" style="font-size:9px;">
                    {{ Str::limit($entry->description, 40) }}
                </td>
                <td class="text-right" style="color:#2e5bb8;">
                    @if($entry->debit > 0)
                        Rp {{ number_format($entry->debit, 0, ',', '.') }}
                    @else
                        <span class="text-muted">&mdash;</span>
                    @endif
                </td>
                <td class="text-right" style="color:#1e7145;">
                    @if($entry->credit > 0)
                        Rp {{ number_format($entry->credit, 0, ',', '.') }}
                    @else
                        <span class="text-muted">&mdash;</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">Total</td>
                <td class="text-right blue">
                    Rp {{ number_format($entries->sum('debit'), 0, ',', '.') }}
                </td>
                <td class="text-right green">
                    Rp {{ number_format($entries->sum('credit'), 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

    @if($entries->count() > 50)
        <p style="font-size:9px; color:#7a8499; margin-top:6px; text-align:center;">
            Menampilkan 50 dari {{ $entries->count() }} entri. Gunakan export Excel untuk data lengkap.
        </p>
    @endif

    {{-- ── Footer ─────────────────────────────────────────────── --}}
    <div class="footer">
        <div>
            Yoza Rent Car &bull; Laporan Penjualan Internal &bull; Tahun {{ $tahun }}<br>
            Dicetak oleh: {{ auth()->user()->name }} pada {{ now()->format('d M Y, H:i') }}
        </div>
        <div style="text-align:right;">
            Dokumen ini bersifat rahasia dan hanya untuk keperluan internal.<br>
            &copy; {{ date('Y') }} Yoza Rent Car. Semua hak dilindungi.
        </div>
    </div>

</div>
</body>
</html>