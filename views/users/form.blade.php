@extends('twill::layouts.form', [
    'contentFieldsetLabel' => twillTrans('twill::lang.user-management.content-fieldset-label'),
    'editModalTitle' => twillTrans('twill::lang.user-management.edit-modal-title'),
    'reloadOnSuccess' => true
])

@php
    $isSuperAdmin = isset($item->role) ? $item->role === 'SUPERADMIN' : false;
@endphp

@section('contentFields')
    @formField('input', [
        'name' => 'email',
        'label' => twillTrans('twill::lang.user-management.email')
    ])

    @can('manage-users')
        @if(!$isSuperAdmin && ($item->id !== $currentUser->id))
            @formField('select', [
                'name' => "role",
                'label' => twillTrans('twill::lang.user-management.role'),
                'options' => $roleList,
                'placeholder' => twillTrans('twill::lang.user-management.role-placeholder'),
            ])
        @endif
    @endcan

    @if(config('twill.enabled.users-image'))
        @formField('medias', [
            'name' => 'profile',
            'label' => twillTrans('twill::lang.user-management.profile-image'),
        ])
    @endif
    @if(config('twill.enabled.users-description'))
        @formField('input', [
            'name' => 'title',
            'label' => twillTrans('twill::lang.user-management.title'),
            'maxlength' => 250
        ])
        @formField('input', [
            'name' => 'description',
            'rows' => 4,
            'type' => 'textarea',
            'label' => twillTrans('twill::lang.user-management.description'),
        ])
    @endif

    @formField('select', [
        'name' => 'language',
        'label' => twillTrans('twill::lang.user-management.language'),
        'placeholder' => twillTrans('twill::lang.user-management.language-placeholder'),
        'default' => config('twill.locale', 'en'),
        'options' => array_map(function($locale) {
            return [
                'value' => $locale,
                'label' => getLanguageLabelFromLocaleCode($locale, true)
            ];
        }, config('twill.available_user_locales', ['en']))
    ])

    @if($with2faSettings ?? false)
        @formField('checkbox', [
            'name' => 'google_2fa_enabled',
            'label' => twillTrans('twill::lang.user-management.2fa'),
        ])

        @unless($item->google_2fa_enabled ?? false)
            @component('twill::partials.form.utils._connected_fields', [
                'fieldName' => 'google_2fa_enabled',
                'fieldValues' => true,
            ])
                <img style="display: block; margin-left: auto; margin-right: auto; max-height: 300px;" src="{{ $qrCode }}">
                <div class="f--regular f--note" style="margin: 20px 0;">{!! twillTrans('twill::lang.user-management.2fa-description', ['link' => 'https://github.com/antonioribeiro/google2fa#google-authenticator-apps']) !!}</div>
                @formField('input', [
                    'name' => 'verify-code',
                    'label' => twillTrans('twill::lang.user-management.otp'),
                ])
            @endcomponent
        @else
            @component('twill::partials.form.utils._connected_fields', [
                'fieldName' => 'google_2fa_enabled',
                'fieldValues' => false,
            ])
                @formField('input', [
                    'name' => 'verify-code',
                    'label' => twillTrans('twill::lang.user-management.otp'),
                    'note' => twillTrans('twill::lang.user-management.2fa-disable'),

                ])
            @endcomponent
        @endunless
    @endif

    @can('manage-users')
        @if(config('twill.enabled.users-2fa') && $item->google_2fa_enabled && ($item->id !== $currentUser->id))
            @formField('checkbox', [
                'name' => 'google_2fa_enabled',
                'label' => twillTrans('twill::lang.user-management.2fa'),
            ])

            @component('twill::partials.form.utils._connected_fields', [
                'fieldName' => 'google_2fa_enabled',
                'fieldValues' => false,
            ])
                @formField('input', [
                    'name' => 'force-2fa-disable-challenge',
                    'label' => twillTrans('twill::lang.user-management.force-2fa-disable'),
                    'note' => twillTrans('twill::lang.user-management.force-2fa-disable-description'),
                    'placeholder' => twillTrans('twill::lang.user-management.force-2fa-disable-challenge', ['user' => $item->email])
                ])
            @endcomponent
        @endif
    @endcan

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
    @if ($item->id == $currentUser->id)
        window['{{ config('twill.js_namespace') }}'].STORE.publication.withPublicationToggle = false
    @endif
@endpush
