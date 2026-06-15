@extends('layouts.admin')
@section('title', __('Pengguna'))

@section('content')

<x-page-header title="{{ __('Pengguna') }}" description="{{ __('Database pelanggan terdaftar.') }}" />

<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr class="text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                <th class="px-4 py-3">{{ __('Pelanggan') }}</th>
                <th class="px-4 py-3 hidden sm:table-cell">{{ __('Kontak') }}</th>
                <th class="px-4 py-3 text-right hidden md:table-cell">{{ __('Total Pemesanan') }}</th>
                <th class="px-4 py-3 text-right">{{ __('Status') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr class="border-t border-gray-100 hover:bg-gray-50 transition-colors">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <x-avatar :name="$user->name" size="sm" />
                        <div>
                            <p class="font-medium text-gray-900">{{ $user->name }}</p>
                            <p class="text-xs text-gray-400 sm:hidden">{{ $user->email }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 hidden sm:table-cell">
                    <div class="flex items-center gap-1 text-xs text-gray-500">
                        <x-icon name="user" class="w-3 h-3" />
                        {{ $user->email }}
                    </div>
                    @if($user->no_hp)
                    <div class="flex items-center gap-1 text-xs text-gray-400 mt-0.5">
                        <x-icon name="chat" class="w-3 h-3" />
                        {{ $user->no_hp }}
                    </div>
                    @endif
                </td>
                <td class="px-4 py-3 text-right tabular-nums hidden md:table-cell text-gray-900 font-medium">
                    {{ $user->pemesanans_count }}
                </td>
                <td class="px-4 py-3 text-right">
                    @if($user->email_verified_at)
                        <span class="inline-flex items-center gap-1 rounded-full border border-green-200
                                     bg-green-50 px-2 py-0.5 text-[11px] font-medium text-green-700">
                            <x-icon name="check-circle" class="w-3 h-3" />
                            {{ __('Terverifikasi') }}
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full border border-gray-200
                                     bg-gray-50 px-2 py-0.5 text-[11px] font-medium text-gray-500">
                            {{ __('Belum verifikasi') }}
                        </span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4">
                    <x-empty-state icon="users" title="{{ __('Belum ada pengguna') }}"
                        description="{{ __('Pengguna yang mendaftar akan muncul di sini.') }}" />
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($users->hasPages())
        <div class="border-t border-gray-100 px-4 py-3">
            {{ $users->links() }}
        </div>
    @endif
</div>

@endsection