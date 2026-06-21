{{-- ============================================================ --}}
{{-- Style bersama untuk semua dokumen PDF Yoza Rent Car.          --}}
{{-- Dipanggil dari dalam tag <style> di tiap view PDF lewat:      --}}
{{--   @include('pdf.partials.brand-style')                       --}}
{{-- Style yang spesifik untuk satu dokumen (misal: data-table,    --}}
{{-- summary-table, chart-table) tetap ditulis di file masing-     --}}
{{-- masing, bukan di sini.                                       --}}
{{-- ============================================================ --}}

/* ===== HEADER BRAND ===== */
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
.header-brand-inline { display: inline-block; vertical-align: middle; }
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
.header-divider { border: none; border-top: 1px solid #D1D5DB; margin: 14px 0 0; }
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
.header-doc p { font-size: 8px; color: #6B7280; margin: 0; }

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

/* ===== BADGES ===== */
.badge {
    display: inline-block;
    padding: 1.5px 6px;
    font-size: 6.5px;
    font-weight: bold;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}
.badge-selesai      { background: #D1FAE5; color: #065F46; }
.badge-dikonfirmasi { background: #DBEAFE; color: #1E3A8A; }
.badge-menunggu     { background: #FEF3C7; color: #78350F; }
.badge-dibatalkan   { background: #FEE2E2; color: #7F1D1D; }
.badge-kadaluarsa   { background: #F3F4F6; color: #374151; }
.badge-pending      { background: #FFF7ED; color: #7C2D12; }

/* ===== FOOTER ===== */
.footer {
    text-align: center;
    font-size: 7px;
    color: #9CA3AF;
    border-top: 1px solid #E5E7EB;
    padding-top: 8px;
    margin-top: 20px;
}
.footer strong { color: #1B3A6B; }
