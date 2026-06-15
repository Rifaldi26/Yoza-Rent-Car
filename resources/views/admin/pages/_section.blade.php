<div class="section-block rounded-xl border border-[#e5e9f2] bg-white p-6 shadow-sm space-y-4"
     data-si="{{ $si }}">

    {{-- Header section --}}
    <div class="flex items-center justify-between">
        <p class="section-label text-sm font-bold text-[#18213a]">
            {{ __('Section') }} {{ $si + 1 }}
        </p>

        <button type="button"
                onclick="removeSection(this)"
                class="text-xs text-red-500 hover:text-red-700">
            {{ __('Hapus Section') }}
        </button>
    </div>

    {{-- Judul section --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium text-[#18213a]">
            {{ __('Judul Section') }}
            <span class="text-red-500">*</span>
        </label>

        <input
            type="text"
            name="sections[{{ $si }}][title]"
            data-field="title"
            value="{{ old("sections.{$si}.title", $section['title'] ?? '') }}"
            class="w-full rounded-lg border border-[#e5e9f2] px-4 py-2.5 text-sm outline-none
                   transition-all focus:border-[#3b6fd4] focus:ring-1 focus:ring-[#3b6fd4]"
        >

        @error("sections.{$si}.title")
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Intro --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium text-[#18213a]">
            {{ __('Paragraf Pembuka') }}
            <span class="text-[#7a8499] font-normal">
                ({{ __('opsional') }})
            </span>
        </label>

        <textarea
            name="sections[{{ $si }}][intro]"
            data-field="intro"
            rows="2"
            class="w-full rounded-lg border border-[#e5e9f2] px-4 py-2.5 text-sm outline-none
                   transition-all focus:border-[#3b6fd4] focus:ring-1 focus:ring-[#3b6fd4] resize-none"
        >{{ old("sections.{$si}.intro", $section['intro'] ?? '') }}</textarea>
    </div>

    {{-- Items --}}
    <div class="items-wrapper space-y-3">
        @foreach ($section['items'] ?? [] as $ii => $item)
            <div class="item-block rounded-lg bg-[#f8f9fc] p-4 space-y-2">

                <div class="flex items-center justify-between">
                    <p class="item-label text-xs font-semibold text-[#7a8499] uppercase">
                        {{ __('Poin') }} {{ $ii + 1 }}
                    </p>

                    <button type="button"
                            onclick="removeItem(this)"
                            class="text-xs text-red-400 hover:text-red-600">
                        {{ __('Hapus') }}
                    </button>
                </div>

                <div>
                    <label class="mb-1 block text-xs text-[#18213a]">
                        {{ __('Label') }}
                        <span class="text-[#7a8499]">
                            ({{ __('bold, opsional') }})
                        </span>
                    </label>

                    <input
                        type="text"
                        name="sections[{{ $si }}][items][{{ $ii }}][label]"
                        data-field="label"
                        value="{{ old("sections.{$si}.items.{$ii}.label", $item['label'] ?? '') }}"
                        class="w-full rounded-lg border border-[#e5e9f2] px-3 py-2 text-sm
                               outline-none transition-all focus:border-[#3b6fd4]"
                    >
                </div>

                <div>
                    <label class="mb-1 block text-xs text-[#18213a]">
                        {{ __('Isi') }}
                        <span class="text-red-500">*</span>
                    </label>

                    <textarea
                        name="sections[{{ $si }}][items][{{ $ii }}][text]"
                        data-field="text"
                        rows="2"
                        class="w-full rounded-lg border border-[#e5e9f2] px-3 py-2 text-sm
                               outline-none transition-all focus:border-[#3b6fd4] resize-none"
                    >{{ old("sections.{$si}.items.{$ii}.text", $item['text'] ?? '') }}</textarea>

                    @error("sections.{$si}.items.{$ii}.text")
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        @endforeach
    </div>

    <button type="button"
            onclick="addItem(this)"
            class="w-full rounded-lg border border-dashed border-[#3b6fd4] py-2 text-sm
                   text-[#3b6fd4] hover:bg-[#f0f4fc] transition-colors">
        + {{ __('Tambah Poin') }}
    </button>

</div>