@extends('emails.layout')

@section('content')
<p class="greeting">Halo, <strong>{{ $pemesanan->user->name }}</strong>!</p>
<p class="message">
    Mohon maaf, pemesanan Anda untuk <strong>{{ $pemesanan->mobil->nama }}</strong>
    tidak dapat kami proses saat ini.
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
        <span class="label">Tanggal</span>
        <span class="value">{{ $pemesanan->tanggal_mulai->format('d M Y') }} — {{ $pemesanan->tanggal_selesai->format('d M Y') }}</span>
    </div>
    <div class="card-row">
        <span class="label">Status</span>
        <span class="value"><span class="badge badge-warning">Ditolak</span></span>
    </div>
</div>

<p class="message">
    Jika Anda memiliki pertanyaan atau ingin mengetahui alasan penolakan,
    silakan hubungi tim kami melalui fitur chat.
</p>
<a href="{{ config('app.url') }}/chat" class="btn">Hubungi Kami via Chat</a>
@endsection