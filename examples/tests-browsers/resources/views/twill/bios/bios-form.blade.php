@extends('twill::layouts.form')

@section('contentFields')
    <x-twill::input
        name="description"
        label="Description"
        :maxlength="100"
    />

    <x-twill::browser
        module-name="writers"
        name="writer"
        label="Writer"
        :max="1"
    />
@stop
