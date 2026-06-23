{{--
    Partial: _ulasan.blade.php
    Sisipkan di resources/views/user/mobil/show.blade.php,
    di dalam kolom kiri (.lg:col-span-3 .space-y-4), setelah blok deskripsi.

    Variabel yang dibutuhkan dari controller:
      $ulasans        → koleksi Ulasan (disetujui=true) untuk mobil ini, with('user'), latest
      $pemesananSelesai → Pemesanan milik auth user yang sudah selesai untuk mobil ini
                         dan belum punya ulasan (nullable)
      $ulasanSaya     → Ulasan milik auth user untuk mobil ini (nullable)
      $rataRating     → float rata-rata rating (sudah dihitung di controller)
      $jumlahUlasan   → int jumlah ulasan disetujui
--}}

{{-- ── Ringkasan Rating ──────────────────────────────────────────────────── --}}
<div class="rounded-2xl border border-[#e5e9f2] bg-white p-5">

    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-semibold text-[#18213a]">{{ __('Ulasan Penyewa') }}</h3>
        @if($jumlahUlasan > 0)
            <span class="text-xs text-[#7a8499]">{{ $jumlahUlasan }} {{ __('ulasan') }}</span>
        @endif
    </div>

    @if($jumlahUlasan > 0)
        {{-- Rata-rata bintang --}}
        <div class="flex items-center gap-3 mb-5">
            <span class="text-4xl font-bold text-[#18213a]">
                {{ number_format($rataRating, 1) }}
            </span>
            <div>
                <div class="flex gap-0.5">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-5 h-5 {{ $i <= round($rataRating) ? 'text-amber-400' : 'text-gray-200' }}"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
                <p class="text-xs text-[#7a8499] mt-0.5">{{ __('dari 5 bintang') }}</p>
            </div>
        </div>

        {{-- Bar distribusi --}}
        <div class="space-y-1.5 mb-5">
            @for($bintang = 5; $bintang >= 1; $bintang--)
                @php
                    $jumlah = $ulasans->where('rating', $bintang)->count();
                    $persen = $jumlahUlasan > 0 ? ($jumlah / $jumlahUlasan) * 100 : 0;
                @endphp
                <div class="flex items-center gap-2 text-xs">
                    <span class="w-2 text-right text-[#7a8499]">{{ $bintang }}</span>
                    <svg class="w-3 h-3 text-amber-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <div class="flex-1 h-1.5 rounded-full bg-[#f4f6fb] overflow-hidden">
                        <div class="h-full rounded-full bg-amber-400 transition-all"
                             style="width: {{ $persen }}%"></div>
                    </div>
                    <span class="w-5 text-right text-[#7a8499]">{{ $jumlah }}</span>
                </div>
            @endfor
        </div>
    @else
        <div class="py-4 text-center">
            <p class="text-sm text-[#7a8499]">{{ __('Belum ada ulasan untuk kendaraan ini.') }}</p>
        </div>
    @endif

    {{-- ── Form Tulis Ulasan ────────────────────────────────────────────── --}}
    @auth
        @if($ulasanSaya)
            {{-- Sudah pernah menulis ulasan --}}
            <div class="rounded-xl border border-[#e5e9f2] bg-[#f4f6fb] p-4 mt-2">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-semibold text-[#18213a]">{{ __('Ulasan Anda') }}</p>
                    <div class="flex gap-0.5">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-3.5 h-3.5 {{ $i <= $ulasanSaya->rating ? 'text-amber-400' : 'text-gray-200' }}"
                                 fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                </div>
                @if($ulasanSaya->komentar)
                    <p class="text-sm text-[#7a8499]">{{ $ulasanSaya->komentar }}</p>
                @endif
                @if(!$ulasanSaya->disetujui)
                    <p class="mt-2 text-xs text-amber-600 flex items-center gap-1">
                        <x-icon name="clock" class="w-3 h-3" />
                        {{ __('Menunggu persetujuan admin') }}
                    </p>
                @endif
            </div>

        @elseif($pemesananSelesai && ! auth()->user()->hasVerifiedEmail())
            {{-- Eligible, tapi email belum diverifikasi --}}
            <div class="mt-2 rounded-xl border border-amber-200 bg-amber-50 p-4">
                <p class="text-sm font-medium text-amber-800 flex items-center gap-1.5">
                    <x-icon name="warning" class="w-4 h-4 flex-shrink-0" />
                    {{ __('Verifikasi email Anda untuk menulis ulasan') }}
                </p>
                <p class="mt-1 text-xs text-amber-700">
                    {{ __('Kami sudah mengirim link verifikasi ke email Anda saat mendaftar.') }}
                </p>
                <a href="{{ route('verification.notice') }}"
                   class="mt-2 inline-flex items-center gap-1.5 text-xs font-semibold text-amber-800 hover:underline">
                    {{ __('Verifikasi Sekarang') }}
                </a>
            </div>

        @elseif($pemesananSelesai)
            {{-- Eligible menulis ulasan --}}
            <div class="mt-2" x-data="{ rating: 0, hover: 0 }">
                <p class="text-xs font-semibold text-[#18213a] mb-3">
                    {{ __('Bagikan Pengalaman Anda') }}
                </p>

                <form method="POST" action="{{ route('ulasan.store', $mobil) }}">
                    @csrf
                    <input type="hidden" name="pemesanan_id" value="{{ $pemesananSelesai->id }}">

                    {{-- Bintang interaktif --}}
                    <div class="flex gap-1 mb-3">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button"
                                    @click="rating = {{ $i }}"
                                    @mouseenter="hover = {{ $i }}"
                                    @mouseleave="hover = 0"
                                    class="focus:outline-none transition-transform hover:scale-110">
                                <svg class="w-8 h-8 transition-colors"
                                     :class="(hover || rating) >= {{ $i }} ? 'text-amber-400' : 'text-gray-200'"
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </button>
                        @endfor
                        <input type="hidden" name="rating" :value="rating">
                    </div>

                    @error('rating')
                        <p class="mb-2 text-xs text-red-500">{{ $message }}</p>
                    @enderror

                    <textarea name="komentar" rows="3"
                              placeholder="{{ __('Ceritakan pengalaman Anda (opsional)...') }}"
                              class="w-full rounded-xl border border-[#e5e9f2] bg-[#f4f6fb] px-3 py-2.5
                                     text-sm text-[#18213a] placeholder-[#b0b8cc]
                                     focus:border-primary-600 focus:bg-white focus:outline-none focus:ring-2
                                     focus:ring-primary-600/20 transition resize-none">{{ old('komentar') }}</textarea>

                    @error('komentar')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror

                    <button type="submit"
                            :disabled="rating === 0"
                            :class="rating === 0
                                ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                : 'bg-primary-600 text-white hover:bg-primary-700'"
                            class="mt-3 flex w-full items-center justify-center gap-2 rounded-xl
                                   py-2.5 text-sm font-semibold transition-colors">
                        <x-icon name="star" class="w-4 h-4" />
                        {{ __('Kirim Ulasan') }}
                    </button>
                </form>
            </div>

        @endif
    @else
        {{-- Guest --}}
        <div class="mt-3 rounded-xl border border-[#e5e9f2] bg-[#f4f6fb] p-3 text-center">
            <p class="text-xs text-[#7a8499]">
                <button @click="$dispatch('open-login')"
                        class="font-semibold text-primary-600 hover:underline">
                    {{ __('Masuk') }}
                </button>
                {{ __(' untuk menulis ulasan setelah menyewa.') }}
            </p>
        </div>
    @endauth

</div>

{{-- ── Daftar Ulasan ────────────────────────────────────────────────────── --}}
@if($ulasans->isNotEmpty())
<div class="rounded-2xl border border-[#e5e9f2] bg-white p-5 space-y-4">
    <h3 class="text-sm font-semibold text-[#18213a]">{{ __('Semua Ulasan') }}</h3>

    @foreach($ulasans as $ulasan)
        <div class="flex gap-3 {{ !$loop->last ? 'border-b border-[#f0f2f8] pb-4' : '' }}">
            <x-avatar :name="$ulasan->user->name" size="sm" class="flex-shrink-0 mt-0.5" />
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2 flex-wrap">
                    <span class="text-sm font-medium text-[#18213a]">
                        {{ $ulasan->user->name }}
                    </span>
                    <span class="text-xs text-[#b0b8cc]">
                        {{ $ulasan->created_at->diffForHumans() }}
                    </span>
                </div>
                <div class="flex gap-0.5 mt-0.5">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-3.5 h-3.5 {{ $i <= $ulasan->rating ? 'text-amber-400' : 'text-gray-200' }}"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
                @if($ulasan->komentar)
                    <p class="mt-1.5 text-sm text-[#7a8499] leading-relaxed">
                        {{ $ulasan->komentar }}
                    </p>
                @endif
            </div>
        </div>
    @endforeach
</div>
@endif