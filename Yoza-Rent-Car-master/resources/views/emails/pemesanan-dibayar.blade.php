@extends('emails.layout')

@section('content')
<p class="greeting">Halo, <strong>{{ $pemesanan->user->name }}</strong>!</p>
<p class="message">Pembayaran Anda telah kami terima. Pemesanan sedang menunggu konfirmasi dari tim kami. Kami akan segera memproses dan mengirimkan konfirmasi.</p>

<div class="card">
    <div class="card-row">
        <span class="label">ID Pemesanan</span>
        <span class="value">#{{ $pemesanan->id }}</span>
    </div>
    <div class="card-row">
        <span class="label">Mobil</span>
        <span class="value">{{ $pemesanan->mobil->nama }}</span>
    </div>
    <div class="card-row">
        <span class="label">Jumlah Dibayar</span>
        <span class="value">Rp {{ number_format($pemesanan->payment->amount, 0, ',', '.') }}</span>
    </div>
    <div class="card-row">
        <span class="label">Metode Pembayaran</span>
        <span class="value">{{ $pemesanan->payment->labelMetode() }}</span>
    </div>
    <div class="card-row">
        <span class="label">Waktu Pembayaran</span>
        <span class="value">{{ $pemesanan->payment->paid_at->format('d M Y, H:i') }}</span>
    </div>
    <div class="card-row">
        <span class="label">Status</span>
        <span class="value"><span class="badge badge-info">Menunggu Konfirmasi Admin</span></span>
    </div>
</div>

<a href="{{ route('pemesanan.show', $pemesanan) }}" class="btn">Lihat Detail Pemesanan</a>
@endsection