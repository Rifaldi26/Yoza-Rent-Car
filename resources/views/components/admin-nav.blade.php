@php
$groups = [
    [
        'label' => 'Beranda',
        'single' => true,
        'route'  => 'admin.dashboard',
        'icon'   => 'chart-bar',
        'active' => request()->routeIs('admin.dashboard'),
    ],
    [
        'label'  => 'Operasional',
        'single' => false,
        'active' => request()->routeIs('admin.pemesanan.*','admin.mobil.*','admin.user.*'),
        'items'  => [
            ['route'=>'admin.pemesanan.index','icon'=>'calendar','label'=>'Pemesanan'],
            ['route'=>'admin.mobil.index',    'icon'=>'car',     'label'=>'Armada Mobil'],
            ['route'=>'admin.user.index',     'icon'=>'users',   'label'=>'Pengguna'],
        ],
    ],
    [
        'label'  => 'Penjualan',
        'single' => false,
        'active' => request()->routeIs('admin.laporan.*','admin.akuntansi.*'),
        'items'  => [
            ['route'=>'admin.laporan.index',   'icon'=>'trending-up','label'=>'Laporan'],
            ['route'=>'admin.akuntansi.index', 'icon'=>'book-open', 'label'=>'Akuntansi'],
        ],
    ],
    [
        'label'  => 'Chat',
        'single' => true,
        'route'  =>'admin.chat.index',
        'icon'   =>'chat',
        'active' => request()->routeIs('admin.chat.*'),
    ],
];
@endphp

@foreach($groups as $group)
    @if($group['single'])
        <a href="{{ route($group['route']) }}"
           class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-sm font-medium
                  transition-colors
                  {{ $group['active']
                      ? 'bg-blue-50 text-blue-600'
                      : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900' }}">
            <x-icon :name="$group['icon']" class="w-4 h-4" />
            {{ $group['label'] }}
        </a>
    @else
        <div class="group relative">
            <button class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-sm font-medium
                           transition-colors
                           {{ $group['active']
                               ? 'bg-blue-50 text-blue-600'
                               : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900' }}">
                {{ $group['label'] }}
                <x-icon name="chevron-down"
                    class="w-3.5 h-3.5 transition-transform duration-200 group-hover:rotate-180" />
            </button>

            {{-- Dropdown --}}
            <div class="invisible absolute left-0 top-full z-50 min-w-[200px] translate-y-1
                        rounded-xl border border-gray-100 bg-white p-1.5 opacity-0 shadow-lg
                        transition-all duration-150
                        group-hover:visible group-hover:translate-y-0 group-hover:opacity-100">
                @foreach($group['items'] as $item)
                    @php $itemActive = request()->routeIs($item['route'].'*'); @endphp
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm transition-colors
                              {{ $itemActive
                                  ? 'bg-blue-600 text-white'
                                  : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600' }}">
                        <x-icon :name="$item['icon']" class="w-4 h-4" />
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif
@endforeach