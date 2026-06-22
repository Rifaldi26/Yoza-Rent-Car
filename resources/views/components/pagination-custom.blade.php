@if ($paginator->hasPages())
<nav role="navigation" aria-label="{{ __('Navigasi halaman') }}"
     class="flex flex-col items-center justify-between gap-3 sm:flex-row">

    {{-- Info jumlah data --}}
    <p class="text-xs text-[#7a8499]">
        {{ __('Menampilkan') }}
        <span class="font-medium text-[#18213a]">{{ $paginator->firstItem() }}</span>
        –
        <span class="font-medium text-[#18213a]">{{ $paginator->lastItem() }}</span>
        {{ __('dari') }}
        <span class="font-medium text-[#18213a]">{{ $paginator->total() }}</span>
        {{ __('hasil') }}
    </p>

    {{-- Tombol navigasi halaman --}}
    <div class="flex items-center gap-1">

        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span aria-disabled="true" aria-label="{{ __('Sebelumnya') }}"
                  class="grid h-8 w-8 cursor-not-allowed place-items-center rounded-lg
                         border border-[#e5e9f2] text-[#c7cbd6]">
                <x-icon name="chevron-left" class="w-4 h-4" />
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
               aria-label="{{ __('Sebelumnya') }}"
               class="grid h-8 w-8 place-items-center rounded-lg border border-[#e5e9f2]
                      text-[#7a8499] transition-colors hover:bg-[#f1f4fa] hover:text-[#18213a]">
                <x-icon name="chevron-left" class="w-4 h-4" />
            </a>
        @endif

        {{-- Nomor halaman --}}
        <div class="hidden items-center gap-1 sm:flex">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="grid h-8 w-8 place-items-center text-xs text-[#7a8499]">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page"
                                  class="grid h-8 w-8 place-items-center rounded-lg bg-primary-600
                                         text-xs font-semibold text-white">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                               class="grid h-8 w-8 place-items-center rounded-lg text-xs font-medium
                                      text-[#7a8499] transition-colors hover:bg-[#f1f4fa] hover:text-[#18213a]">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        {{-- Indikator halaman versi mobile (tanpa daftar nomor) --}}
        <span class="px-2 text-xs font-medium text-[#7a8499] sm:hidden">
            {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
        </span>

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next"
               aria-label="{{ __('Selanjutnya') }}"
               class="grid h-8 w-8 place-items-center rounded-lg border border-[#e5e9f2]
                      text-[#7a8499] transition-colors hover:bg-[#f1f4fa] hover:text-[#18213a]">
                <x-icon name="chevron-right" class="w-4 h-4" />
            </a>
        @else
            <span aria-disabled="true" aria-label="{{ __('Selanjutnya') }}"
                  class="grid h-8 w-8 cursor-not-allowed place-items-center rounded-lg
                         border border-[#e5e9f2] text-[#c7cbd6]">
                <x-icon name="chevron-right" class="w-4 h-4" />
            </span>
        @endif
    </div>
</nav>
@endif