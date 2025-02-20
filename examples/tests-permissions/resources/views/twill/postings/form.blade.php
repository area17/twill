@extends('twill::layouts.form')

@section('contentFields')
    <x-twill::input
        name="description"
        label="Description"
        :maxlength="100"
    />
@stop
