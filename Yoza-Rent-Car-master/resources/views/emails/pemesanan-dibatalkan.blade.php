@extends('emails.layout')

@section('content')
<p class="greeting">Halo, <strong>{{ $pemesanan->user->name }}</strong>!</p>
<p class="message">Pemesanan Anda telah berhasil dibatalkan sesuai permintaan.</p>

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
        <span class="label">Tanggal</span>
        <span class="value">{{ $pemesanan->tanggal_mulai->format('d M Y') }} — {{ $pemesanan->tanggal_selesai->format('d M Y') }}</span>
    </div>
    <div class="card-row">
        <span class="label">Status</span>
        <span class="value"><span class="badge badge-warning">Dibatalkan</span></span>
    </div>
</div>

<p class="message">Ingin memesan kembali? Kami siap melayani Anda.</p>
<a href="{{ config('app.url') }}" class="btn">Lihat Katalog Mobil</a>
@endsection