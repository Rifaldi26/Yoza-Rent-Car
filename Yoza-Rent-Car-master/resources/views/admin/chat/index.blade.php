@extends('layouts.admin')
@section('title', 'Chat')

@section('content')

<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm"
     x-data="adminChat()">

    <div class="grid h-[calc(100vh-220px)] min-h-[480px] grid-cols-1 md:grid-cols-[280px_1fr]">

        {{-- Daftar Percakapan --}}
        <aside class="border-r border-gray-100 flex flex-col">
            <div class="border-b border-gray-100 p-3">
                <div class="relative">
                    <x-icon name="search"
                        class="pointer-events-none absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <input placeholder="Cari pelanggan..."
                           class="h-9 w-full rounded-lg border border-gray-200 bg-gray-50 pl-8 pr-3
                                  text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-200">
                </div>
            </div>

            <ul class="flex-1 overflow-y-auto divide-y divide-gray-50">
                @forelse($users as $user)
                <li>
                    <button
                        @click="selectUser({{ $user->id }}, '{{ $user->name }}')"
                        :class="activeUserId === {{ $user->id }} ? 'bg-blue-50' : 'hover:bg-gray-50'"
                        class="flex w-full items-center gap-3 p-3 text-left transition-colors">
                        <div class="relative flex-shrink-0">
                            <x-avatar :name="$user->name" size="sm" />
                            @if($user->unread > 0)
                                <span class="absolute -right-1 -top-1 grid h-4 min-w-4 place-items-center
                                             rounded-full bg-blue-600 px-0.5 text-[10px] font-bold text-white">
                                    {{ $user->unread }}
                                </span>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-gray-900">
                                {{ $user->name }}
                            </p>
                        </div>
                    </button>
                </li>
                @empty
                <li class="p-6 text-center text-sm text-gray-400">
                    Belum ada percakapan
                </li>
                @endforelse
            </ul>
        </aside>

        {{-- Area Chat --}}
        <section class="flex flex-col">

            {{-- Header --}}
            <div x-show="activeUserId" class="flex items-center gap-3 border-b border-gray-100 p-3">
                <x-avatar :name="''" size="sm" x-bind:data-name="activeUserName" />
                <div>
                    <p class="text-sm font-semibold text-gray-900" x-text="activeUserName"></p>
                </div>
            </div>

            {{-- Placeholder --}}
            <div x-show="!activeUserId"
                 class="flex flex-1 flex-col items-center justify-center text-center p-6">
                <div class="grid h-14 w-14 place-items-center rounded-full bg-gray-100 mb-3">
                    <x-icon name="chat" class="w-7 h-7 text-gray-300" />
                </div>
                <p class="text-sm font-medium text-gray-500">Pilih percakapan untuk memulai</p>
            </div>

            {{-- Pesan --}}
            <div x-show="activeUserId"
                 x-ref="pesanArea"
                 class="flex-1 space-y-3 overflow-y-auto bg-gray-50/50 p-4">
                <template x-for="msg in pesan" :key="msg.id">
                    <div :class="msg.pengirim_id === {{ auth()->id() }} ? 'justify-end' : 'justify-start'"
                         class="flex">
                        <div :class="msg.pengirim_id === {{ auth()->id() }}
                                         ? 'rounded-br-sm bg-blue-600 text-white'
                                         : 'rounded-bl-sm border border-gray-200 bg-white text-gray-900'"
                             class="max-w-[78%] rounded-2xl px-3 py-2 text-sm shadow-sm">

                            {{-- Lampiran Pemesanan --}}
                            <template x-if="msg.pemesanan">
                                <a :href="msg.pemesanan.url"
                                   class="mb-2 block w-64 rounded-lg border border-gray-200 bg-white p-3 text-left">
                                    <div class="flex items-center gap-1.5 text-xs text-gray-400">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125"/></svg>
                                        Lampiran Pemesanan
                                    </div>
                                    <p class="mt-1 font-mono text-xs text-blue-600"
                                       x-text="'#' + msg.pemesanan.id"></p>
                                    <p class="font-semibold text-gray-900"
                                       x-text="msg.pemesanan.nama_mobil"></p>
                                    <p class="text-xs text-gray-500"
                                       x-text="msg.pemesanan.tanggal_mulai + ' — ' + msg.pemesanan.tanggal_selesai"></p>
                                    <div class="mt-2 flex items-center justify-between border-t border-gray-100 pt-2">
                                        <span class="text-[11px] font-medium text-yellow-600"
                                              x-text="msg.pemesanan.status"></span>
                                        <span class="text-sm font-bold text-gray-900"
                                              x-text="'Rp ' + msg.pemesanan.total_harga"></span>
                                    </div>
                                </a>
                            </template>

                            <span x-text="msg.isi"></span>
                            <span class="ml-2 text-[10px] opacity-60" x-text="msg.waktu"></span>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Input --}}
            <div x-show="activeUserId"
                 class="flex items-center gap-2 border-t border-gray-100 p-3">

                {{-- Lampirkan Pemesanan --}}
                <div class="relative" x-data="{ showList: false }">
                    <button @click="showList = !showList"
                            :class="selectedPemesananId ? 'border-blue-400 text-blue-600 bg-blue-50' : 'border-gray-200 text-gray-500 hover:bg-gray-100'"
                            class="grid h-9 w-9 place-items-center rounded-lg border transition-colors">
                        <x-icon name="paper-clip" class="w-4 h-4" />
                    </button>
                    {{-- Dropdown list pemesanan --}}
                    <div x-show="showList" @click.away="showList=false" x-cloak
                         class="absolute bottom-full left-0 mb-2 w-72 rounded-xl border border-gray-200
                                bg-white shadow-lg z-20">
                        <div class="border-b border-gray-100 px-3 py-2">
                            <p class="text-xs font-semibold text-gray-700">Lampirkan Pemesanan</p>
                        </div>
                        <ul class="max-h-48 overflow-y-auto py-1">
                            <template x-for="p in daftarPemesanan" :key="p.id">
                                <li>
                                    <button
                                        @click="selectedPemesananId = p.id; selectedPemesananLabel = '#' + p.id + ' ' + p.nama_mobil; showList = false"
                                        class="flex w-full items-center justify-between px-3 py-2
                                               text-xs hover:bg-gray-50 transition-colors">
                                        <div class="text-left">
                                            <p class="font-medium text-gray-900"
                                               x-text="'#' + p.id + ' — ' + p.nama_mobil"></p>
                                            <p class="text-gray-400" x-text="p.status"></p>
                                        </div>
                                    </button>
                                </li>
                            </template>
                            <template x-if="daftarPemesanan.length === 0">
                                <li class="px-3 py-4 text-center text-xs text-gray-400">
                                    Tidak ada pemesanan
                                </li>
                            </template>
                        </ul>
                        <template x-if="selectedPemesananId">
                            <div class="border-t border-gray-100 px-3 py-2">
                                <button @click="selectedPemesananId = null; selectedPemesananLabel = ''"
                                        class="text-xs text-red-500 hover:underline">
                                    Hapus lampiran
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <input x-model="isiPesan"
                       @keydown.enter.prevent="kirim()"
                       placeholder="Tulis pesan..."
                       class="h-9 flex-1 rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm
                              outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-200 transition-colors">

                <button @click="kirim()"
                        :disabled="!isiPesan.trim()"
                        class="inline-flex h-9 items-center gap-1.5 rounded-lg bg-blue-600 px-3
                               text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-40
                               disabled:cursor-not-allowed transition-colors">
                    <x-icon name="send" class="w-4 h-4" />
                    Kirim
                </button>
            </div>
        </section>

    </div>
</div>

@endsection

@push('scripts')
<script>
function adminChat() {
    return {
        activeUserId: null,
        activeUserName: '',
        pesan: [],
        isiPesan: '',
        selectedPemesananId: null,
        selectedPemesananLabel: '',
        daftarPemesanan: [],
        adminId: {{ auth()->id() }},

        selectUser(id, name) {
            this.activeUserId = id;
            this.activeUserName = name;
            this.isiPesan = '';
            this.selectedPemesananId = null;
            this.loadPesan();
            this.loadPemesananUser();
        },

        async loadPesan() {
            const r = await fetch(`/admin/chat/${this.activeUserId}/pesan`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            this.pesan = await r.json();
            this.$nextTick(() => {
                const el = this.$refs.pesanArea;
                if (el) el.scrollTop = el.scrollHeight;
            });
        },

        async loadPemesananUser() {
            // Ambil daftar pemesanan user aktif via endpoint show
            this.daftarPemesanan = [];
        },

        async kirim() {
            if (!this.isiPesan.trim()) return;
            const body = {
                isi: this.isiPesan,
                pemesanan_id: this.selectedPemesananId || null,
                _token: document.querySelector('meta[name=csrf-token]').content,
            };
            const r = await fetch(`/admin/chat/${this.activeUserId}/kirim`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify(body),
            });
            const data = await r.json();
            this.pesan.push(data);
            this.isiPesan = '';
            this.selectedPemesananId = null;
            this.$nextTick(() => {
                const el = this.$refs.pesanArea;
                if (el) el.scrollTop = el.scrollHeight;
            });
        },

        init() {
            // Listen Reverb
            if (typeof Echo !== 'undefined') {
                Echo.private(`chat.${this.adminId}`)
                    .listen('PesanTerkirim', (e) => {
                        if (e.pesan.pengirim_id === this.activeUserId) {
                            this.pesan.push(e.pesan);
                            this.$nextTick(() => {
                                const el = this.$refs.pesanArea;
                                if (el) el.scrollTop = el.scrollHeight;
                            });
                        }
                    });
            }
        }
    }
}
</script>
@endpush