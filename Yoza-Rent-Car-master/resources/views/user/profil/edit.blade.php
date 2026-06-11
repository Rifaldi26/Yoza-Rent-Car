@extends('layouts.app')
@section('title', 'Profil Saya')

@section('content')
<div class="mx-auto max-w-xl px-4 py-8">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[#18213a]">Profil Saya</h1>
        <p class="mt-1 text-sm text-[#7a8499]">Kelola informasi akun dan kata sandi Anda.</p>
    </div>

    {{-- Avatar + Nama --}}
    <div class="mb-4 flex items-center gap-4 rounded-2xl border border-[#e5e9f2] bg-white p-4 shadow-sm">
        <x-avatar :name="$user->name" size="lg" />
        <div>
            <p class="font-semibold text-[#18213a]">{{ $user->name }}</p>
            <p class="text-sm text-[#7a8499]">{{ $user->email }}</p>
            @if($user->isAdmin())
                <span class="mt-1 inline-flex items-center gap-1 rounded-full bg-[#eef2fb] px-2 py-0.5
                             text-[11px] font-medium text-[#3b6fd4]">
                    <x-icon name="shield" class="w-3 h-3" />
                    Administrator
                </span>
            @endif
        </div>
    </div>

    {{-- Form Edit --}}
    <form method="POST" action="{{ route('profil.update') }}"
          class="space-y-4">
        @csrf @method('PATCH')

        <div class="rounded-2xl border border-[#e5e9f2] bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-[#18213a] mb-4">Informasi Akun</h3>
            <div class="space-y-3">
                <x-input name="name" label="Nama Lengkap"
                    :value="old('name', $user->name)" required />
                <x-input name="email" label="Email" type="email"
                    :value="old('email', $user->email)" required />
                <x-input name="no_hp" label="Nomor HP"
                    :value="old('no_hp', $user->no_hp)"
                    placeholder="081234567890" />
            </div>
        </div>

        <div class="rounded-2xl border border-[#e5e9f2] bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-[#18213a] mb-1">Ganti Kata Sandi</h3>
            <p class="text-xs text-[#7a8499] mb-4">Kosongkan jika tidak ingin mengubah kata sandi.</p>
            <div class="space-y-3">
                <x-input name="current_password" label="Kata Sandi Saat Ini"
                    type="password" autocomplete="current-password" />
                <x-input name="password" label="Kata Sandi Baru"
                    type="password" autocomplete="new-password" />
                <x-input name="password_confirmation" label="Konfirmasi Kata Sandi Baru"
                    type="password" autocomplete="new-password" />
            </div>
        </div>

        <button type="submit"
                class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#3b6fd4]
                       py-3 text-sm font-semibold text-white hover:bg-[#2e5bb8] transition-colors">
            <x-icon name="check-circle" class="w-4 h-4" />
            Simpan Perubahan
        </button>
    </form>
</div>
@endsection