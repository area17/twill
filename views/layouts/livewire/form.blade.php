@extends('twill::layouts.free')

@section('customPageContent')
    <div class="flex w-full">
        <livewire:livewire-twill-form :item-id="$itemId"/>
    </div>
@endsection

@push('extra_css')
    {{-- Temporary strategy   --}}
    <style>
        {{file_get_contents(base_path('twill/dist/style.css'))}}
    </style>
@endpush

