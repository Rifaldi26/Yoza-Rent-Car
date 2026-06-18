<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Yoza Rent Car') — {{ __('Rental Mobil Terpercaya') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-screen bg-[#f4f6fb] font-inter antialiased"
      x-data="{ drawerOpen: false }">

    {{-- ── Navbar ──────────────────────────────────────────── --}}
    <header class="sticky top-0 z-40 border-b border-[#e5e9f2] bg-white/95 backdrop-blur">
        <div class="mx-auto flex h-16 max-w-7xl items-center gap-3 px-4">

            {{-- Hamburger (mobile) --}}
            <button @click="drawerOpen = true"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg border
                           border-[#e5e9f2] md:hidden"
                    aria-label="{{ __('Buka menu') }}">
                <x-icon name="menu" class="w-5 h-5 text-[#18213a]" />
            </button>

            {{-- Brand --}}
            <a class="flex items-center gap-2.5">
                <div class="grid h-9 w-9 flex-shrink-0 place-items-center rounded-lg
                            bg-[#3b6fd4] text-white">
                    <x-icon name="car" class="w-5 h-5" />
                </div>
                <span class="whitespace-nowrap text-sm font-bold text-[#18213a]">Yoza Rent Car</span>
            </a>

            {{-- Desktop Nav --}}
            <nav class="ml-6 hidden items-center gap-1 md:flex">
                @auth
                <a href="{{ route('dashboard') }}"
                   class="rounded-lg px-3 py-1.5 text-sm font-medium transition-colors
                          {{ request()->routeIs('dashboard')
                              ? 'bg-[#eef2fb] text-[#3b6fd4]'
                              : 'text-[#7a8499] hover:bg-[#f1f4fa] hover:text-[#18213a]' }}">
                    {{ __('Dashboard') }}
                </a>
                @endauth
                <a href="{{ route('home') }}"
                   class="rounded-lg px-3 py-1.5 text-sm font-medium transition-colors
                          {{ request()->routeIs('home')
                              ? 'bg-[#eef2fb] text-[#3b6fd4]'
                              : 'text-[#7a8499] hover:bg-[#f1f4fa] hover:text-[#18213a]' }}">
                    {{ __('Katalog') }}
                </a>
                @auth
                <a href="{{ route('pemesanan.index') }}"
                   class="rounded-lg px-3 py-1.5 text-sm font-medium transition-colors
                          {{ request()->routeIs('pemesanan.*')
                              ? 'bg-[#eef2fb] text-[#3b6fd4]'
                              : 'text-[#7a8499] hover:bg-[#f1f4fa] hover:text-[#18213a]' }}">
                    {{ __('Pemesanan Saya') }}
                </a>
                <a href="{{ route('favorit.index') }}"
                   class="rounded-lg px-3 py-1.5 text-sm font-medium transition-colors
                          {{ request()->routeIs('favorit.*')
                              ? 'bg-[#eef2fb] text-[#3b6fd4]'
                              : 'text-[#7a8499] hover:bg-[#f1f4fa] hover:text-[#18213a]' }}">
                    {{ __('Favorit') }}
                </a>
                <a href="{{ route('chat.index') }}"
                   class="rounded-lg px-3 py-1.5 text-sm font-medium transition-colors
                          {{ request()->routeIs('chat.*')
                              ? 'bg-[#eef2fb] text-[#3b6fd4]'
                              : 'text-[#7a8499] hover:bg-[#f1f4fa] hover:text-[#18213a]' }}">
                    Chat
                </a>
                @endauth
            </nav>

            {{-- Right --}}
            <div class="ml-auto flex items-center gap-2">

                {{-- Lang Toggle --}}
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

                @auth
                    {{-- Notifikasi --}}
                    <div class="relative" x-data="{ open: false }">
                        <a href="{{ route('notifikasi.index') }}"
                           class="relative inline-flex h-9 w-9 items-center justify-center
                                  rounded-lg border border-[#e5e9f2] hover:bg-[#f1f4fa] transition-colors">
                            <x-icon name="bell" class="w-4 h-4 text-[#18213a]" />
                            @if(auth()->user()->unreadNotifikasi() > 0)
                                <span class="absolute -right-1 -top-1 grid h-4 min-w-4 place-items-center
                                             rounded-full bg-[#3b6fd4] px-1 text-[10px] font-bold text-white">
                                    {{ auth()->user()->unreadNotifikasi() }}
                                </span>
                            @endif
                        </a>
                    </div>

                    {{-- Profile --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                                class="flex items-center gap-2 rounded-lg px-2 py-1.5
                                       hover:bg-[#f1f4fa] transition-colors">
                            <x-avatar :name="auth()->user()->name" size="sm" />
                            <span class="hidden text-sm font-medium text-[#18213a] md:block max-w-[100px] truncate">
                                {{ auth()->user()->name }}
                            </span>
                            <x-icon name="chevron-down" class="hidden w-3.5 h-3.5 text-[#7a8499] md:block" />
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition
                             class="absolute right-0 top-full mt-2 w-52 rounded-xl border border-[#e5e9f2]
                                    bg-white py-1 shadow-lg z-50"
                             x-cloak>
                            <div class="border-b border-[#e5e9f2] px-4 py-2.5">
                                <p class="text-sm font-semibold text-[#18213a] truncate">
                                    {{ auth()->user()->name }}
                                </p>
                                <p class="text-xs text-[#7a8499] truncate">{{ auth()->user()->email }}</p>
                            </div>
                            <a href="{{ route('profil.edit') }}"
                               class="flex items-center gap-2 px-4 py-2.5 text-sm text-[#18213a]
                                      hover:bg-[#f1f4fa] transition-colors">
                                <x-icon name="user" class="w-4 h-4 text-[#7a8499]" />
                                {{ __('Profil Saya') }}
                            </a>
                            <!-- @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}"
                                   class="flex items-center gap-2 px-4 py-2.5 text-sm text-[#3b6fd4]
                                          hover:bg-[#eef2fb] transition-colors">
                                    <x-icon name="shield" class="w-4 h-4" />
                                    {{ __('Panel Admin') }}
                                </a>
                            @endif -->
                            <div class="border-t border-[#e5e9f2] mt-1 pt-1">
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

                @else
                    <button @click="$dispatch('open-login')"
                            class="rounded-lg border border-[#e5e9f2] bg-white px-3 py-1.5 text-sm font-medium text-[#18213a] hover:bg-[#f1f4fa] transition-colors">
                        {{ __('Masuk') }}
                    </button>
                    <button @click="$dispatch('open-register')"
                            class="rounded-lg bg-[#3b6fd4] px-3 py-1.5 text-sm font-medium text-white hover:bg-[#2e5bb8] transition-colors">
                        {{ __('Daftar') }}
                    </button>
                @endauth
            </div>
        </div>
    </header>

    {{-- ── Mobile Drawer ────────────────────────────────────── --}}
    <div x-show="drawerOpen" class="fixed inset-0 z-50 md:hidden" x-cloak>
        <div class="absolute inset-0 bg-[#18213a]/40" @click="drawerOpen = false"></div>
        <aside class="absolute left-0 top-0 h-full w-72 max-w-[85%] overflow-y-auto bg-white shadow-xl">

            <div class="flex items-center justify-between border-b border-[#e5e9f2] p-4">
                <div class="flex items-center gap-2.5">
                    <div class="grid h-9 w-9 place-items-center rounded-lg bg-[#3b6fd4] text-white">
                        <x-icon name="car" class="w-5 h-5" />
                    </div>
                    <span class="text-sm font-bold text-[#18213a]">Yoza Rent Car</span>
                </div>
                <button @click="drawerOpen = false"
                        class="grid h-8 w-8 place-items-center rounded-lg hover:bg-[#f1f4fa]">
                    <x-icon name="x" class="w-5 h-5 text-[#7a8499]" />
                </button>
            </div>

            <nav class="space-y-0.5 p-3">
                @auth
                    <a href="{{ route('dashboard') }}" @click="drawerOpen=false"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium
                              text-[#18213a] hover:bg-[#f1f4fa]">
                        <x-icon name="chart-bar" class="w-4 h-4" />
                        {{ __('Dashboard') }}
                    </a>
                @endauth
                <a href="{{ route('home') }}" @click="drawerOpen=false"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium
                          {{ request()->routeIs('home') ? 'bg-[#eef2fb] text-[#3b6fd4]'
                          : 'text-[#18213a] hover:bg-[#f1f4fa]' }}">
                    <x-icon name="home" class="w-4 h-4" />
                    {{ __('Katalog Mobil') }}
                </a>
                @auth
                    <a href="{{ route('pemesanan.index') }}" @click="drawerOpen=false"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium
                              text-[#18213a] hover:bg-[#f1f4fa]">
                        <x-icon name="calendar" class="w-4 h-4" />
                        {{ __('Pemesanan Saya') }}
                    </a>
                    <a href="{{ route('favorit.index') }}" @click="drawerOpen=false"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium
                              text-[#18213a] hover:bg-[#f1f4fa]">
                        <x-icon name="heart" class="w-4 h-4" />
                        {{ __('Favorit') }}
                    </a>
                    <a href="{{ route('chat.index') }}" @click="drawerOpen=false"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium
                              text-[#18213a] hover:bg-[#f1f4fa]">
                        <x-icon name="chat" class="w-4 h-4" />
                        {{ __('Chat') }}
                    </a>
                    <div class="border-t border-[#e5e9f2] pt-2 mt-2">
                        <a href="{{ route('profil.edit') }}" @click="drawerOpen=false"
                           class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium
                                  text-[#18213a] hover:bg-[#f1f4fa]">
                            <x-icon name="user" class="w-4 h-4" />
                            {{ __('Profil Saya') }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" @click="drawerOpen=false"
                                    class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5
                                           text-sm font-medium text-red-600 hover:bg-red-50">
                                <x-icon name="logout" class="w-4 h-4" />
                                {{ __('Keluar') }}
                            </button>
                        </form>
                    </div>
                @else
                    <button @click="$dispatch('open-login')"
                            class="rounded-lg border border-[#e5e9f2] bg-white px-3 py-1.5 text-sm font-medium text-[#18213a] hover:bg-[#f1f4fa] transition-colors">
                        {{ __('Masuk') }}
                    </button>
                    <button @click="$dispatch('open-register')"
                            class="rounded-lg bg-[#3b6fd4] px-3 py-1.5 text-sm font-medium text-white hover:bg-[#2e5bb8] transition-colors">
                        {{ __('Daftar') }}
                    </button>
                @endauth
            </nav>
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
                <x-alert type="warning" dismissible>
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <span>Silakan verifikasi email Anda untuk mendapatkan notifikasi lengkap.</span>
                    <a href="{{ route('verification.notice') }}"
                    class="inline-flex items-center justify-center rounded-lg bg-yellow-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-yellow-600">Verifikasi Sekarang</a>
        </div>
    </x-alert>
@elseif(session('warning'))
    <x-alert type="warning" dismissible>
        {{ session('warning') }}

                </x-alert>
            @endif
        </div>
    @endif

    {{-- ── Content ──────────────────────────────────────────── --}}
    <main class="pb-24 md:pb-0">
        @yield('content')
    </main>

    {{-- ── Bottom Nav Mobile (hanya saat login) ────────────── --}}
    <!-- @auth
    <nav class="fixed bottom-0 left-0 right-0 z-30 border-t border-[#e5e9f2]
                bg-white/95 backdrop-blur md:hidden">
        @php
        $bnav = [
            ['route' => 'home',              'icon' => 'home',     'label' => 'Katalog'],
            ['route' => 'pemesanan.index',   'icon' => 'calendar', 'label' => 'Pesan'],
            ['route' => 'favorit.index',     'icon' => 'heart',    'label' => 'Favorit'],
            ['route' => 'chat.index',        'icon' => 'chat',     'label' => 'Chat'],
            ['route' => 'notifikasi.index',  'icon' => 'bell',     'label' => 'Notif'],
        ];
        @endphp
        <ul class="mx-auto grid max-w-7xl grid-cols-5">
            @foreach($bnav as $item)
                @php $active = request()->routeIs($item['route']); @endphp
                <li>
                    <a href="{{ route($item['route']) }}"
                       class="flex flex-col items-center justify-center gap-0.5 py-2.5
                              text-[10px] font-medium transition-colors
                              {{ $active ? 'text-[#3b6fd4]' : 'text-[#7a8499] hover:text-[#18213a]' }}">
                        <x-icon :name="$item['icon']" class="w-5 h-5" />
                        {{ $item['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>
    @endauth -->
    @guest
        <x-auth-modal />
    @endguest
    @stack('scripts')
</body>
</html>