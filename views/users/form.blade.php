@extends('twill::layouts.form', [
    'contentFieldsetLabel' => 'User settings',
    'editModalTitle' => 'Edit user name'
])

@php
    $isSuperAdmin = isset($item->role) ? $item->role === 'SUPERADMIN' : false;
@endphp

@section('contentFields')
    @formField('input', [
        'name' => 'email',
        'label' => 'Email'
    ])

    @can('edit-user-role')
        @if(!$isSuperAdmin && ($item->id !== $currentUser->id))
            @formField('select', [
                'name' => "role",
                'label' => "Role",
                'options' => $roleList,
                'placeholder' => 'Select a role'
            ])
        @endif
    @endcan

    @if(config('twill.enabled.users-image'))
        @formField('medias', [
            'name' => 'profile',
            'label' => 'Profile image'
        ])
    @endif
    @if(config('twill.enabled.users-description'))
        @formField('input', [
            'name' => 'title',
            'label' => 'Title',
            'maxlength' => 250
        ])
        @formField('input', [
            'name' => 'description',
            'rows' => 4,
            'type' => 'textarea',
            'label' => 'Description'
        ])
    @endif
@stop

{{-- @section('fieldsets')
    <a17-fieldset title="Permissions" id="permissions">
      @formField('checkboxes', [
        'name' => 'permissions',
        'label' => '',
        'min' => 0,
        'inline' => false,
        'options' => [
            [
                'value' => 'list',
                'label' => 'List'
            ],
            [
                'value' => 'edit',
                'label' => 'Edit'
            ],
            [
                'value' => 'reorder',
                'label' => 'Reorder'
            ],
            [
                'value' => 'publish',
                'label' => 'Publish'
            ],
            [
                'value' => 'feature',
                'label' => 'Feature'
            ],
            [
                'value' => 'delete',
                'label' => 'Delete'
            ],
            [
                'value' => 'edit-user',
                'label' => 'Edit User'
            ],
            [
                'value' => 'edit-user-role',
                'label' => 'Edit User Role'
            ],
            [
                'value' => 'publish-user',
                'label' => 'Publish User'
            ]
        ]
    ])
    </a17-fieldset>
@stop --}}

@section('fieldsets')
    <a17-fieldset title="Groups" id="groups">

    </a17-fieldset>
@stop

@push('vuexStore')
    window.STORE.publication.submitOptions = {
        draft: [
          {
            name: 'save',
            text: 'Update disabled user'
          },
          {
            name: 'save-close',
            text: 'Update disabled and close'
          },
          {
            name: 'save-new',
            text: 'Update disabled user and create new'
          },
          {
            name: 'cancel',
            text: 'Cancel'
          }
        ],
        live: [
          {
            name: 'publish',
            text: 'Enable user'
          },
          {
            name: 'publish-close',
            text: 'Enable user and close'
          },
          {
            name: 'publish-new',
            text: 'Enable user and create new'
          },
          {
            name: 'cancel',
            text: 'Cancel'
          }
        ],
        update: [
          {
            name: 'update',
            text: 'Update'
          },
          {
            name: 'update-close',
            text: 'Update and close'
          },
          {
            name: 'update-new',
            text: 'Update and create new'
          },
          {
            name: 'cancel',
            text: 'Cancel'
          }
        ]
      }
    @if ($item->id == $currentUser->id)
        window.STORE.publication.withPublicationToggle = false
    @endif
@endpush
