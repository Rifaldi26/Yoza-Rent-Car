<?php

namespace App\Exports;

use App\Models\Payment;
use App\Models\Pemesanan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCharts;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PemesananExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles, WithEvents, WithCharts
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
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
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

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheetTitle = $sheet->getTitle();
                $highestRow = $sheet->getHighestRow();
                $chartStartRow = $highestRow + 3; // 2 blank rows after data

                // Query monthly revenue for the selected year
                $pendapatan = collect(range(1, 12))->map(fn ($bulan) =>
                    Payment::where('status', 'dikonfirmasi')
                        ->whereYear('paid_at', $this->tahun)
                        ->whereMonth('paid_at', $bulan)
                        ->sum('amount')
                );

                // Write section title
                $sheet->setCellValue("A{$chartStartRow}", "PENDAPATAN PER BULAN TAHUN {$this->tahun}");
                $sheet->getStyle("A{$chartStartRow}")->getFont()->setBold(true)->setSize(12);

                // Write column headers for chart data
                $headerRow = $chartStartRow + 1;
                $sheet->setCellValue("A{$headerRow}", 'Bulan');
                $sheet->setCellValue("B{$headerRow}", 'Pendapatan (Rp)');
                $sheet->getStyle("A{$headerRow}:B{$headerRow}")->getFont()->setBold(true);
                $sheet->getStyle("A{$headerRow}:B{$headerRow}")
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFE2E8F0');

                // Month labels
                $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

                // Write data rows for chart source
                foreach ($months as $i => $month) {
                    $row = $headerRow + 1 + $i;
                    $sheet->setCellValue("A{$row}", $month);
                    $sheet->setCellValue("B{$row}", $pendapatan->get($i + 1, 0));
                }

                $dataStartRow = $headerRow + 1; // First data row
                $dataEndRow = $headerRow + 12;  // Last data row

                // === CREATE BAR CHART ===
                // Categories (months)
                $labelSeries = new DataSeriesValues(
                    DataSeriesValues::DATASERIES_TYPE_STRING,
                    "'{$sheetTitle}'!\$A\${$dataStartRow}:\$A\${$dataEndRow}",
                    null,
                    12
                );

                // Values (revenue)
                $valueSeries = new DataSeriesValues(
                    DataSeriesValues::DATASERIES_TYPE_NUMBER,
                    "'{$sheetTitle}'!\$B\${$dataStartRow}:\$B\${$dataEndRow}",
                    null,
                    12
                );

                // Build the series
                $series = new DataSeries(
                    DataSeries::TYPE_BARCHART,
                    DataSeries::GROUPING_CLUSTERED,
                    range(0, 0),  // plotOrder
                    [],           // plotLabel (auto-filled)
                    [$labelSeries],  // plotCategory = x-axis (months)
                    [$valueSeries]   // plotValues = y-axis (revenue)
                );

                $plotArea = new PlotArea(null, [$series]);
                $chartTitle = new Title("Pendapatan Bulanan {$this->tahun}");
                $legend = new Legend(Legend::POSITION_BOTTOM, null, false);

                $chart = new Chart(
                    'pendapatan_chart',
                    $chartTitle,
                    $legend,
                    $plotArea,
                    true,
                    DataSeries::EMPTY_AS_GAP
                );

                // Position the chart
                $chart->setTopLeftPosition("D{$chartStartRow}");
                $chart->setBottomRightPosition("P" . ($chartStartRow + 16));

                $sheet->addChart($chart);
            },
        ];
    }

    public function charts(): array
    {
        // The chart is created dynamically in registerEvents, return empty array
        // since we don't have the chart object at this point
        return [];
    }
}
