@extends('layouts.admin')
@section('title', __('Manajemen Halaman'))

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-[#18213a]">{{ __('Manajemen Halaman') }}</h2>
    </div>

    <div class="overflow-hidden rounded-xl border border-[#e5e9f2] bg-white shadow-sm">
        <table class="w-full text-left text-sm text-[#7a8499]">
            <thead class="border-b border-[#e5e9f2] bg-[#f8fafc] text-[#18213a]">
                <tr>
                    <th class="px-6 py-4 font-semibold">{{ __('Nama Halaman') }}</th>
                    <th class="px-6 py-4 font-semibold">{{ __('Terakhir Diperbarui') }}</th>
                    <th class="px-6 py-4 font-semibold text-right">{{ __('Aksi') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#e5e9f2]">
                @foreach($pages as $page)
                <tr class="hover:bg-[#f8fafc] transition-colors">
                    <td class="px-6 py-4 font-medium text-[#18213a]">{{ $page->title }}</td>
                    <td class="px-6 py-4">{{ $page->updated_at->translatedFormat('d M Y, H:i') }} WIB</td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.pages.edit', $page->slug) }}" 
                           class="inline-flex items-center gap-1.5 rounded-lg bg-primary-50 px-3 py-1.5 text-xs font-semibold text-primary-600 hover:bg-primary-600 hover:text-white transition-colors">
                            <x-icon name="pencil" class="w-3.5 h-3.5" />
                            {{ __('Edit') }}
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection