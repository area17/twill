@extends('twill::layouts.settings')

@section('contentFields')
    @formField('input', [
        'label' => 'Site title',
        'name' => 'site_title',
        'textLimit' => '80'
    ])
@stop
