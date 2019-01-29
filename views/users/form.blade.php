@extends('twill::layouts.form', [
    'contentFieldsetLabel' => 'Account',
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

@section('fieldsets')
    @php
        $row = json_encode(json_decode('{"id":2,"name":"Peter","publish_start_date":null,"publish_end_date":null,"edit":"http://admin.guides.dev.a17.io/users/2/edit","delete":"http://admin.guides.dev.a17.io/users/2","published":1,"email":"peter@area17.com","role_value":"Admin"}'), JSON_UNESCAPED_SLASHES);
        $index = 0;
        $visibleColumns = '[{"name":"bulk","label":"","visible":true,"optional":false,"sortable":false},{"name":"published","label":"Published","visible":true,"optional":false,"sortable":false},{"name":"name","label":"Name","visible":true,"optional":false,"sortable":false},{"name":"email","label":"Email","visible":true,"optional":true,"sortable":true,"html":false},{"name":"role_value","label":"Role","visible":true,"optional":true,"sortable":true,"html":false}]';
    @endphp
    <a17-fieldset title="Guides Permissions" id="permissions">
        @formField('checkboxes', [
            'name' => 'permissions',
            'label' => 'Guides permission',
            'note' => 'Allow this user to have access to the following sections',
            'min' => 0,
            'inline' => false,
            'options' => [
            ]
        ])
        @foreach ($guides as $key => $guide)
            @formField('select', [
                'name' => 'guide' . $key,
                'label' => $guide->title,
                'unpack' => true,
                'options' => [
                    [
                        'value' => 'none',
                        'label' => 'None' 
                    ],
                    [
                        'value' => 'view',
                        'label' => 'View'
                    ],
                    [
                        'value' => 'edit',
                        'label' => 'Edit'
                    ],
                    [
                        'value' => 'manage',
                        'label' => 'Manage'
                    ],
                ]
            ])
        @endforeach
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