@extends('emails.layout')

@section('content')
<p class="greeting">Halo, <strong>{{ $pemesanan->user->name }}</strong>!</p>
<p class="message">
    Kabar baik! Pemesanan Anda telah <strong>dikonfirmasi</strong>.
    Selamat menikmati perjalanan bersama Yoza Rent Car!
</p>

<div class="card">
    <div class="card-row">
        <span class="label">ID Pemesanan</span>
        <span class="value">#{{ $pemesanan->id }}</span>
    </div>
    <div class="card-row">
        <span class="label">Mobil</span>
        <span class="value">{{ $pemesanan->mobil->nama }} — {{ $pemesanan->mobil->plat_nomor }}</span>
    </div>
    <div class="card-row">
        <span class="label">Tanggal Mulai</span>
        <span class="value">{{ $pemesanan->tanggal_mulai->format('d M Y') }}</span>
    </div>
    <div class="card-row">
        <span class="label">Tanggal Selesai</span>
        <span class="value">{{ $pemesanan->tanggal_selesai->format('d M Y') }}</span>
    </div>
    <div class="card-row">
        <span class="label">Opsi Supir</span>
        <span class="value">{{ $pemesanan->opsi_supir ? 'Dengan Supir' : 'Self-Drive' }}</span>
    </div>
    <div class="card-row">
        <span class="label">Status</span>
        <span class="value"><span class="badge badge-success">Dikonfirmasi</span></span>
    </div>
</div>

<p class="message">Ada pertanyaan? Hubungi kami langsung melalui fitur chat.</p>
<a href="{{ route('pemesanan.show', $pemesanan) }}" class="btn">Lihat Detail Pemesanan</a>
@endsection