@extends('layouts.app')
@section('title', $page->title)

@section('content')
<div class="mx-auto max-w-4xl px-4 py-12">
    <div class="rounded-2xl bg-white p-8 shadow-sm border border-[#e5e9f2]">
        <h1 class="mb-6 text-center text-2xl font-bold text-[#18213a] uppercase">{{ $page->title }}</h1>
        
        {{-- Tanggal Otomatis --}}
        <p class="mt-2 text-center text-[#7a8499]">
            {{ __('Terakhir diperbarui') }} : {{ $page->updated_at->translatedFormat('j F Y') }}
        </p>
        
        @php $content = json_decode($page->content, true); @endphp

        @foreach($content['sections'] as $section)
            <h3 class="font-semibold text-[#18213a] mt-8">{{ $section['title'] }}</h3>
            
            @if($section['intro'])
                <p class="mt-2">{{ $section['intro'] }}</p>
            @endif
        
            <ul class="list-disc pl-5 mt-2 space-y-2">
                @foreach($section['items'] as $item)
                    <li>
                        @if($item['label'])<strong>{{ $item['label'] }}:</strong> @endif
                        {{ $item['text'] }}
                    </li>
                @endforeach
            </ul>
        @endforeach
    </div>
</div>
@endsection