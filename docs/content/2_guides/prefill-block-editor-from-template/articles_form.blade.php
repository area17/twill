@extends('twill::layouts.form')

@section('contentFields')
    <x-twill::input
        name="description"
        label="Description"
        :maxlength="100"
    />

    <x-twill::block-editor {{-- [tl! ++] --}}
            :blocks="\App\Models\Article::AVAILABLE_BLOCKS"{{-- [tl! ++] --}}
    />{{-- [tl! ++] --}}
@stop
