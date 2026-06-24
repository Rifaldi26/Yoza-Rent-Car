@extends('layouts.app')
@section('title', __('Chat'))

@section('content')
<div class="mx-auto max-w-3xl px-4 py-8">

    <div class="mb-4">
        <h1 class="text-2xl font-bold text-[#18213a]">{{ __('Chat dengan Admin') }}</h1>
        <p class="mt-1 text-sm text-[#7a8499]">{{ __('Tanya atau lampirkan pemesanan Anda langsung ke admin.') }}</p>
    </div>

    <div class="overflow-hidden rounded-xl border border-[#e5e9f2] bg-white shadow-sm flex flex-col"
         style="height: calc(100vh - 220px); min-height: 480px;"
         x-data="userChat({{ $admin->id ?? 0 }}, {{ auth()->id() }})">

        {{-- Chat Header --}}
        <div class="flex items-center gap-3 border-b border-[#e5e9f2] bg-white p-3 flex-shrink-0">
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
             class="flex-1 overflow-y-auto bg-gray-50/50 px-4 py-4">
            <div class="space-y-3">
                <template x-for="msg in pesan" :key="msg.id">
                    <div :class="msg.pengirim_id === myId ? 'justify-end' : 'justify-start'"
                         class="flex">
                        <div :class="msg.pengirim_id === myId
                                         ? 'rounded-br-sm bg-primary-600 text-white'
                                         : 'rounded-bl-sm border border-[#e5e9f2] bg-white text-[#18213a]'"
                             class="max-w-[78%] rounded-2xl px-3 py-2 text-sm shadow-sm">

                            {{-- Lampiran Pemesanan --}}
                            <template x-if="msg.pemesanan">
                                <a :href="msg.pemesanan.url"
                                   class="mb-2 block w-64 rounded-lg border border-gray-200 bg-white p-3 text-left">
                                    <div class="flex items-center gap-1.5 text-xs text-gray-400 mb-1">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125"/>
                                        </svg>
                                        {{ __('Lampiran Pemesanan') }}
                                    </div>
                                    <p class="font-mono text-xs text-primary-600" x-text="'#' + msg.pemesanan.id"></p>
                                    <p class="font-semibold text-gray-900" x-text="msg.pemesanan.nama_mobil"></p>
                                    <p class="text-xs text-gray-500" x-text="msg.pemesanan.tanggal_mulai + ' — ' + msg.pemesanan.tanggal_selesai"></p>
                                    <div class="mt-2 flex items-center justify-between border-t border-gray-100 pt-2">
                                        <span class="text-[11px] font-medium text-yellow-600" x-text="msg.pemesanan.status"></span>
                                        <span class="text-sm font-bold text-gray-900" x-text="'Rp ' + msg.pemesanan.total_harga"></span>
                                    </div>
                                </a>
                            </template>

                            <span x-text="msg.isi"></span>
                            <span class="ml-2 text-[10px] opacity-60" x-text="msg.waktu"></span>
                        </div>
                    </div>
                </template>

                <template x-if="pesan.length === 0">
                    <div class="flex flex-col items-center justify-center py-16 text-center">
                        <div class="grid h-14 w-14 place-items-center rounded-full bg-primary-50 mb-3">
                            <svg class="h-7 w-7 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133"/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-[#18213a]">{{ __('Mulai percakapan') }}</p>
                        <p class="text-xs text-[#7a8499] mt-1">{{ __('Tanyakan apa saja atau lampirkan pemesanan Anda') }}</p>
                    </div>
                </template>
            </div>
        </div>

        {{-- Lampiran Preview --}}
        <template x-if="selectedPemesananId">
            <div class="mx-3 mb-0 rounded-xl border border-primary-600/30 bg-primary-50 px-3 py-2 text-xs
                        flex items-center justify-between flex-shrink-0">
                <span class="text-primary-600 font-medium" x-text="'{{ __('Melampirkan: ') }}' + selectedPemesananLabel"></span>
                <button @click="selectedPemesananId = null; selectedPemesananLabel = ''"
                        class="text-[#7a8499] hover:text-red-500 transition-colors">
                    <x-icon name="x" class="w-3.5 h-3.5" />
                </button>
            </div>
        </template>

        {{-- Input --}}
        <div class="mt-auto flex items-center gap-2 border-t border-[#e5e9f2] bg-white p-3 flex-shrink-0">

            {{-- Lampiran Pemesanan --}}
            <div class="relative" x-data="{ showList: false }">
                <button type="button" @click="showList = !showList"
                        :class="selectedPemesananId ? 'border-primary-600 text-primary-600 bg-primary-50' : 'border-[#e5e9f2] text-[#7a8499] hover:bg-[#f1f4fa]'"
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
                                    <p class="font-medium text-[#18213a]">#{{ $p->id }} — {{ $p->mobil->nama }}</p>
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
                   class="h-9 flex-1 rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm
                          outline-none focus:border-primary-400 focus:ring-2 focus:ring-primary-200 transition-colors">

            <button type="button" @click="kirim()"
                    :disabled="!isiPesan.trim()"
                    class="inline-flex h-9 items-center gap-1.5 rounded-lg bg-primary-600 px-3
                           text-sm font-medium text-white hover:bg-primary-700 disabled:opacity-40
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
                        this.$nextTick(() => this.scrollBottom());
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