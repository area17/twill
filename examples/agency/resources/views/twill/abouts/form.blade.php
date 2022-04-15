@extends('twill::layouts.form')

@section('contentFields')
    @formField('input', [
        'name' => 'tagline',
        'label' => 'Page tagline',
        'translated' => true,
        'maxlength' => 100
    ])

    @formField('wysiwyg', [
        'name' => 'text',
        'label' => 'Page text',
        'toolbarOptions' => [
            'bold',
            'italic',
            'underline',
            'link'
        ],
        'placeholder' => '',
        'translated' => true
    ])
@stop
