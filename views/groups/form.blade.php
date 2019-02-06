@extends('twill::layouts.form')

@section('contentFields')
    @formField('input', [
        'name' => 'description',
        'label' => 'Description',
        'maxlength' => 250,
        'placeholder' => 'Enter the description for the group',
        'type' => 'textarea',
        'rows' => 3
    ])

    @formField('browser', [
        'moduleName' => 'users',
        'name' => 'users',
        'notes' => 'Add memebers to the groups',
        'label' => 'Users',
        'max' => 999
    ])
@stop