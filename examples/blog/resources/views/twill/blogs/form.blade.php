@extends('twill::layouts.form')

@section('contentFields')
    <x-twill::input
        name="description"
        label="Description"
        :translated="true"
        :maxlength="100"
    />

    <x-twill::medias
        name="cover"
        label="Cover image"
    />

    <x-twill::browser
        module-name="categories"
        name="categories"
        label="Category"
        :max="1"
    />

    <x-twill::tags />

    <x-twill::block-editor/>
@stop
