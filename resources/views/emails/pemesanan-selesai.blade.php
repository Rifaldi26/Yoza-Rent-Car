@extends('emails.layout')

@section('content')
<p class="greeting">Halo, <strong>{{ $pemesanan->user->name }}</strong>!</p>
<p class="message">
    Terima kasih telah menggunakan layanan DrivEase! 🙏
    Pemesanan Anda telah selesai dengan sukses.
</p>

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
        <span class="label">Durasi Sewa</span>
        <span class="value">{{ $pemesanan->durasi() }} hari</span>
    </div>
    <div class="card-row">
        <span class="label">Total Dibayar</span>
        <span class="value">Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}</span>
    </div>
    <div class="card-row">
        <span class="label">Status</span>
        <span class="value"><span class="badge badge-success">Selesai</span></span>
    </div>
</div>

<p class="message">Kami berharap perjalanan Anda menyenangkan. Sampai jumpa di pemesanan berikutnya!</p>
<a href="{{ route('payment.invoice', $pemesanan) }}" class="btn">Download Invoice</a>
@endsection