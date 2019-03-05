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

    @foreach($permission_modules as $module_name => $module_items)
        @formField('checkboxes', [
            'name' => $module_name . '-permission',
            'label' => ucfirst($module_name) . ' permissions',
            'inline' => false,
            'options' => [
                [
                    'value' => 'view ' . $module_name,
                    'label' => 'View ' . $module_name
                ],
                [
                    'value' => 'create ' . $module_name,
                    'label' => 'Create ' . $module_name
                ],
                [
                    'value' => 'destroy ' . $module_name,
                    'label' => 'Destroy ' . $module_name
                ],
                [
                    'value' => 'edit ' . $module_name,
                    'label' => 'Edit ' . $module_name
                ],
                [
                    'value' => 'manage ' . $module_name,
                    'label' => 'Manage ' . $module_name
                ]
            ]
        ])
    @endforeach

    {{-- @formField('checkboxes', [
        'name' => 'in_everyone_group',
        'label' => 'Groups',
        'inline' => false,
        'options' => [
            [
                'value' => 'include-in-everyone',
                'label' => 'Include in everyone'
            ]
        ]
    ]) --}}
@stop