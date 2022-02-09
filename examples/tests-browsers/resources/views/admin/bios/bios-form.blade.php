@extends('twill::layouts.form')

@section('contentFields')
    @formField('input', [
        'name' => 'description',
        'label' => 'Description',
        'maxlength' => 100
    ])

    @formField('browser', [
        'moduleName' => 'writers',
        'name' => 'writer',
        'label' => 'Writer',
        'max' => 1,
    ])
@stop
