@extends('layouts.guest')
@section('title', 'Lupa Kata Sandi')

@section('content')
<div class="w-full max-w-md">
    <div class="overflow-hidden rounded-2xl border border-[#e5e9f2] bg-white shadow-sm">

        <div class="border-b border-[#e5e9f2] px-6 py-5">
            <a href="{{ route('home') }}"
               class="mb-4 inline-flex items-center gap-1.5 text-xs text-[#7a8499] hover:text-[#18213a] transition-colors">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
                Kembali ke beranda
            </a>
            <h1 class="text-lg font-bold text-[#18213a]">Lupa Kata Sandi?</h1>
            <p class="mt-1 text-sm text-[#7a8499]">
                Masukkan email terdaftar. Kami akan kirimkan link untuk mereset kata sandi kamu.
            </p>
        </div>

        <div class="px-6 py-5">

            @if(session('status'))
                <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                @csrf

                <div class="space-y-1">
                    <label class="block text-xs font-medium text-[#18213a]">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           placeholder="nama@email.com"
                           required autofocus
                           class="h-10 w-full rounded-xl border border-[#e5e9f2] bg-[#f4f6fb] px-3
                                  text-sm outline-none placeholder:text-[#aab0bf]
                                  focus:border-[#3b6fd4] focus:ring-2 focus:ring-[#3b6fd4]/20
                                  transition-colors
                                  @error('email') border-red-300 bg-red-50 @enderror">
                    @error('email')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        class="flex h-10 w-full items-center justify-center rounded-xl bg-[#3b6fd4]
                               text-sm font-semibold text-white hover:bg-[#2e5bb8] transition-colors">
                    Kirim Link Reset
                </button>
            </form>
        </div>
    </div>
</div>
@endsection