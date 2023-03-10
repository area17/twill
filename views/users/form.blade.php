@extends('twill::layouts.form', [
    'contentFieldsetLabel' => twillTrans('twill::lang.user-management.content-fieldset-label'),
    'editModalTitle' => twillTrans('twill::lang.user-management.edit-modal-title'),
    'reloadOnSuccess' => true
])

@php
    $isSuperAdmin = isset($item->role) ? $item->role === 'SUPERADMIN' : false;
@endphp

@section('contentFields')

    <x-twill::input
        name="email"
        :label="twillTrans('twill::lang.user-management.email')"
    />

    @can('edit-user-roles')
        @if($item->id !== $currentUser->id)
            <x-twill::select
                :name="$item->getRoleColumnName()"
                :label="twillTrans('twill::lang.user-management.role')"
                :options="$roleList ?? []"
                :placeholder="twillTrans('twill::lang.user-management.role-placeholder')"
            />
        @endif
    @endcan

    @if(config('twill.enabled.users-image'))
        <x-twill::medias
            name="profile"
            :label="twillTrans('twill::lang.user-management.profile-image')"
        />
    @endif

    @if(config('twill.enabled.users-description'))
        <x-twill::input
            name="title"
            :label="twillTrans('twill::lang.user-management.title')"
            :maxlength="250"
        />
        <x-twill::input
            name="description"
            :rows="4"
            type="textarea"
            :label="twillTrans('twill::lang.user-management.description')"
        />
    @endif

    @php
        $languageOptions = array_map(
            function($locale) {
                return [
                    'value' => $locale,
                    'label' => getLanguageLabelFromLocaleCode($locale, true)
                ];
            },
            config('twill.available_user_locales', ['en'])
        );
    @endphp

    <x-twill::select
        name="language"
        :label="twillTrans('twill::lang.user-management.language')"
        :placeholder="twillTrans('twill::lang.user-management.language-placeholder')"
        :default="config('twill.locale', 'en')"
        :options="$languageOptions"
    />

    @if($with2faSettings ?? false)
        <x-twill::checkbox
            name="google_2fa_enabled"
            :label="twillTrans('twill::lang.user-management.2fa')"
        />

        @unless($item->google_2fa_enabled ?? false)
            <x-twill::formConnectedFields
                field-name="google_2fa_enabled"
                :field-values="true"
            >
                <img style="display: block; margin-left: auto; margin-right: auto; max-height: 300px;"
                     src="{{ $qrCode }}">
                <div class="f--regular f--note"
                     style="margin: 20px 0;">{!! twillTrans('twill::lang.user-management.2fa-description', ['link' => 'https://github.com/antonioribeiro/google2fa#google-authenticator-apps']) !!}</div>
                <x-twill::input
                    name="verify-code"
                    :label="twillTrans('twill::lang.user-management.otp')"
                />
            </x-twill::formConnectedFields>
        @else
            <x-twill::formConnectedFields
                field-name="google_2fa_enabled"
                :field-values="false"
            >
                <x-twill::input
                    name="verify-code"
                    :label="twillTrans('twill::lang.user-management.otp')"
                    :note="twillTrans('twill::lang.user-management.2fa-disable')"
                />
            </x-twill::formConnectedFields>
        @endunless
    @endif

    @can('manage-users')
        @if(config('twill.enabled.users-2fa') && $item->google_2fa_enabled && ($item->id !== $currentUser->id))
            <x-twill::checkbox name="google_2fa_enabled" :label="twillTrans('twill::lang.user-management.2fa')"/>

            <x-twill::formConnectedFields
                field-name="google_2fa_enabled"
                :field-values="false"
            >
                <x-twill::input name="force-2fa-disable-challenge"
                                :label="twillTrans('twill::lang.user-management.force-2fa-disable')"
                                :note="twillTrans('twill::lang.user-management.force-2fa-disable-description')"
                                :placeholder="twillTrans('twill::lang.user-management.force-2fa-disable-challenge', ['user' => $item->email])"
                />
            </x-twill::formConnectedFields>
        @endif
    @endcan

    @if(\A17\Twill\Facades\TwillPermissions::levelIsOneOf([\A17\Twill\Enums\PermissionLevel::LEVEL_ROLE_GROUP, \A17\Twill\Enums\PermissionLevel::LEVEL_ROLE_GROUP_ITEM]))
        @can('edit-user-groups')
            <x-twill::browser
                module-name="groups"
                name="groups"
                label="Groups"
                :max="999"
            />
        @else
            @if($item->groups->count())
                @php
                    $groups = json_encode($item->groups->map(function ($group) {
                        return [
                            'label' => $group->name,
                            'value' => $group->id
                        ];
                    }));

                    $values = json_encode($item->groups->map(function ($group) {
                        return $group->id;
                    }));
                @endphp

                <a17-vselect
                    label="Groups"
                    name="groups_readonly"
                    :selected="{{ $groups }}"
                    :options="{{ $groups }}"
                    :multiple="true"
                    :disabled="true"
                ></a17-vselect>
            @endif
        @endcan
    @endif
@stop


@section('fieldsets')
    @if(\A17\Twill\Facades\TwillPermissions::levelIs(\A17\Twill\Enums\PermissionLevel::LEVEL_ROLE_GROUP_ITEM))
        @can('edit-users')
            @unless($item->isSuperAdmin() || $item->id === $currentUser->id)
                <x-twill::formConnectedFields
                    field-name="role_id"
                    :render-for-blocks="false"
                    :field-values="$item->role_id"
                >
                    @foreach($permissionModules as $moduleName => $moduleItems)
                        <a17-fieldset title='{{ ucfirst($moduleName) . " Permissions"}}' id='{{ $moduleName }}'>
                            <x-twill::select-permissions
                                :items-in-selects-tables="$moduleItems"
                                label-key="title"
                                :name-pattern="$moduleName . '_%id%_permission'"
                            />
                        </a17-fieldset>
                    @endforeach
                </x-twill::formConnectedFields>
            @endif
        @endcan
    @endif

@stop

@push('vuexStore')
    window['{{ config('twill.js_namespace') }}'].STORE.publication.submitOptions = {
    draft: [
    {
    name: 'save',
    text: {!! json_encode(twillTrans('twill::lang.user-management.update-disabled-user')) !!}
    },
    {
    name: 'save-close',
    text: {!! json_encode(twillTrans('twill::lang.user-management.update-disabled-and-close')) !!}
    },
    {
    name: 'save-new',
    text: {!! json_encode(twillTrans('twill::lang.user-management.update-disabled-user-and-create-new')) !!}
    },
    {
    name: 'cancel',
    text: {!! json_encode(twillTrans('twill::lang.user-management.cancel')) !!}
    }
    ],
    live: [
    {
    name: 'publish',
    text: {!! json_encode(twillTrans('twill::lang.user-management.enable-user')) !!}
    },
    {
    name: 'publish-close',
    text: {!! json_encode(twillTrans('twill::lang.user-management.enable-user-and-close')) !!}
    },
    {
    name: 'publish-new',
    text: {!! json_encode(twillTrans('twill::lang.user-management.enable-user-and-create-new')) !!}
    },
    {
    name: 'cancel',
    text: {!! json_encode(twillTrans('twill::lang.user-management.cancel')) !!}
    }
    ],
    update: [
    {
    name: 'update',
    text: {!! json_encode(twillTrans('twill::lang.user-management.update')) !!}
    },
    {
    name: 'update-close',
    text: {!! json_encode(twillTrans('twill::lang.user-management.update-and-close')) !!}
    },
    {
    name: 'update-new',
    text: {!! json_encode(twillTrans('twill::lang.user-management.update-and-create-new')) !!}
    },
    {
    name: 'cancel',
    text: {!! json_encode(twillTrans('twill::lang.user-management.cancel')) !!}
    }
    ]
    }
    @unless($item->isSuperAdmin())
        @can('edit-users')
            window['{{ config('twill.js_namespace') }}'].STORE.publication.userInfo = {
            user_name: '{{ $item->name }}',
            registered_at: '{{ $item->isActivated() ? $item->registered_at->format('d M Y') : "Pending ({$item->created_at->format('d M Y')})" }}',
            last_login_at: '{{ $item->isActivated() && $item->last_login_at ? $item->last_login_at->format('d M Y, H:i') : null }}',
            resend_registration_link: '{{ $item->isPublished() && !$item->isActivated() ? route('twill.users.resend.registrationEmail', ['user' => $item]) : null }}',
            is_activated: {{ json_encode($item->isActivated()) }}
            }
        @endcan
    @endunless

    @if ($item->id == $currentUser->id)
        window['{{ config('twill.js_namespace') }}'].STORE.publication.withPublicationToggle = false
    @endif
@endpush

@push('extra_js')
    <script>
        const formFields = {!! json_encode($form_fields) !!};
        const groupPermissionMapping = {!! json_encode($groupPermissionMapping ?? []) !!};
        var selectedGroups = formFields.browsers ? formFields.browsers.groups : []

        window['{{ config('twill.js_namespace') }}'].vm.$store.subscribe((mutation, state) => {
            const { type, payload } = mutation
            switch (type) {
                case 'saveSelectedItems':
                    selectedGroups = JSON.parse(JSON.stringify(payload))
                    selectedGroups.forEach((group) => {
                        const permissions = groupPermissionMapping[group['id']]
                        permissions.forEach((permission) => {
                            const fieldName = `${permission['permissionable_module']}_${permission['permissionable_id']}_permission`
                            const currentPermission = state['form']['fields'].find(function (e) {
                                return e.name === fieldName
                            })
                            // Only update when the permission is none.
                            if (!currentPermission || currentPermission.value === '') {
                                const field = {
                                    name: fieldName,
                                    value: 'view-item'
                                }
                                window['{{ config('twill.js_namespace') }}'].vm.$store.commit('updateFormField', field)
                            }
                        })
                    })
                    break

                case 'destroySelectedItem':
                    const group = selectedGroups[payload.index]
                    const permissions = groupPermissionMapping[group['id']]
                    permissions.forEach((permission) => {
                        const fieldName = `${permission['permissionable_module']}_${permission['permissionable_id']}_permission`
                        const currentPermission = state['form']['fields'].find(function (e) {
                            return e.name === fieldName
                        })
                        if (currentPermission && currentPermission.value === 'view-item') {
                            const field = {
                                name: fieldName,
                                value: ''
                            }
                            window['{{ config('twill.js_namespace') }}'].vm.$store.commit('updateFormField', field)
                        }
                    })
                    break
            }
        })
    </script>
@endpush
