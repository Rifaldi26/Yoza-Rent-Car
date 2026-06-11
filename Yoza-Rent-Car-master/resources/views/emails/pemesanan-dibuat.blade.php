@extends('emails.layout')

@section('content')
<p class="greeting">Halo, <strong>{{ $pemesanan->user->name }}</strong>!</p>
<p class="message">Pemesanan Anda telah berhasil dibuat. Silakan selesaikan pembayaran dalam <strong>24 jam</strong> agar pemesanan tidak otomatis kadaluarsa.</p>

<div class="card">
    <div class="card-row">
        <span class="label">ID Pemesanan</span>
        <span class="value">#{{ $pemesanan->id }}</span>
    </div>
    <div class="card-row">
        <span class="label">Mobil</span>
        <span class="value">{{ $pemesanan->mobil->nama }} ({{ $pemesanan->mobil->merek }})</span>
    </div>
    <div class="card-row">
        <span class="label">Tanggal Sewa</span>
        <span class="value">{{ $pemesanan->tanggal_mulai->format('d M Y') }} — {{ $pemesanan->tanggal_selesai->format('d M Y') }}</span>
    </div>
    <div class="card-row">
        <span class="label">Durasi</span>
        <span class="value">{{ $pemesanan->durasi() }} hari</span>
    </div>
    <div class="card-row">
        <span class="label">Opsi Supir</span>
        <span class="value">{{ $pemesanan->opsi_supir ? 'Dengan Supir' : 'Self-Drive' }}</span>
    </div>
    <div class="card-row">
        <span class="label">Total Pembayaran</span>
        <span class="value">Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}</span>
    </div>
</div>

<a href="{{ route('payment.checkout', $pemesanan) }}" class="btn">Bayar Sekarang</a>

<p class="message" style="font-size:13px; color:#888;">
    Jika Anda tidak merasa melakukan pemesanan ini, abaikan email ini atau hubungi kami segera.
</p>
@endsection