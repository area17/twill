@extends('twill::layouts.free')

@section('customPageContent')
    @livewireStyles
    <livewire:livewire-twill-form :item-id="$itemId"/>
    @livewireScripts
@endsection
