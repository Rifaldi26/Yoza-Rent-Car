@extends('layouts.admin')
@section('title', __('Edit') . ' ' . $page->title)

@section('content')
<div class="space-y-6 max-w-4xl">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.pages.index') }}" class="grid h-9 w-9 place-items-center rounded-lg border border-[#e5e9f2] text-[#7a8499] hover:bg-[#f1f4fa]">
            <x-icon name="arrow-left" class="w-5 h-5" />
        </a>
        <h2 class="text-xl font-bold text-[#18213a]">{{ __('Edit') }} {{ $page->title }}</h2>
    </div>

    <form action="{{ route('admin.pages.update', $page->slug) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Judul --}}
        <div class="rounded-xl border border-[#e5e9f2] bg-white p-6 shadow-sm">
            <label class="mb-1.5 block text-sm font-medium text-[#18213a]">{{ __('Judul Halaman') }}</label>
            <input type="text" name="title" value="{{ old('title', $page->title) }}" required
                   class="w-full rounded-lg border border-[#e5e9f2] px-4 py-2.5 text-sm focus:border-primary-600 focus:ring-1 focus:ring-primary-600 outline-none transition-all">
        </div>

        {{-- Sections --}}
        @php $content = json_decode($page->content, true); @endphp

        <div id="sections-wrapper" class="space-y-6">
            @foreach($content['sections'] as $si => $section)
            <div class="section-block rounded-xl border border-[#e5e9f2] bg-white p-6 shadow-sm space-y-4" data-section="{{ $si }}">

                <div class="flex items-center justify-between">
                    <p class="text-sm font-bold text-[#18213a]">{{ __('Section') }} {{ $si + 1 }}</p>
                    <button type="button" onclick="removeSection(this)" class="text-xs text-red-500 hover:text-red-700">{{ __('Hapus Section') }}</button>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-[#18213a]">{{ __('Judul Section') }}</label>
                    <input type="text" name="sections[{{ $si }}][title]" value="{{ $section['title'] }}"
                           class="w-full rounded-lg border border-[#e5e9f2] px-4 py-2.5 text-sm focus:border-primary-600 focus:ring-1 focus:ring-primary-600 outline-none transition-all">
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-[#18213a]">{{ __('Paragraf Pembuka') }} <span class="text-[#7a8499] font-normal">({{ __('opsional') }})</span></label>
                    <textarea name="sections[{{ $si }}][intro]" rows="2"
                              class="w-full rounded-lg border border-[#e5e9f2] px-4 py-2.5 text-sm focus:border-primary-600 focus:ring-1 focus:ring-primary-600 outline-none transition-all resize-none">{{ $section['intro'] }}</textarea>
                </div>

                <div class="items-wrapper space-y-3">
                    @foreach($section['items'] as $ii => $item)
                    <div class="item-block rounded-lg bg-[#f8f9fc] p-4 space-y-2">
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-semibold text-[#7a8499] uppercase">{{ __('Poin') }} {{ $ii + 1 }}</p>
                            <button type="button" onclick="removeItem(this)" class="text-xs text-red-400 hover:text-red-600">{{ __('Hapus') }}</button>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs text-[#18213a]">{{ __('Label') }} <span class="text-[#7a8499]">({{ __('bold, opsional') }})</span></label>
                            <input type="text" name="sections[{{ $si }}][items][{{ $ii }}][label]" value="{{ $item['label'] }}"
                                   class="w-full rounded-lg border border-[#e5e9f2] px-3 py-2 text-sm focus:border-primary-600 outline-none transition-all">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs text-[#18213a]">{{ __('Isi') }}</label>
                            <textarea name="sections[{{ $si }}][items][{{ $ii }}][text]" rows="2"
                                      class="w-full rounded-lg border border-[#e5e9f2] px-3 py-2 text-sm focus:border-primary-600 outline-none transition-all resize-none">{{ $item['text'] }}</textarea>
                        </div>
                    </div>
                    @endforeach
                </div>

                <button type="button" onclick="addItem(this)"
                        class="w-full rounded-lg border border-dashed border-primary-600 py-2 text-sm text-primary-600 hover:bg-[#f0f4fc] transition-colors">
                    + {{ __('Tambah Poin') }}
                </button>
            </div>
            @endforeach
        </div>

        {{-- Tambah Section --}}
        <button type="button" onclick="addSection()"
                class="w-full rounded-xl border border-dashed border-[#7a8499] py-3 text-sm text-[#7a8499] hover:bg-[#f8f9fc] transition-colors">
            + {{ __('Tambah Section') }}
        </button>

        <div class="flex justify-end">
            <button type="submit" class="rounded-lg bg-primary-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-primary-700 transition-colors">
                {{ __('Simpan Perubahan') }}
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    const _lang = {
        poin: "{{ __('Poin') }}",
        hapus: "{{ __('Hapus') }}",
        section: "{{ __('Section') }}",
        hapusSection: "{{ __('Hapus Section') }}",
        judulSection: "{{ __('Judul Section') }}",
        paragrafPembuka: "{{ __('Paragraf Pembuka') }}",
        opsional: "{{ __('opsional') }}",
        label: "{{ __('Label') }}",
        boldOpsional: "{{ __('bold, opsional') }}",
        isi: "{{ __('Isi') }}",
        tambahPoin: "{{ __('Tambah Poin') }}",
        minSection: "{{ __('Minimal harus ada 1 section.') }}"
    };

    function reindex() {
        document.querySelectorAll('.section-block').forEach((section, si) => {
            section.querySelectorAll('.item-block').forEach((item, ii) => {
                item.querySelectorAll('[name]').forEach(el => {
                    el.name = el.name.replace(/sections\[\d+\]\[items\]\[\d+\]/, 'sections[' + si + '][items][' + ii + ']');
                });
                item.querySelector('.text-xs.font-semibold').textContent = _lang.poin + ' ' + (ii + 1);
            });
            section.querySelectorAll(':scope > div > [name], :scope > [name]').forEach(el => {
                el.name = el.name.replace(/sections\[\d+\](?!\[items\])/, 'sections[' + si + ']');
            });
            section.querySelector('.text-sm.font-bold').textContent = _lang.section + ' ' + (si + 1);
        });
    }

    function addItem(btn) {
        const wrapper = btn.previousElementSibling;
        const si = btn.closest('.section-block').dataset.section;
        const ii = wrapper.querySelectorAll('.item-block').length;

        const div = document.createElement('div');
        div.className = 'item-block rounded-lg bg-[#f8f9fc] p-4 space-y-2';
        div.innerHTML =
            '<div class="flex items-center justify-between">' +
                '<p class="text-xs font-semibold text-[#7a8499] uppercase">' + _lang.poin + ' ' + (ii + 1) + '</p>' +
                '<button type="button" onclick="removeItem(this)" class="text-xs text-red-400 hover:text-red-600">' + _lang.hapus + '</button>' +
            '</div>' +
            '<div>' +
                '<label class="mb-1 block text-xs text-[#18213a]">' + _lang.label + ' <span class="text-[#7a8499]">(' + _lang.boldOpsional + ')</span></label>' +
                '<input type="text" name="sections[' + si + '][items][' + ii + '][label]" value=""' +
                       ' class="w-full rounded-lg border border-[#e5e9f2] px-3 py-2 text-sm focus:border-primary-600 outline-none transition-all">' +
            '</div>' +
            '<div>' +
                '<label class="mb-1 block text-xs text-[#18213a]">' + _lang.isi + '</label>' +
                '<textarea name="sections[' + si + '][items][' + ii + '][text]" rows="2"' +
                          ' class="w-full rounded-lg border border-[#e5e9f2] px-3 py-2 text-sm focus:border-primary-600 outline-none transition-all resize-none"></textarea>' +
            '</div>';
        wrapper.appendChild(div);
        reindex();
    }

    function removeItem(btn) {
        btn.closest('.item-block').remove();
        reindex();
    }

    function addSection() {
        const wrapper = document.getElementById('sections-wrapper');
        const si = wrapper.querySelectorAll('.section-block').length;

        const div = document.createElement('div');
        div.className = 'section-block rounded-xl border border-[#e5e9f2] bg-white p-6 shadow-sm space-y-4';
        div.dataset.section = si;
        div.innerHTML =
            '<div class="flex items-center justify-between">' +
                '<p class="text-sm font-bold text-[#18213a]">' + _lang.section + ' ' + (si + 1) + '</p>' +
                '<button type="button" onclick="removeSection(this)" class="text-xs text-red-500 hover:text-red-700">' + _lang.hapusSection + '</button>' +
            '</div>' +
            '<div>' +
                '<label class="mb-1.5 block text-sm font-medium text-[#18213a]">' + _lang.judulSection + '</label>' +
                '<input type="text" name="sections[' + si + '][title]" value=""' +
                       ' class="w-full rounded-lg border border-[#e5e9f2] px-4 py-2.5 text-sm focus:border-primary-600 focus:ring-1 focus:ring-primary-600 outline-none transition-all">' +
            '</div>' +
            '<div>' +
                '<label class="mb-1.5 block text-sm font-medium text-[#18213a]">' + _lang.paragrafPembuka + ' <span class="text-[#7a8499] font-normal">(' + _lang.opsional + ')</span></label>' +
                '<textarea name="sections[' + si + '][intro]" rows="2"' +
                          ' class="w-full rounded-lg border border-[#e5e9f2] px-4 py-2.5 text-sm focus:border-primary-600 focus:ring-1 focus:ring-primary-600 outline-none transition-all resize-none"></textarea>' +
            '</div>' +
            '<div class="items-wrapper space-y-3"></div>' +
            '<button type="button" onclick="addItem(this)"' +
                    ' class="w-full rounded-lg border border-dashed border-primary-600 py-2 text-sm text-primary-600 hover:bg-[#f0f4fc] transition-colors">' +
                '+ ' + _lang.tambahPoin +
            '</button>';
        wrapper.appendChild(div);
        reindex();
    }

    function removeSection(btn) {
        if (document.querySelectorAll('.section-block').length <= 1) {
            alert(_lang.minSection);
            return;
        }
        btn.closest('.section-block').remove();
        reindex();
    }
</script>
@endpush