@extends('twill::layouts.form')

@section('contentFields')
    @formField('input', [
        'name' => 'description',
        'label' => 'Description',
        'translated' => true,
        'maxlength' => 100
    ])

    @formField('input', [
        'name' => 'birthday',
        'label' => 'Birthday',
        'translated' => false
    ])

    @formField('input', [
        'name' => 'bio',
        'label' => 'Bio',
        'translated' => true,
        'type' => 'textarea'
    ])
@stop
