@extends('twill::layouts.form')

@section('contentFields')
    <x-twill::input
        name="description"
        label="Description"
        :translated="true"
        :maxlength="100"
    />
@stop
