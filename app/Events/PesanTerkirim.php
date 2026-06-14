<?php

namespace App\Events;

use App\Models\Pesan;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PesanTerkirim implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Pesan $pesan) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("chat.{$this->pesan->penerima_id}"),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->pesan->id,
            'pengirim_id' => $this->pesan->pengirim_id,
            'isi' => $this->pesan->isi,
            'waktu' => $this->pesan->created_at->format('H:i'),
            'pemesanan' => $this->pesan->pemesanan ? [
                'id' => $this->pesan->pemesanan->id,
                'nama_mobil' => $this->pesan->pemesanan->mobil->nama ?? '-',
                'tanggal_mulai' => $this->pesan->pemesanan->tanggal_mulai->format('d M Y'),
                'tanggal_selesai' => $this->pesan->pemesanan->tanggal_selesai->format('d M Y'),
                'status' => $this->pesan->pemesanan->labelStatus(),
                'total_harga' => number_format($this->pesan->pemesanan->total_harga, 0, ',', '.'),
            ] : null,
        ];
    }
}
