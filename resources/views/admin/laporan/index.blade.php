@extends('layouts.admin')
@section('title', __('Laporan'))

@section('content')

<x-page-header title="{{ __('Laporan Penjualan') }}" description="{{ __('Ringkasan penjualan dan analitik bisnis.') }}">
    <x-slot:actions>
        <form method="GET" class="flex items-center gap-2">
            <select name="tahun" onchange="this.form.submit()"
                    class="h-9 rounded-lg border border-gray-200 bg-white px-3 text-sm
                           focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200">
                @foreach(range(now()->year, now()->year - 4) as $y)
                    <option value="{{ $y }}" @selected($y == $tahun)>{{ $y }}</option>
                @endforeach
            </select>
        </form>
        <a href="{{ route('admin.laporan.export-excel', ['tahun' => $tahun]) }}"
           class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white
                  px-3 py-1.5 text-sm font-medium hover:bg-gray-50 transition-colors">
            <x-icon name="download" class="w-4 h-4" />
            {{ __('Export Excel') }}
        </a>
    </x-slot:actions>
</x-page-header>

{{-- Summary Cards --}}
<div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mb-6">
    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="mb-3 grid h-10 w-10 place-items-center rounded-lg bg-green-100 text-green-600">
            <x-icon name="trending-up" class="w-5 h-5" />
        </div>
        <p class="text-xs font-medium text-gray-500">{{ __('Total Pendapatan') }}</p>
        <p class="mt-1 text-2xl font-bold tabular-nums text-gray-900">
            Rp {{ number_format($ringkasan['total_pendapatan'], 0, ',', '.') }}
        </p>
    </div>
    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="mb-3 grid h-10 w-10 place-items-center rounded-lg bg-yellow-100 text-yellow-600">
            <x-icon name="trending-down" class="w-5 h-5" />
        </div>
        <p class="text-xs font-medium text-gray-500">{{ __('Total Selesai') }}</p>
        <p class="mt-1 text-2xl font-bold tabular-nums text-gray-900">
            {{ $ringkasan['total_selesai'] }} {{ __('pemesanan') }}
        </p>
    </div>
    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="mb-3 grid h-10 w-10 place-items-center rounded-lg bg-blue-100 text-blue-600">
            <x-icon name="chart-bar" class="w-5 h-5" />
        </div>
        <p class="text-xs font-medium text-gray-500">{{ __('Dibatalkan') }}</p>
        <p class="mt-1 text-2xl font-bold tabular-nums text-gray-900">
            {{ $ringkasan['total_dibatalkan'] }} {{ __('pemesanan') }}
        </p>
    </div>
</div>

{{-- Chart --}}
<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm mb-6">
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-sm font-semibold text-gray-900">
            {{ __('Pendapatan Bulanan') }} {{ $tahun }}
        </h2>
        <div class="flex items-center gap-3 text-xs text-gray-400">
            <span class="inline-flex items-center gap-1">
                <span class="h-2 w-2 rounded-full bg-blue-600"></span>{{ __('Pendapatan') }}
            </span>
        </div>
    </div>
    <canvas id="chart-pendapatan" height="80"></canvas>
</div>

{{-- Top Mobil --}}
<div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
        <h2 class="text-sm font-semibold text-gray-900">{{ __('Top 5 Mobil Terlaris') }}</h2>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr class="text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                <th class="px-4 py-3">No</th>
                <th class="px-4 py-3">{{ __('Kendaraan') }}</th>
                <th class="px-4 py-3 text-right">{{ __('Total Sewa') }}</th>
                <th class="px-4 py-3 text-right hidden sm:table-cell">{{ __('Pendapatan') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topMobil as $i => $m)
            <tr class="border-t border-gray-100 hover:bg-gray-50 transition-colors">
                <td class="px-4 py-3 font-bold text-gray-300">{{ $i + 1 }}</td>
                <td class="px-4 py-3 font-medium text-gray-900">{{ $m->nama }}</td>
                <td class="px-4 py-3 text-right tabular-nums text-gray-700">
                    {{ $m->total_sewa ?? 0 }}x
                </td>
                <td class="px-4 py-3 text-right tabular-nums font-medium text-gray-900
                           hidden sm:table-cell">
                    Rp {{ number_format($m->total_pendapatan ?? 0, 0, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4">
                    <x-empty-state icon="chart-bar" title="{{ __('Belum ada data') }}"
                        description="{{ __('Data akan muncul setelah ada pemesanan selesai.') }}" />
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    fetch('{{ route('admin.laporan.chart-data', ['tahun' => $tahun]) }}')
        .then(r => r.json())
        .then(data => {
            new Chart(document.getElementById('chart-pendapatan'), {
                type: 'bar',
                data: {
                    labels: ['{{ __("Jan") }}','{{ __("Feb") }}','{{ __("Mar") }}','{{ __("Apr") }}','{{ __("Mei") }}','{{ __("Jun") }}',
                             '{{ __("Jul") }}','{{ __("Agu") }}','{{ __("Sep") }}','{{ __("Okt") }}','{{ __("Nov") }}','{{ __("Des") }}'],
                    datasets: [{
                        label: '{{ __("Pendapatan") }}',
                        data: data.pendapatan_per_bulan,
                        backgroundColor: 'rgba(59, 130, 246, 0.7)',
                        borderRadius: 6,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            ticks: {
                                callback: v => 'Rp ' + (v/1e6).toFixed(0) + 'jt',
                                font: { size: 11 }
                            },
                            grid: { color: '#f1f4fa' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 } }
                        }
                    }
                }
            });
        });
});
</script>
@endpush