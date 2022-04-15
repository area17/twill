@extends('twill::layouts.form', [
    'additionalFieldsets' => [
        ['fieldset' => 'videos', 'label' => 'Videos'],
    ]
])

@section('contentFields')
    @formField('input', [
        'name' => 'first_name',
        'label' => 'First Name',
        'translated' => false,
    ])

    @formField('input', [
        'name' => 'last_name',
        'label' => 'Last Name',
        'translated' => false,
    ])

    @formField('select', [
        'name' => 'role_id',
        'label' => 'Role',
        'placeholder' => 'Select a role',
        'options' => $roles
    ])

    @formField('wysiwyg', [
        'name' => 'biography',
        'label' => 'Biography',
        'toolbarOptions' => [
            'bold',
            'italic',
            'underline',
            'link'
        ],
        'placeholder' => '',
        'translated' => true
    ])

    @formField('select', [
        'name' => 'start_year',
        'label' => 'Start year',
        'placeholder' => 'Select a year',
        'options' => $years
    ])

    @formField('select', [
        'name' => 'office_id',
        'label' => 'Office',
        'placeholder' => 'Select an office',
        'options' => $offices
    ])

    @formField('medias', [
        'name' => 'main',
        'label' => 'Main',
        'max' => 1,
        'fieldNote' => 'Minimum image width: 1500px'
    ])

    @formField('medias', [
        'name' => 'search',
        'label' => 'Search',
        'max' => 1
    ])
@stop

@section('fieldsets')
    @formFieldset(['id' => 'videos', 'title' => 'Videos', 'open' => true])
        @formField('repeater', ['type' => 'video'])
    @endformFieldset
@stop
