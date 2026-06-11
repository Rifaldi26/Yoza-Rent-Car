@extends('layouts.guest')
@section('title', 'Konfirmasi Kata Sandi')

@section('content')
<div class="w-full max-w-md">
    <div class="overflow-hidden rounded-2xl border border-[#e5e9f2] bg-white shadow-sm">

        <div class="border-b border-[#e5e9f2] px-6 py-5">
            <div class="mx-auto mb-3 grid h-12 w-12 place-items-center rounded-2xl bg-[#eef2fb]">
                <svg class="h-6 w-6 text-[#3b6fd4]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/>
                </svg>
            </div>
            <h1 class="text-lg font-bold text-[#18213a] text-center">Konfirmasi Kata Sandi</h1>
            <p class="mt-1 text-sm text-[#7a8499] text-center">
                Ini adalah area aman. Konfirmasi kata sandi untuk melanjutkan.
            </p>
        </div>

        <div class="px-6 py-5">
            <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
                @csrf
                <div class="space-y-1">
                    <label class="block text-xs font-medium text-[#18213a]">Kata Sandi</label>
                    <input type="password" name="password"
                           placeholder="••••••••"
                           required autofocus
                           class="h-10 w-full rounded-xl border border-[#e5e9f2] bg-[#f4f6fb] px-3
                                  text-sm outline-none placeholder:text-[#aab0bf]
                                  focus:border-[#3b6fd4] focus:ring-2 focus:ring-[#3b6fd4]/20
                                  transition-colors
                                  @error('password') border-red-300 bg-red-50 @enderror">
                    @error('password')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit"
                        class="flex h-10 w-full items-center justify-center rounded-xl bg-[#3b6fd4]
                               text-sm font-semibold text-white hover:bg-[#2e5bb8] transition-colors">
                    Konfirmasi
                </button>
            </form>
        </div>
    </div>
</div>
@endsection