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
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles, WithEvents, WithCharts
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
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
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
                $chartStartRow = $highestRow + 3;

                // ══════════════════════════════════════════════════════════
                // CHART 1: Pendapatan Bulanan (Bar Chart)
                // ══════════════════════════════════════════════════════════

                // Query monthly revenue
                $pendapatan = collect(range(1, 12))->map(fn ($bulan) =>
                    Payment::where('status', 'dikonfirmasi')
                        ->whereYear('paid_at', $this->tahun)
                        ->whereMonth('paid_at', $bulan)
                        ->sum('amount')
                );

                // Section title
                $sheet->setCellValue("A{$chartStartRow}", "PENDAPATAN PER BULAN TAHUN {$this->tahun}");
                $sheet->getStyle("A{$chartStartRow}")->getFont()->setBold(true)->setSize(13);
                $sheet->getStyle("A{$chartStartRow}")->getFont()->getColor()->setARGB('1E40AF');

                // Column headers for chart data
                $headerRow = $chartStartRow + 1;
                $sheet->setCellValue("A{$headerRow}", 'Bulan');
                $sheet->setCellValue("B{$headerRow}", 'Pendapatan (Rp)');
                $sheet->getStyle("A{$headerRow}:B{$headerRow}")->getFont()->setBold(true);
                $sheet->getStyle("A{$headerRow}:B{$headerRow}")
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFE2E8F0');

                $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

                foreach ($months as $i => $month) {
                    $row = $headerRow + 1 + $i;
                    $sheet->setCellValue("A{$row}", $month);
                    $sheet->setCellValue("B{$row}", $pendapatan->get($i + 1, 0));
                }

                $dataStartRow = $headerRow + 1;
                $dataEndRow = $headerRow + 12;

                // ── Bar Chart: Pendapatan Bulanan ──────────────────────
                $labelSeries = new DataSeriesValues(
                    DataSeriesValues::DATASERIES_TYPE_STRING,
                    "'{$sheetTitle}'!\$A\${$dataStartRow}:\$A\${$dataEndRow}",
                    null,
                    12
                );

                $valueSeries = new DataSeriesValues(
                    DataSeriesValues::DATASERIES_TYPE_NUMBER,
                    "'{$sheetTitle}'!\$B\${$dataStartRow}:\$B\${$dataEndRow}",
                    null,
                    12
                );

                $series = new DataSeries(
                    DataSeries::TYPE_BARCHART,
                    DataSeries::GROUPING_CLUSTERED,
                    range(0, 0),
                    [],
                    [$labelSeries],
                    [$valueSeries]
                );

                $series->setPlotDirection(DataSeries::DIRECTION_COL);

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

                $chart->setTopLeftPosition("D{$chartStartRow}");
                $chart->setBottomRightPosition("P" . ($chartStartRow + 16));

                $sheet->addChart($chart);

                // ══════════════════════════════════════════════════════════
                // CHART 2: Status Pemesanan (Pie Chart)
                // ══════════════════════════════════════════════════════════

                $pieStartRow = $chartStartRow + 19;

                // Query status counts
                $statuses = ['selesai', 'dikonfirmasi', 'menunggu_konfirmasi_admin', 'dibatalkan', 'kadaluarsa', 'pending'];
                $statusLabels = ['Selesai', 'Dikonfirmasi', 'Menunggu Konfirmasi', 'Dibatalkan', 'Kadaluarsa', 'Pending'];
                $statusCounts = collect($statuses)->map(fn ($s) =>
                    Pemesanan::whereYear('created_at', $this->tahun)
                        ->where('status', $s)
                        ->count()
                );

                // Section title
                $sheet->setCellValue("A{$pieStartRow}", "DISTRIBUSI STATUS PEMESANAN TAHUN {$this->tahun}");
                $sheet->getStyle("A{$pieStartRow}")->getFont()->setBold(true)->setSize(13);
                $sheet->getStyle("A{$pieStartRow}")->getFont()->getColor()->setARGB('1E40AF');

                // Column headers
                $pieHeaderRow = $pieStartRow + 1;
                $sheet->setCellValue("A{$pieHeaderRow}", 'Status');
                $sheet->setCellValue("B{$pieHeaderRow}", 'Jumlah');
                $sheet->getStyle("A{$pieHeaderRow}:B{$pieHeaderRow}")->getFont()->setBold(true);
                $sheet->getStyle("A{$pieHeaderRow}:B{$pieHeaderRow}")
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFE2E8F0');

                foreach ($statusLabels as $i => $label) {
                    $row = $pieHeaderRow + 1 + $i;
                    $sheet->setCellValue("A{$row}", $label);
                    $sheet->setCellValue("B{$row}", $statusCounts[$i]);
                }

                $pieDataStart = $pieHeaderRow + 1;
                $pieDataEnd = $pieHeaderRow + count($statusLabels);

                // ── Pie Chart: Status Pemesanan ────────────────────────
                $pieLabelSeries = new DataSeriesValues(
                    DataSeriesValues::DATASERIES_TYPE_STRING,
                    "'{$sheetTitle}'!\$A\${$pieDataStart}:\$A\${$pieDataEnd}",
                    null,
                    count($statusLabels)
                );

                $pieValueSeries = new DataSeriesValues(
                    DataSeriesValues::DATASERIES_TYPE_NUMBER,
                    "'{$sheetTitle}'!\$B\${$pieDataStart}:\$B\${$pieDataEnd}",
                    null,
                    count($statusLabels)
                );

                $pieSeries = new DataSeries(
                    DataSeries::TYPE_PIECHART,
                    DataSeries::GROUPING_STANDARD,
                    range(0, 0),
                    [],
                    [$pieLabelSeries],
                    [$pieValueSeries]
                );

                $piePlotArea = new PlotArea(null, [$pieSeries]);
                $pieChartTitle = new Title("Status Pemesanan {$this->tahun}");
                $pieLegend = new Legend(Legend::POSITION_RIGHT, null, false);

                $pieChart = new Chart(
                    'status_chart',
                    $pieChartTitle,
                    $pieLegend,
                    $piePlotArea,
                    true,
                    DataSeries::EMPTY_AS_GAP
                );

                $pieChart->setTopLeftPosition("D{$pieStartRow}");
                $pieChart->setBottomRightPosition("P" . ($pieStartRow + 16));

                $sheet->addChart($pieChart);
            },
        ];
    }

    public function charts(): array
    {
        return [];
    }
}