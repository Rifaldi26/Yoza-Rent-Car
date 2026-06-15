<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Yoza Rent Car Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-screen bg-gray-50 font-inter antialiased"
      x-data="{ drawerOpen: false }">

    {{-- ── Top Bar ──────────────────────────────────────────── --}}
    <header class="sticky top-0 z-40 border-b border-gray-200 bg-white/95 backdrop-blur
                   supports-[backdrop-filter]:bg-white/80">
        <div class="mx-auto flex h-16 max-w-7xl items-center gap-3 px-4">

            {{-- Hamburger (mobile) --}}
            <button @click="drawerOpen = true"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg border
                           border-gray-200 text-gray-700 md:hidden"
                    aria-label="{{ __('Buka menu') }}">
                <x-icon name="menu" class="w-5 h-5" />
            </button>

            {{-- Brand --}}
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                <div class="grid h-9 w-9 flex-shrink-0 place-items-center rounded-lg bg-blue-600 text-white">
                    <x-icon name="car" class="w-5 h-5" />
                </div>
                <div class="hidden flex-col leading-tight sm:flex">
                    <span class="whitespace-nowrap text-sm font-bold text-gray-900">Yoza Rent Car</span>
                    <span class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">
                        {{ __('Panel Admin') }}
                    </span>
                </div>
            </a>

            {{-- Desktop Nav --}}
            <nav class="ml-6 hidden items-center gap-0.5 md:flex">
                <x-admin-nav />
            </nav>

            {{-- Right Actions --}}
            <div class="ml-auto flex items-center gap-2">

                {{-- Language Toggle --}}
                {{-- Ganti button Alpine lama dengan ini: --}}
<div class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white
            px-2.5 py-1.5 text-xs font-semibold">
    <a href="{{ route('locale.switch', 'id') }}"
       class="{{ app()->getLocale() === 'id' ? 'text-blue-600' : 'text-gray-400 hover:text-gray-700' }}
              transition-colors">
        ID
    </a>
    <span class="text-gray-300">|</span>
    <a href="{{ route('locale.switch', 'en') }}"
       class="{{ app()->getLocale() === 'en' ? 'text-blue-600' : 'text-gray-400 hover:text-gray-700' }}
              transition-colors">
        EN
    </a>
</div>

                {{-- Notification Bell --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="relative inline-flex h-9 w-9 items-center justify-center
                                   rounded-lg border border-gray-200 text-gray-700
                                   hover:bg-gray-50 transition-colors">
                        <x-icon name="bell" class="w-4 h-4" />
                        <span id="admin-notif-badge"
                              data-route="{{ route('admin.notifikasi.unread-count') }}"
                              class="absolute -right-1 -top-1 hidden min-w-[16px] place-items-center
                                     rounded-full bg-blue-600 px-1 text-[10px] font-bold text-white">
                        </span>
                    </button>

                    <div x-show="open" @click.away="open = false" x-transition
                         class="absolute right-0 top-full mt-2 w-80 rounded-xl border border-gray-200
                                bg-white shadow-lg z-50"
                         x-cloak>
                        <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                            <span class="text-sm font-semibold text-gray-900">{{ __('Notifikasi') }}</span>
                            <a href="{{ route('admin.notifikasi.index') }}"
                               class="text-xs text-blue-600 hover:underline">{{ __('Lihat semua') }}</a>
                        </div>
                        <div class="max-h-72 divide-y divide-gray-50 overflow-y-auto">
                            @forelse(auth()->user()->notifikasis()->latest()->take(4)->get() as $notif)
                                <a href="{{ $notif->link ?? '#' }}"
                                   class="flex gap-3 px-4 py-3 hover:bg-gray-50 transition-colors">
                                    <div class="grid h-8 w-8 flex-shrink-0 place-items-center rounded-full
                                        {{ $notif->tipe === 'success' ? 'bg-green-100 text-green-600' :
                                           ($notif->tipe === 'warning' ? 'bg-yellow-100 text-yellow-600'
                                           : 'bg-blue-100 text-blue-600') }}">
                                        <x-icon name="{{ $notif->tipe === 'success' ? 'check-circle'
                                            : ($notif->tipe === 'warning' ? 'warning' : 'bell') }}"
                                            class="w-4 h-4" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ $notif->judul }}
                                        </p>
                                        <p class="truncate text-xs text-gray-500">{{ $notif->pesan }}</p>
                                        <p class="mt-0.5 text-[10px] text-gray-400">
                                            {{ $notif->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    @if(!$notif->dibaca)
                                        <div class="mt-2 h-2 w-2 flex-shrink-0 rounded-full bg-blue-500"></div>
                                    @endif
                                </a>
                            @empty
                                <div class="px-4 py-8 text-center text-sm text-gray-400">
                                    {{ __('Tidak ada notifikasi') }}
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Profile Dropdown --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="flex items-center gap-2 rounded-lg px-2 py-1.5
                                   hover:bg-gray-100 transition-colors">
                        <x-avatar :name="auth()->user()->name" size="sm" />
                        <span class="hidden text-sm font-medium text-gray-700 md:block max-w-[120px] truncate">
                            {{ auth()->user()->name }}
                        </span>
                        <x-icon name="chevron-down" class="hidden h-3.5 w-3.5 text-gray-400 md:block" />
                    </button>

                    <div x-show="open" @click.away="open = false" x-transition
                         class="absolute right-0 top-full mt-2 w-52 rounded-xl border border-gray-200
                                bg-white py-1 shadow-lg z-50"
                         x-cloak>
                        <div class="border-b border-gray-100 px-4 py-2.5">
                            <p class="text-sm font-semibold text-gray-900 truncate">
                                {{ auth()->user()->name }}
                            </p>
                            <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                        </div>
                        <a href="{{ route('admin.pages.index') }}"
                           class="flex items-center gap-2 px-4 py-2.5 text-sm text-[#18213a]
                                  hover:bg-gray-[#f1f4fa] transition-colors">
                            <x-icon name="cog" class="w-4 h-4" text-[#7a8499]"/>
                            Pengaturan
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="flex w-full items-center gap-2 px-4 py-2.5 text-sm
                                           text-red-600 hover:bg-red-50 transition-colors">
                                <x-icon name="logout" class="w-4 h-4" />
                                {{ __('Keluar') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- ── Mobile Drawer ────────────────────────────────────── --}}
    <div x-show="drawerOpen" class="fixed inset-0 z-50 md:hidden" x-cloak>
        <div class="absolute inset-0 bg-gray-900/40" @click="drawerOpen = false"></div>
        <aside class="absolute left-0 top-0 h-full w-72 max-w-[85%] overflow-y-auto bg-white shadow-xl">
            <x-admin-drawer />
        </aside>
    </div>

    {{-- ── Flash Messages ───────────────────────────────────── --}}
    @if(session('success') || session('error') || session('warning') || session('info'))
        <div class="mx-auto max-w-7xl px-4 pt-4">
            @if(session('success'))
                <x-alert type="success" dismissible>{{ session('success') }}</x-alert>
            @endif
            @if(session('error'))
                <x-alert type="error" dismissible>{{ session('error') }}</x-alert>
            @endif
            @if(session('warning'))
                <x-alert type="warning" dismissible>{{ session('warning') }}</x-alert>
            @endif
            @if(session('info'))
                <x-alert type="info" dismissible>{{ session('info') }}</x-alert>
            @endif
        </div>
    @endif

    {{-- ── Page Content ─────────────────────────────────────── --}}
    <main class="mx-auto max-w-7xl px-4 pb-24 pt-6 md:pb-10">
        @yield('content')
    </main>

    {{-- ── Bottom Nav (mobile) ─────────────────────────────── --}}
    <!-- <nav class="fixed bottom-0 left-0 right-0 z-30 border-t border-gray-200
                bg-white/95 backdrop-blur md:hidden">
        <ul class="mx-auto grid max-w-7xl grid-cols-5">
            @php
            $bottomNav = [
                ['route' => 'admin.dashboard',      'icon' => 'chart-bar',  'label' => 'Dasbor'],
                ['route' => 'admin.pemesanan.index', 'icon' => 'calendar',   'label' => 'Pesan'],
                ['route' => 'admin.mobil.index',     'icon' => 'car',        'label' => 'Mobil'],
                ['route' => 'admin.chat.index',      'icon' => 'chat',       'label' => 'Chat'],
                ['route' => 'admin.notifikasi.index','icon' => 'bell',       'label' => 'Notif'],
            ];
            @endphp
            @foreach($bottomNav as $item)
                @php $active = request()->routeIs($item['route']); @endphp
                <li>
                    <a href="{{ route($item['route']) }}"
                       class="flex flex-col items-center justify-center gap-0.5 py-2.5
                              text-[10px] font-medium transition-colors
                              {{ $active ? 'text-blue-600' : 'text-gray-400 hover:text-gray-700' }}">
                        <x-icon :name="$item['icon']" class="w-5 h-5" />
                        {{ $item['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>
    </nav> -->

    @stack('scripts')

    <script>
    // Notifikasi badge polling
    (function() {
        const badge = document.getElementById('admin-notif-badge');
        if (!badge) return;
        const url = badge.dataset.route;
        const update = async () => {
            try {
                const r = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const d = await r.json();
                const n = d.count || 0;
                badge.textContent = n > 99 ? '99+' : n;
                badge.style.display = n > 0 ? 'grid' : 'none';
            } catch(_) {}
        };
        update();
        setInterval(update, 15000);
    })();
    </script>
</body>
</html>