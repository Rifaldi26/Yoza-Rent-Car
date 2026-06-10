@extends('emails.layout')

@section('content')
<p class="greeting">Halo, <strong>Admin</strong>!</p>
<p class="message">
    Ada pemesanan baru yang sudah dibayar dan menunggu konfirmasi Anda.
</p>

<div class="card">
    <div class="card-row">
        <span class="label">ID Pemesanan</span>
        <span class="value">#{{ $pemesanan->id }}</span>
    </div>
    <div class="card-row">
        <span class="label">Pelanggan</span>
        <span class="value">{{ $pemesanan->user->name }}</span>
    </div>
    <div class="card-row">
        <span class="label">Email Pelanggan</span>
        <span class="value">{{ $pemesanan->user->email }}</span>
    </div>
    <div class="card-row">
        <span class="label">Mobil</span>
        <span class="value">{{ $pemesanan->mobil->nama }}</span>
    </div>
    <div class="card-row">
        <span class="label">Tanggal Sewa</span>
        <span class="value">{{ $pemesanan->tanggal_mulai->format('d M Y') }} — {{ $pemesanan->tanggal_selesai->format('d M Y') }}</span>
    </div>
    <div class="card-row">
        <span class="label">Total Dibayar</span>
        <span class="value">Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}</span>
    </div>
    <div class="card-row">
        <span class="label">Metode Bayar</span>
        <span class="value">{{ $pemesanan->payment->labelMetode() }}</span>
    </div>
</div>

<a href="{{ route('admin.pemesanan.show', $pemesanan) }}" class="btn">Konfirmasi Sekarang</a>
@endsection