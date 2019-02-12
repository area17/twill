@extends('twill::layouts.form', [
    'contentFieldsetLabel' => 'Permissions',
])

@section('contentFields')
    @formField('checkboxes', [
        'name' => 'general-permissions',
        'label' => 'General permissions',
        'inline' => false,
        'options' => [
            [
                'value' => 'edit-property-settings',
                'label' => 'Edit property settings'
            ],
            [
                'value' => 'manage-users',
                'label' => 'Manage users'
            ],
            [
                'value' => 'manage-user-roles',
                'label' => 'Manage user roles'
            ],
            [
                'value' => 'manage-user-groups',
                'label' => 'Manage user groups'
            ],
        ]
    ])

    @foreach($permission_modules as $module_name => $module_items)
        @formField('checkboxes', [
            'name' => $module_name . '-permission',
            'label' => ucfirst($module_name) . ' permissions',
            'inline' => false,
            'options' => [
                [
                    'value' => 'create-destroy-' . $module_name,
                    'label' => 'Create/destroy ' . $module_name
                ],
                [
                    'value' => 'manage-all-' . $module_name,
                    'label' => 'Manage all ' . $module_name
                ]
            ]
        ])
    @endforeach

    @formField('checkboxes', [
        'name' => 'groups',
        'label' => 'Groups',
        'inline' => false,
        'options' => [
            [
                'value' => 'include-in-everyone',
                'label' => 'Include in everyone'
            ]
        ]
    ])
@stop