@extends('twill::layouts.form')

@section('contentFields')
    @formField('input', [
        'name' => 'description',
        'label' => 'Description',
        'maxlength' => 100
    ])

    @formField('browser', [
        'moduleName' => 'authors',
        'name' => 'authors',
        'label' => 'Authors',
        'max' => 4,
    ])
@stop
