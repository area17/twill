@extends('twill::layouts.form')

@section('contentFields')
    @formField('input', [
        'name' => 'description',
        'label' => 'Description',
        'maxlength' => 100
    ])

    @formField('browser', [
        'moduleName' => 'authors',
        'name' => 'author',
        'label' => 'Author',
        'max' => 1,
    ])
@stop
