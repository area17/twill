@extends('twill::layouts.form')

@section('contentFields')
    @formField('input', [
        'name' => 'description',
        'label' => 'Description',
        'maxlength' => 100
    ])

    @formField('browser', [
        'moduleName' => 'writers',
        'name' => 'writers',
        'label' => 'Writers',
        'max' => 4,
    ])
@stop
