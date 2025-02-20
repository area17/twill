@extends('twill::layouts.form')

@section('contentFields')
    <x-twill::input
        type="textarea"
        name="description"
        label="Description"
        :maxlength="1000"
    />

    <x-twill::checkbox
        name="is_vip"
        label="Can access all VIP content"
    />
@stop
