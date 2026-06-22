@extends('layouts.app')
@section('title', __('Pesan') . ' ' . $mobil->nama)

@section('content')
<div class="mx-auto max-w-4xl px-4 py-8">

    <div class="mb-4">
        <a href="{{ route('mobil.show', $mobil) }}"
           class="inline-flex items-center gap-1.5 text-sm text-[#7a8499] hover:text-[#18213a] transition-colors">
            <x-icon name="arrow-left" class="w-4 h-4" />
            {{ __('Kembali ke Detail Mobil') }}
        </a>
    </div>

    <h1 class="mb-6 text-2xl font-bold text-[#18213a]">{{ __('Form Pemesanan') }}</h1>

    <form method="POST" action="{{ route('pemesanan.store') }}"
          x-data="pemesananForm({{ $mobil->harga_per_hari }}, {{ $mobil->biaya_supir_per_hari ?? 0 }})">
    @csrf

        <input type="hidden" name="mobil_id" value="{{ $mobil->id }}">

        <div class="grid gap-4 lg:grid-cols-5">

            {{-- Form --}}
            <div class="lg:col-span-3 space-y-4">

                {{-- Info Mobil --}}
                <div class="rounded-2xl border border-[#e5e9f2] bg-white p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        @if($mobil->foto)
                            <img src="{{ Storage::url($mobil->foto) }}"
                                 class="h-16 w-24 rounded-xl object-cover flex-shrink-0"
                                 alt="{{ $mobil->nama }}">
                        @else
                            <div class="grid h-16 w-24 flex-shrink-0 place-items-center
                                        rounded-xl bg-primary-50">
                                <x-icon name="car" class="w-8 h-8 text-primary-600" />
                            </div>
                        @endif
                        <div>
                            <h3 class="font-semibold text-[#18213a]">{{ $mobil->nama }}</h3>
                            <p class="text-xs text-[#7a8499]">
                                {{ $mobil->merek }} &middot; {{ $mobil->plat_nomor }}
                            </p>
                            <p class="text-sm font-bold text-primary-600 mt-0.5">
                                Rp {{ number_format($mobil->harga_per_hari, 0, ',', '.') }} / hari
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Tanggal --}}
                <div class="rounded-2xl border border-[#e5e9f2] bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-semibold text-[#18213a] mb-4">{{ __('Periode Sewa') }}</h3>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <x-input name="tanggal_mulai" label="{{ __('Tanggal Mulai') }}" type="date"
                                :value="old('tanggal_mulai')"
                                :min="now()->format('Y-m-d')"
                                x-model="tanggalMulai"
                                @change="hitungHarga()"
                                required />
                        </div>
                        <div>
                            <x-input name="tanggal_selesai" label="{{ __('Tanggal Selesai') }}" type="date"
                                :value="old('tanggal_selesai')"
                                :min="now()->addDay()->format('Y-m-d')"
                                x-model="tanggalSelesai"
                                @change="hitungHarga()"
                                required />
                        </div>
                    </div>
                </div>

                {{-- Opsi Supir --}}
                @if($mobil->adaSupir())
                <div class="rounded-2xl border border-[#e5e9f2] bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-semibold text-[#18213a] mb-3">{{ __('Opsi Layanan') }}</h3>
                    <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-[#e5e9f2]
                                  p-4 hover:bg-[#f4f6fb] transition-colors"
                           :class="opsiSupir ? 'border-primary-600 bg-primary-50' : ''">
                        <input type="checkbox" name="opsi_supir" value="1"
                               x-model="opsiSupir"
                               @change="hitungHarga()"
                               class="mt-0.5 h-4 w-4 rounded border-[#e5e9f2] text-primary-600
                                      focus:ring-primary-600">
                        <div>
                            <p class="text-sm font-medium text-[#18213a]">{{ __('Sewa dengan Supir') }}</p>
                            <p class="text-xs text-[#7a8499] mt-0.5">
                                + Rp {{ number_format($mobil->biaya_supir_per_hari, 0, ',', '.') }} per hari
                            </p>
                        </div>
                    </label>
                </div>
                @endif

                {{-- Catatan --}}
                <div class="rounded-2xl border border-[#e5e9f2] bg-white p-5 shadow-sm">
                    <x-textarea name="catatan" label="{{ __('Catatan (Opsional)') }}"
                        placeholder="{{ __('Instruksi khusus, permintaan tambahan, dll.') }}"
                        rows="3" />
                </div>
            </div>

            {{-- Ringkasan Harga --}}
            <div class="lg:col-span-2">
                <div class="sticky top-24 rounded-2xl border border-[#e5e9f2] bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-semibold text-[#18213a] mb-4">{{ __('Ringkasan Biaya') }}</h3>

                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between text-[#7a8499]">
                            <span>{{ __('Durasi') }}</span>
                            <span class="font-medium text-[#18213a]" x-text="durasi > 0 ? durasi + ' hari' : '—'"></span>
                        </div>
                        <div class="flex justify-between text-[#7a8499]">
                            <span>{{ __('Sewa Mobil') }}</span>
                            <span class="tabular-nums" x-text="durasi > 0 ? 'Rp ' + formatRp(biayaSewa) : '—'"></span>
                        </div>
                        <template x-if="opsiSupir && durasi > 0">
                            <div class="flex justify-between text-[#7a8499]">
                                <span>{{ __('Jasa Supir') }}</span>
                                <span class="tabular-nums" x-text="'Rp ' + formatRp(biayaSupirTotal)"></span>
                            </div>
                        </template>
                        <div class="border-t border-[#e5e9f2] pt-2 flex justify-between font-semibold text-[#18213a]">
                            <span>{{ __('Total') }}</span>
                            <span class="text-primary-600 tabular-nums text-base"
                                  x-text="durasi > 0 ? 'Rp ' + formatRp(total) : '—'"></span>
                        </div>
                    </div>

                    <button type="submit"
                            :disabled="durasi <= 0"
                            class="mt-5 flex w-full items-center justify-center gap-2 rounded-xl
                                   bg-primary-600 py-3 text-sm font-semibold text-white
                                   hover:bg-primary-700 disabled:bg-gray-200 disabled:text-gray-400
                                   disabled:cursor-not-allowed transition-colors">
                        <x-icon name="calendar" class="w-4 h-4" />
                        {{ __('Lanjutkan ke Pembayaran') }}
                    </button>

                    <p class="mt-3 text-center text-xs text-[#7a8499]">
                        {{ __('Pembayaran akan dikonfirmasi via WhatsApp') }}
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function pemesananForm(hargaPerHari, biayaSupirPerHari) {
    return {
        tanggalMulai: '{{ old('tanggal_mulai') }}',
        tanggalSelesai: '{{ old('tanggal_selesai') }}',
        opsiSupir: {{ old('opsi_supir') ? 'true' : 'false' }},
        hargaPerHari, biayaSupirPerHari,
        durasi: 0, biayaSewa: 0, biayaSupirTotal: 0, total: 0,

        hitungHarga() {
            if (!this.tanggalMulai || !this.tanggalSelesai) return;
            const d1 = new Date(this.tanggalMulai);
            const d2 = new Date(this.tanggalSelesai);
            this.durasi = Math.max(0, Math.round((d2 - d1) / 86400000));
            this.biayaSewa = this.durasi * this.hargaPerHari;
            this.biayaSupirTotal = this.opsiSupir ? this.durasi * this.biayaSupirPerHari : 0;
            this.total = this.biayaSewa + this.biayaSupirTotal;
        },

        formatRp(n) {
            return n.toLocaleString('id-ID');
        }
    }
}
</script>
@endpush

@endsection