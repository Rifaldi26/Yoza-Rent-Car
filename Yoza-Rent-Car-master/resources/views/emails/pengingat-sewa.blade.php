@extends('emails.layout')

@section('content')

{{-- Greeting --}}
<p class="greeting">
    Halo, <strong>{{ $pemesanan->user->name }}</strong>!
</p>

{{-- Pesan utama --}}
@if ($hariSebelum === 1)
    <p class="message">
        Sewa kendaraan Anda dimulai <strong>besok</strong>.
        Pastikan Anda telah mempersiapkan segala kebutuhan untuk pengambilan kendaraan.
    </p>
@else
    <p class="message">
        Sewa kendaraan Anda dimulai <strong>{{ $hariSebelum }} hari lagi</strong>.
        Berikut ringkasan pemesanan Anda sebagai pengingat.
    </p>
@endif

{{-- Info card --}}
<div class="card">
    <div class="card-row">
        <span class="label">ID Pemesanan</span>
        <span class="value">#{{ $pemesanan->id }}</span>
    </div>
    <div class="card-row">
        <span class="label">Kendaraan</span>
        <span class="value">{{ $pemesanan->mobil->nama }}</span>
    </div>
    <div class="card-row">
        <span class="label">Plat Nomor</span>
        <span class="value">{{ $pemesanan->mobil->plat_nomor }}</span>
    </div>
    <div class="card-row">
        <span class="label">Tanggal Mulai</span>
        <span class="value">{{ $pemesanan->tanggal_mulai->isoFormat('D MMMM Y') }}</span>
    </div>
    <div class="card-row">
        <span class="label">Tanggal Selesai</span>
        <span class="value">{{ $pemesanan->tanggal_selesai->isoFormat('D MMMM Y') }}</span>
    </div>
    <div class="card-row">
        <span class="label">Durasi</span>
        <span class="value">{{ $pemesanan->durasi() }} hari</span>
    </div>
    <div class="card-row">
        <span class="label">Opsi Supir</span>
        <span class="value">{{ $pemesanan->opsi_supir ? 'Dengan Supir' : 'Self-Drive' }}</span>
    </div>
</div>

{{-- Checklist persiapan --}}
<p class="section-title">Yang perlu disiapkan</p>
<table class="checklist" cellpadding="0" cellspacing="0">
    <tr>
        <td class="checklist-icon">
            {{-- SVG: ID card icon --}}
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                 fill="none" stroke="#2E75B6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="5" width="20" height="14" rx="2"/>
                <path d="M16 10h2M16 14h2M7 10h.01"/>
                <circle cx="9" cy="10" r="2"/>
                <path d="M6.5 14a2.5 2.5 0 0 1 5 0"/>
            </svg>
        </td>
        <td class="checklist-text">KTP atau identitas resmi</td>
    </tr>
    <tr>
        <td class="checklist-icon">
            {{-- SVG: car icon --}}
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                 fill="none" stroke="#2E75B6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 17H3a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h2m14 0h2a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2h-2"/>
                <rect x="5" y="7" width="14" height="10" rx="2"/>
                <circle cx="7.5" cy="17" r="1.5"/><circle cx="16.5" cy="17" r="1.5"/>
            </svg>
        </td>
        <td class="checklist-text">SIM yang masih berlaku</td>
    </tr>
    <tr>
        <td class="checklist-icon">
            {{-- SVG: document icon --}}
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                 fill="none" stroke="#2E75B6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="9" y1="13" x2="15" y2="13"/>
                <line x1="9" y1="17" x2="12" y2="17"/>
            </svg>
        </td>
        <td class="checklist-text">Invoice atau bukti konfirmasi pemesanan</td>
    </tr>
</table>

<p class="message" style="margin-top:24px;">
    Ada pertanyaan sebelum keberangkatan? Tim kami siap membantu melalui live chat.
</p>

<a href="{{ route('pemesanan.show', $pemesanan) }}" class="btn">Lihat Detail Pemesanan</a>

@endsection