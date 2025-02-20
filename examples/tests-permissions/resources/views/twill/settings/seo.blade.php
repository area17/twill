@extends('twill::layouts.settings')

@section('contentFields')
    <x-twill::input
        label="Site title"
        name="site_title"
        :maxlength="80"
    />
@stop
