@extends('twill::layouts.form', [
    'contentFieldsetLabel' => 'Permissions',
])

@section('contentFields')
    <br/><h1>General permissions</h1>
    @formField('checkbox', [
        'name' => 'edit-settings',
        'label' => 'Edit property settings'
    ])

    @formField('checkbox', [
        'name' => 'edit-users',
        'label' => 'Manage users'
    ])

    @formField('checkbox', [
        'name' => 'edit-user-role',
        'label' => 'Manage user role'
    ])

    @formField('checkbox', [
        'name' => 'edit-user-groups',
        'label' => 'Manage user groups'
    ])

    @formField('checkbox', [
        'name' => 'manage-modules',
        'label' => 'Manage All Modules'
    ])

    @formField('checkbox', [
        'name' => 'access-media-library',
        'label' => 'Access media library'
    ])

    @component('twill::partials.form.utils._connected_fields', [
        'fieldName' => 'manage-modules',
        'fieldValues' => false,
    ])
        @foreach($permission_modules as $module_name => $module_items)
            @formField('select', [
                'name' => 'module_' . $module_name . '_permissions',
                'label' => ucfirst($module_name) . ' permissions',
                'placeholder' => 'Select a permission',
                'options' => [
                    [
                        'value' => 'none',
                        'label' => 'None'
                    ],
                    [
                        'value' => 'view-module',
                        'label' => 'View ' . $module_name
                    ],
                    [
                        'value' => 'edit-module',
                        'label' => 'Edit ' . $module_name
                    ],
                    [
                        'value' => 'manage-module',
                        'label' => 'Manage ' . $module_name
                    ]
                ]
            ])
        @endforeach
    @endcomponent

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