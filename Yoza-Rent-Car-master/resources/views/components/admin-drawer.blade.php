@php
$groups = [
    [
        'label' => null,
        'items' => [
            ['route'=>'admin.dashboard','icon'=>'chart-bar','label'=>__('Beranda'),
             'active'=> request()->routeIs('admin.dashboard')],
        ],
    ],
    [
        'label' => __('Operasional'),
        'items' => [
            ['route'=>'admin.pemesanan.index','icon'=>'calendar','label'=>__('Pemesanan'),
             'active'=> request()->routeIs('admin.pemesanan.*')],
            ['route'=>'admin.mobil.index',   'icon'=>'car',     'label'=>__('Armada Mobil'),
             'active'=> request()->routeIs('admin.mobil.*')],
            ['route'=>'admin.user.index',    'icon'=>'users',   'label'=>__('Pengguna'),
             'active'=> request()->routeIs('admin.user.*')],
        ],
    ],
    [
        'label' => __('Penjualan'),
        'items' => [
            ['route'=>'admin.laporan.index',   'icon'=>'trending-up','label'=>__('Laporan Penjualan'),
             'active'=> request()->routeIs('admin.laporan.*')],
            ['route'=>'admin.pembukuan.index', 'icon'=>'book-open',  'label'=>__('Pembukuan'),
             'active'=> request()->routeIs('admin.pembukuan.*')],
        ],
    ],
    [
        'label' => null,
        'items' => [
            ['route'=>'admin.chat.index','icon'=>'chat','label'=>__('Chat'),
             'active'=> request()->routeIs('admin.chat.*')],
        ],
    ],
];
@endphp

{{-- Header Drawer --}}
<div class="flex items-center justify-between border-b border-gray-200 p-4">
    <div class="flex items-center gap-2.5">
        <div class="grid h-9 w-9 place-items-center rounded-lg bg-blue-600 text-white">
            <x-icon name="car" class="w-5 h-5" />
        </div>
        <div class="leading-tight">
            <p class="text-sm font-bold text-gray-900">Yoza Rent Car</p>
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">{{ __('Panel Admin') }}</p>
        </div>
    </div>
    <button @click="drawerOpen = false"
            class="grid h-8 w-8 place-items-center rounded-lg hover:bg-gray-100 transition-colors">
        <x-icon name="x" class="w-5 h-5 text-gray-500" />
    </button>
</div>

{{-- Nav Groups --}}
<nav class="space-y-5 p-3">
    @foreach($groups as $group)
        <div>
            @if($group['label'])
                <p class="px-3 pb-1.5 text-[10px] font-semibold uppercase tracking-wider text-gray-400">
                    {{ $group['label'] }}
                </p>
            @endif
            <div class="space-y-0.5">
                @foreach($group['items'] as $item)
                    <a href="{{ route($item['route']) }}"
                       @click="drawerOpen = false"
                       class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium
                              transition-colors
                              {{ $item['active']
                                  ? 'bg-blue-600 text-white'
                                  : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600' }}">
                        <x-icon :name="$item['icon']" class="w-4 h-4" />
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </div>
        </div>
    @endforeach
</nav>
