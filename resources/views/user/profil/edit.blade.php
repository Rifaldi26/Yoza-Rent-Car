@extends('layouts.app')
@section('title', __('Profil Saya'))

@section('content')
<div class="mx-auto max-w-xl px-4 py-8">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[#18213a]">{{ __('Profil Saya') }}</h1>
        <p class="mt-1 text-sm text-[#7a8499]">{{ __('Kelola informasi akun dan kata sandi Anda.') }}</p>
    </div>

    {{-- Avatar + Nama --}}
    <div class="mb-4 flex items-center gap-4 rounded-2xl border border-[#e5e9f2] bg-white p-4 shadow-sm">
        <x-avatar :name="$user->name" size="lg" />
        <div>
            <p class="font-semibold text-[#18213a]">{{ $user->name }}</p>
            <p class="text-sm text-[#7a8499]">{{ $user->email }}</p>
            @if($user->isAdmin())
                <span class="mt-1 inline-flex items-center gap-1 rounded-full bg-primary-50 px-2 py-0.5
                             text-[11px] font-medium text-primary-600">
                    <x-icon name="shield" class="w-3 h-3" />
                    {{ __('Administrator') }}
                </span>
            @endif
        </div>
    </div>

    {{-- Form Edit --}}
    <form method="POST" action="{{ route('profil.update') }}"
          class="space-y-4">
        @csrf @method('PATCH')

        <div class="rounded-2xl border border-[#e5e9f2] bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-[#18213a] mb-4">{{ __('Informasi Akun') }}</h3>
            <div class="space-y-3">
                <x-input name="name" label="{{ __('Nama Lengkap') }}"
                    :value="old('name', $user->name)" required />
                <div>
                    <label class="mb-1 block text-sm font-medium text-[#18213a]">Email</label>
                    <input type="email" value="{{ $user->email }}"
                           class="w-full rounded-xl border border-[#e5e9f2] bg-[#f1f4fa] px-3 py-2 text-sm text-[#7a8499] cursor-not-allowed"
                           disabled />
                    <p class="mt-1 text-xs text-[#7a8499]">{{ __('Email tidak dapat diubah.') }}</p>
                </div>
                <x-input name="no_hp" label="{{ __('Nomor HP') }}"
                    :value="old('no_hp', $user->no_hp)"
                    placeholder="081234567890" />
            </div>
        </div>

        <div class="rounded-2xl border border-[#e5e9f2] bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-[#18213a] mb-1">{{ __('Ganti Kata Sandi') }}</h3>
            <p class="text-xs text-[#7a8499] mb-4">{{ __('Kosongkan jika tidak ingin mengubah kata sandi.') }}</p>
            <div class="space-y-3">
                <x-input name="current_password" label="{{ __('Kata Sandi Saat Ini') }}"
                    type="password" autocomplete="current-password" />
                <x-input name="password" label="{{ __('Kata Sandi Baru') }}"
                    type="password" autocomplete="new-password" />
                <x-input name="password_confirmation" label="{{ __('Konfirmasi Kata Sandi Baru') }}"
                    type="password" autocomplete="new-password" />
            </div>
        </div>

        <button type="submit"
                class="flex w-full items-center justify-center gap-2 rounded-xl bg-primary-600
                       py-3 text-sm font-semibold text-white hover:bg-primary-700 transition-colors">
            <x-icon name="check-circle" class="w-4 h-4" />
            {{ __('Simpan Perubahan') }}
        </button>
    </form>
</div>
@endsection