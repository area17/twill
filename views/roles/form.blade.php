@extends('twill::layouts.form', [
    'contentFieldsetLabel' => 'Permissions',
])

@section('contentFields')
    @component('twill::partials.form.utils._field_rows', [
        'title' => 'General permissions'
    ])
        @formField('checkbox', [
            'name' => 'edit-settings',
            'label' => 'Edit property settings'
        ])

        @formField('checkbox', [
            'name' => 'edit-users',
            'label' => 'Manage users'
        ])

        @formField('checkbox', [
            'name' => 'edit-user-roles',
            'label' => 'Manage user roles'
        ])

        @formField('checkbox', [
            'name' => 'edit-user-groups',
            'label' => 'Manage user groups'
        ])

        @formField('checkbox', [
            'name' => 'access-media-library',
            'label' => 'Access media library'
        ])

        @formField('checkbox', [
            'name' => 'edit-media-library',
            'label' => 'Upload to media library'
        ])
    @endcomponent

    @component('twill::partials.form.utils._field_rows', [
        'title' => 'Content permissions'
    ])
        @formField('checkbox', [
            'name' => 'manage-modules',
            'label' => 'Manage All Modules'
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
                    'options' => array_merge([
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
                        ]
                    ], (config('twill.permissions.level')=='roleGroupItem' ? [['value' => 'manage-module', 'label' => 'Manage ' . $module_name ]] : []))
                ])
            @endforeach
        @endcomponent
    @endcomponent

    @component('twill::partials.form.utils._field_rows', [
        'title' => 'Groups'
    ])
        @formField('checkbox', [
            'name' => 'in_everyone_group',
            'label' => 'Include in "Everyone"'
        ])
    @endcomponent
@stop
