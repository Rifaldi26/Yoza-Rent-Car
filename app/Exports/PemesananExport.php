<?php

namespace App\Exports;

use App\Models\Pemesanan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PemesananExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles
{
    protected $tahun;

    protected $status;

    public function __construct($tahun, $status = null)
    {
        $this->tahun = $tahun;
        $this->status = $status;
    }

    public function collection()
    {
        return Pemesanan::with(['user', 'mobil', 'payment'])
            ->whereYear('created_at', $this->tahun)
            ->when($this->status, fn ($q) => $q->where('status', $this->status)
            )
            ->get()
            ->map(function ($p) {
                return [
                    $p->id,
                    $p->user->name,
                    $p->user->email,
                    $p->mobil->nama,
                    $p->tanggal_mulai->format('d/m/Y'),
                    $p->tanggal_selesai->format('d/m/Y'),
                    $p->durasi().' Hari',
                    $p->opsi_supir ? 'Dengan Supir' : 'Lepas Kunci',
                    $p->total_harga,
                    ucfirst($p->status),
                    $p->payment?->labelMetode() ?? '-',
                    $p->created_at->format('d/m/Y H:i'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Pelanggan',
            'Email',
            'Mobil',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Durasi',
            'Opsi Supir',
            'Total Harga',
            'Status',
            'Metode Bayar',
            'Tanggal Dibuat',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => '2563EB'],
                ],
            ],
        ];
    }
}
