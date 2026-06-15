@extends('layouts.guest')
@section('title', __('Reset Kata Sandi'))

@section('content')
<div class="w-full max-w-md">
    <div class="overflow-hidden rounded-2xl border border-[#e5e9f2] bg-white shadow-sm">

        <div class="border-b border-[#e5e9f2] px-6 py-5">
            <h1 class="text-lg font-bold text-[#18213a]">{{ __('Buat Kata Sandi Baru') }}</h1>
            <p class="mt-1 text-sm text-[#7a8499]">
                {{ __('Masukkan kata sandi baru untuk akun') }} <strong>{{ $email }}</strong>
            </p>
        </div>

        <div class="px-6 py-5">
            <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="space-y-1" x-data="{ show: false }">
                    <label class="block text-xs font-medium text-[#18213a]">{{ __('Kata Sandi Baru') }}</label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'"
                               name="password"
                               placeholder="{{ __('Min. 8 karakter') }}"
                               required autofocus
                               class="h-10 w-full rounded-xl border border-[#e5e9f2] bg-[#f4f6fb]
                                      px-3 pr-10 text-sm outline-none placeholder:text-[#aab0bf]
                                      focus:border-[#3b6fd4] focus:ring-2 focus:ring-[#3b6fd4]/20
                                      transition-colors
                                      @error('password') border-red-300 bg-red-50 @enderror">
                        <button type="button" @click="show = !show"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-[#7a8499]
                                       hover:text-[#18213a]">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-medium text-[#18213a]">{{ __('Konfirmasi Kata Sandi') }}</label>
                    <input type="password" name="password_confirmation"
                           placeholder="{{ __('Ulangi kata sandi baru') }}"
                           required
                           class="h-10 w-full rounded-xl border border-[#e5e9f2] bg-[#f4f6fb] px-3
                                  text-sm outline-none placeholder:text-[#aab0bf]
                                  focus:border-[#3b6fd4] focus:ring-2 focus:ring-[#3b6fd4]/20
                                  transition-colors">
                </div>

                <button type="submit"
                        class="flex h-10 w-full items-center justify-center rounded-xl bg-[#3b6fd4]
                               text-sm font-semibold text-white hover:bg-[#2e5bb8] transition-colors">
                    {{ __('Reset Kata Sandi') }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection