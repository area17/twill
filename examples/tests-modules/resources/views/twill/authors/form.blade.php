@extends('twill::layouts.form')

@section('contentFields')
    <x-twill::medias
        name="avatar"
        label="Avatar"
    />

    <x-twill::input
        name="description"
        label="Description"
        :translated="true"
        :maxlength="100"
    />

    <x-twill::input
        name="birthday"
        label="Birthday"
        :translated="true"
    />

    <x-twill::input
        name="bio"
        label="Bio"
        :translated="true"
        type="textarea"
    />

    <x-twill::block-editor/>
@stop
