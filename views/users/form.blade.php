@extends('cms-toolkit::layouts.form', [
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

    @if(config('cms-toolkit.enabled.users-image'))
        @formField('medias', [
            'name' => 'profile',
            'label' => 'Profile image'
        ])
    @endif
@stop

@push('vuexStore')
    window.STORE.publication.publishedLabel = 'Enabled'
    window.STORE.publication.draftLabel = 'Disabled'
    window.STORE.publication.submitOptions = {
        draft: [
          {
            name: 'save',
            text: 'Disable user'
          },
          {
            name: 'save-close',
            text: 'Disable user and close'
          },
          {
            name: 'save-new',
            text: 'Disable user and create new'
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
