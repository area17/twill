@extends('twill::layouts.form')

@section('contentFields')
    @formField('input', [
        'name' => 'description',
        'label' => 'Description',
        'translated' => true,
        'maxlength' => 100
    ])

    {{--
    -- This is the repeater field allow to reference partners. We chose a name project_partner to indicate that it is
    -- a repeater specifically for selecting partners and filling in their role.
    -- See repeaters/project_partner.blade.php for the form itself.
    --}}
    @formField('repeater', [
        'label' => 'Partners',
        'type' => 'project_partner',
        'allowCreate' => false,
        'relation' => 'partners', // The relation method in your model.
        'browserModule' => [ // Same as a browser field, but this is limited to 1 type only.
            'label' => 'Partner',
            'name' => 'partners',
        ]
    ])
@stop
