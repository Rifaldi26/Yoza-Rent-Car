@extends('layouts.app')
@section('title', __('Chat'))

@section('content')
<div class="mx-auto max-w-3xl px-4 py-8">

    <div class="mb-4">
        <h1 class="text-2xl font-bold text-[#18213a]">{{ __('Chat dengan Admin') }}</h1>
        <p class="mt-1 text-sm text-[#7a8499]">{{ __('Tanya atau lampirkan pemesanan Anda langsung ke admin.') }}</p>
    </div>

    <div class="overflow-hidden rounded-2xl border border-[#e5e9f2] bg-white shadow-sm"
         x-data="userChat({{ $admin->id ?? 0 }}, {{ auth()->id() }})">

        {{-- Chat Header --}}
        <div class="flex items-center gap-3 border-b border-[#e5e9f2] p-4">
            <x-avatar :name="$admin->name ?? 'Admin'" size="sm" />
            <div>
                <p class="text-sm font-semibold text-[#18213a]">{{ $admin->name ?? __('Admin Yoza Rent Car') }}</p>
                <p class="text-xs text-green-600 flex items-center gap-1">
                    <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                    {{ __('Online') }}
                </p>
            </div>
        </div>

        {{-- Area Pesan --}}
        <div x-ref="chatArea"
             class="h-[420px] overflow-y-auto space-y-3 bg-[#f4f6fb]/50 p-4">
            <template x-for="msg in pesan" :key="msg.id">
                <div :class="msg.pengirim_id === myId ? 'justify-end' : 'justify-start'"
                     class="flex">
                    <div :class="msg.pengirim_id === myId
                                     ? 'rounded-br-sm bg-[#3b6fd4] text-white'
                                     : 'rounded-bl-sm border border-[#e5e9f2] bg-white text-[#18213a]'"
                         class="max-w-[80%] rounded-2xl px-3 py-2 text-sm shadow-sm">

                        {{-- Lampiran Pemesanan --}}
                        <template x-if="msg.pemesanan">
                            <div class="mb-2 w-64 rounded-xl border border-[#e5e9f2] bg-white p-3 text-[#18213a]">
                                <div class="flex items-center gap-1.5 text-xs text-[#7a8499] mb-1">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125"/>
                                    </svg>
                                    Lampiran Pemesanan
                                </div>
                                <p class="font-mono text-xs text-[#3b6fd4]" x-text="'#' + msg.pemesanan.id"></p>
                                <p class="font-semibold" x-text="msg.pemesanan.nama_mobil"></p>
                                <p class="text-xs text-[#7a8499]"
                                   x-text="msg.pemesanan.tanggal_mulai + ' — ' + msg.pemesanan.tanggal_selesai"></p>
                                <div class="mt-2 flex items-center justify-between border-t border-[#e5e9f2] pt-2">
                                    <span class="text-[11px] font-medium text-yellow-600" x-text="msg.pemesanan.status"></span>
                                    <span class="text-sm font-bold" x-text="'Rp ' + msg.pemesanan.total_harga"></span>
                                </div>
                            </div>
                        </template>

                        <span x-text="msg.isi"></span>
                        <span class="ml-1.5 text-[10px] opacity-60" x-text="msg.waktu"></span>
                    </div>
                </div>
            </template>

            <template x-if="pesan.length === 0">
                <div class="flex h-full flex-col items-center justify-center py-12 text-center">
                    <div class="grid h-12 w-12 place-items-center rounded-full bg-[#eef2fb] mb-3">
                        <svg class="h-6 w-6 text-[#3b6fd4]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-[#18213a]">{{ __('Mulai percakapan') }}</p>
                    <p class="text-xs text-[#7a8499] mt-1">{{ __('Tanyakan apa saja atau lampirkan pemesanan Anda') }}</p>
                </div>
            </template>
        </div>

        {{-- Lampiran Preview --}}
        <template x-if="selectedPemesananId">
            <div class="mx-4 mt-0 mb-0 rounded-xl border border-[#3b6fd4]/30 bg-[#eef2fb] px-3 py-2 text-xs
                        flex items-center justify-between">
                <span class="text-[#3b6fd4] font-medium" x-text="'{{ __('Melampirkan: ') }}' + selectedPemesananLabel"></span>
                <button @click="selectedPemesananId = null; selectedPemesananLabel = ''"
                        class="text-[#7a8499] hover:text-red-500 transition-colors">
                    <x-icon name="x" class="w-3.5 h-3.5" />
                </button>
            </div>
        </template>

        {{-- Input --}}
        <div class="flex items-center gap-2 border-t border-[#e5e9f2] p-3">

            {{-- Lampiran Pemesanan --}}
            <div class="relative" x-data="{ showList: false }">
                <button type="button" @click="showList = !showList"
                        :class="selectedPemesananId ? 'border-[#3b6fd4] text-[#3b6fd4] bg-[#eef2fb]' : 'border-[#e5e9f2] text-[#7a8499] hover:bg-[#f1f4fa]'"
                        class="grid h-9 w-9 place-items-center rounded-lg border transition-colors">
                    <x-icon name="paper-clip" class="w-4 h-4" />
                </button>

                <div x-show="showList" @click.away="showList = false" x-cloak
                     class="absolute bottom-full left-0 mb-2 w-72 rounded-xl border border-[#e5e9f2]
                            bg-white shadow-lg z-20">
                    <div class="border-b border-[#e5e9f2] px-3 py-2">
                        <p class="text-xs font-semibold text-[#18213a]">{{ __('Lampirkan Pemesanan') }}</p>
                    </div>
                    <ul class="max-h-48 overflow-y-auto py-1">
                        @forelse(auth()->user()->pemesanans()->with('mobil')->latest()->take(10)->get() as $p)
                        <li>
                            <button type="button"
                                    @click="selectedPemesananId = {{ $p->id }}; selectedPemesananLabel = '#{{ $p->id }} {{ $p->mobil->nama }}'; showList = false"
                                    class="flex w-full items-start justify-between gap-2 px-3 py-2
                                           text-xs hover:bg-[#f4f6fb] transition-colors text-left">
                                <div>
                                    <p class="font-medium text-[#18213a]">
                                        #{{ $p->id }} — {{ $p->mobil->nama }}
                                    </p>
                                    <p class="text-[#7a8499]">{{ $p->labelStatus() }}</p>
                                </div>
                            </button>
                        </li>
                        @empty
                        <li class="px-3 py-4 text-center text-xs text-[#7a8499]">
                            {{ __('Tidak ada pemesanan') }}
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <input x-model="isiPesan"
                   @keydown.enter.prevent="kirim()"
                   placeholder="{{ __('Tulis pesan...') }}"
                   class="h-9 flex-1 rounded-lg border border-[#e5e9f2] bg-[#f4f6fb] px-3 text-sm
                          outline-none focus:border-[#3b6fd4] focus:ring-2 focus:ring-[#3b6fd4]/20
                          transition-colors">

            <button type="button" @click="kirim()"
                    :disabled="!isiPesan.trim()"
                    class="inline-flex h-9 items-center gap-1.5 rounded-lg bg-[#3b6fd4] px-3
                           text-sm font-medium text-white hover:bg-[#2e5bb8] disabled:opacity-40
                           disabled:cursor-not-allowed transition-colors">
                <x-icon name="send" class="w-4 h-4" />
                {{ __('Kirim') }}
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function userChat(adminId, myId) {
    return {
        adminId, myId,
        pesan: [],
        isiPesan: '',
        selectedPemesananId: null,
        selectedPemesananLabel: '',

        async init() {
            await this.loadPesan();
            if (typeof Echo !== 'undefined') {
                Echo.private(`chat.${myId}`)
                    .listen('PesanTerkirim', (e) => {
                        this.pesan.push(e.pesan);
                        this.scrollBottom();
                    });
            }
        },

        async loadPesan() {
            const r = await fetch(`/chat/${this.adminId}/pesan`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            this.pesan = await r.json();
            this.$nextTick(() => this.scrollBottom());
        },

        async kirim() {
            if (!this.isiPesan.trim()) return;
            const body = {
                isi: this.isiPesan,
                pemesanan_id: this.selectedPemesananId || null,
                _token: document.querySelector('meta[name=csrf-token]').content,
            };
            const r = await fetch(`/chat/${this.adminId}/kirim`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify(body),
            });
            const data = await r.json();
            this.pesan.push(data);
            this.isiPesan = '';
            this.selectedPemesananId = null;
            this.selectedPemesananLabel = '';
            this.$nextTick(() => this.scrollBottom());
        },

        scrollBottom() {
            const el = this.$refs.chatArea;
            if (el) el.scrollTop = el.scrollHeight;
        }
    }
}
</script>
@endpush

@endsection