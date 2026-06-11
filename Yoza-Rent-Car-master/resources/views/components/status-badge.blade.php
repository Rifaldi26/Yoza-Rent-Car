@props(['status' => ''])

@php
$map = [
    'pending'                   => 'border-yellow-300 bg-yellow-50 text-yellow-700',
    'menunggu_konfirmasi_admin' => 'border-blue-200 bg-blue-50 text-blue-700',
    'dikonfirmasi'              => 'border-blue-300 bg-blue-100 text-blue-700',
    'selesai'                   => 'border-green-300 bg-green-50 text-green-700',
    'dibatalkan'                => 'border-red-300 bg-red-50 text-red-600',
    'kadaluarsa'                => 'border-orange-300 bg-orange-50 text-orange-600',
    'tersedia'                  => 'border-green-300 bg-green-50 text-green-700',
    'disewa'                    => 'border-blue-200 bg-blue-50 text-blue-700',
    'perawatan'                 => 'border-yellow-300 bg-yellow-50 text-yellow-700',
    'paid'                      => 'border-green-300 bg-green-50 text-green-700',
    'failed'                    => 'border-red-300 bg-red-50 text-red-600',
    'expired'                   => 'border-orange-300 bg-orange-50 text-orange-600',
];
$cls = $map[$status] ?? 'border-gray-200 bg-gray-50 text-gray-500';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full border px-2 py-0.5 text-[11px] font-medium $cls"]) }}>
    {{ $slot }}
</span>