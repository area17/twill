@extends('twill::layouts.form', [
    'contentFieldsetLabel' => 'Permissions',
])

@section('contentFields')
    @formField('checkboxes', [
        'name' => 'general_permissions',
        'label' => 'General permissions',
        'inline' => false,
        'options' => [
            [
                'value' => 'edit-settings',
                'label' => 'Edit property settings'
            ],
            [
                'value' => 'edit-users',
                'label' => 'Manage users'
            ],
            [
                'value' => 'edit-user-role',
                'label' => 'Manage user role'
            ],
            [
                'value' => 'edit-user-groups',
                'label' => 'Manage user groups'
            ],
            [
                'value' => 'manage-modules',
                'label' => 'Manage all modules'
            ],
            [
                'value' => 'access-media-library',
                'label' => 'Access media library'
            ],
        ]
    ])

    {{-- @foreach($permission_modules as $module_name => $module_items)
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
    @endforeach --}}

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