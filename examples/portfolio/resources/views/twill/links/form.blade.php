@extends('twill::layouts.form')

@section('contentFields')
    <x-twill::input
        label="Description"
        name="description"
        :maxlength="100"
    />
@stop
