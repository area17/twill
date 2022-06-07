@extends('twill::layouts.form', [
    'contentFieldsetLabel' => 'Permissions',
])

@section('contentFields')
    <x-twill::fieldRows title="General permissions">
        <x-twill::checkbox
            name="edit-settings"
            label="Edit property settings"
        />

        <x-twill::checkbox
            name="edit-users"
            label="Manage users"
        />

        <x-twill::checkbox
            name="edit-user-roles"
            label="Manage user roles"
        />

        <x-twill::checkbox
            name="edit-user-groups"
            label="Manage user groups"
        />

        <x-twill::checkbox
            name="access-media-library"
            label="Access media library"
        />

        <x-twill::checkbox
            name="edit-media-library"
            label="Upload to media library"
        />
    </x-twill::fieldRows>

    <x-twill::fieldRows title="Content permissions">
        <x-twill::checkbox
            name="manage-modules"
            label="Manage all modules"
        />

        <x-twill::formConnectedFields
            field-name="manage-modules"
            :field-values="false"
        >
            @foreach($permission_modules as $module_name => $module_items)
                <x-twill::select
                    :name="'module_' . $module_name . '_permissions'"
                    :label="ucfirst($module_name) . ' permissions'"
                    placeholder="Select a permission"
                    :options="array_merge([
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
                        ],
                        (\A17\Twill\Facades\TwillPermissions::levelIs(\A17\Twill\Enums\PermissionLevel::LEVEL_ROLE_GROUP_ITEM) ? [['value' => 'manage-module', 'label' => 'Manage ' . $module_name ]] : []))"
                />
            @endforeach
        </x-twill::formConnectedFields>
    </x-twill::fieldRows>

    <x-twill::fieldRows title="Groups">
        <x-twill::checkbox
            name="in_everyone_group"
            label="Include in 'Everyone'"
        />
    </x-twill::fieldRows>
@stop
