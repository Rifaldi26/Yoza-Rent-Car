@extends('layouts.guest')
@section('title', __('Verifikasi Email'))

@section('content')
<div class="w-full max-w-md">
    <div class="overflow-hidden rounded-2xl border border-[#e5e9f2] bg-white shadow-sm">

        <div class="border-b border-[#e5e9f2] px-6 py-5 text-center">
            <div class="mx-auto mb-3 grid h-14 w-14 place-items-center rounded-2xl bg-primary-50">
                <svg class="h-7 w-7 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
                </svg>
            </div>
            <h1 class="text-lg font-bold text-[#18213a]">{{ __('Verifikasi Email Kamu') }}</h1>
            <p class="mt-1 text-sm text-[#7a8499]">
                {{ __('Kami mengirim link verifikasi ke') }}<br>
                <span class="font-medium text-[#18213a]">{{ auth()->user()->email }}</span>
            </p>
        </div>

        <div class="px-6 py-5 space-y-3">

            @if(session('status') === 'verification-link-sent')
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 text-center">
                    {{ __('Link verifikasi baru telah dikirim ke email kamu.') }}
                </div>
            @endif

            <p class="text-center text-xs text-[#7a8499]">
                {{ __('Cek folder Spam jika tidak menemukan email dalam 2 menit.') }}
            </p>
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit"
                        class="flex h-10 w-full items-center justify-center rounded-xl bg-primary-600
                               text-sm font-semibold text-white hover:bg-primary-700 transition-colors">
                    {{ __('Kirim Ulang Email Verifikasi') }}
                </button>
            </form>
            <form method="GET" action="{{ url()->previous() }}">
                @csrf
                <button type="submit"
                        class="flex h-10 w-full items-center justify-center rounded-xl border
                               border-[#e5e9f2] text-sm font-medium text-[#7a8499]
                               hover:bg-[#f1f4fa] transition-colors">
                    {{ __('Kembali') }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection