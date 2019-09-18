@extends('twill::layouts.form', [
    'contentFieldsetLabel' => 'Account',
    'editModalTitle' => 'Edit user name',
    // 'reloadOnSuccess' => true
])

@section('contentFields')

    @formField('input', [
        'name' => 'email',
        'label' => 'Email'
    ])

    @can('edit-user-role')
        @unless($item->is_superadmin)
            @formField('select', [
                'name' => "role_id",
                'label' => "Role",
                'options' => $roleList,
                'placeholder' => 'Select a role'
            ])
        @endunless
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

    @unless($item->is_superadmin)
        @formField('multi_select', [
            'name' => "groups",
            'label' => 'Groups',
            'options' => $groupOptions,
            'endpoint' => '/group/search',
            'unpack' => false,
            'note' => 'Every user belongs to the "Everyone" group'
        ])
    @endunless

    @if($with2faSettings ?? false)
        @formField('checkbox', [
            'name' => 'google_2fa_enabled',
            'label' => '2-factor authentication',
        ])

        @unless($item->google_2fa_enabled ?? false)
            @component('twill::partials.form.utils._connected_fields', [
                'fieldName' => 'google_2fa_enabled',
                'fieldValues' => true,
            ])
                <img style="display: block; margin-left: auto; margin-right: auto;" src="{{ $qrCode }}">
                <div class="f--regular f--note" style="margin: 20px 0;">Please scan this QR code with a Google Authenticator compatible application and enter your one time password below before submitting. See a list of compatible applications <a href="https://github.com/antonioribeiro/google2fa#google-authenticator-apps" target="_blank" rel="noopener">here</a>.</div>
                @formField('input', [
                    'name' => 'verify-code',
                    'label' => 'One time password',
                ])
            @endcomponent
        @else
            @component('twill::partials.form.utils._connected_fields', [
                'fieldName' => 'google_2fa_enabled',
                'fieldValues' => false,
            ])
                @formField('input', [
                    'name' => 'verify-code',
                    'label' => 'One time password',
                    'note' => 'Enter your one time password to disable the 2-factor authentication'

                ])
            @endcomponent
        @endunless
    @endif
@stop

@can('edit-users')
  @unless($item->is_superadmin)
    @section('fieldsets')
        @foreach($permissionModules as $moduleName => $moduleItems)
            <a17-fieldset title='{{ ucfirst($moduleName) . " Permissions"}}' id='{{ $moduleName }}'>
                {{-- <h2>{{ ucfirst($moduleName) .' permission' }}</h2> --}}
                @foreach ($moduleItems as $moduleItem)
                    @formField('select', [
                        'name' => $moduleName . '_' . $moduleItem->id . '_permission',
                        'label' => $moduleItem->title,
                        'unpack' => true,
                        'options' => [
                            [
                                'value' => '',
                                'label' => 'None'
                            ],
                            [
                                'value' => 'view-item',
                                'label' => 'View'
                            ],
                            [
                                'value' => 'edit-item',
                                'label' => 'Edit'
                            ],
                            [
                                'value' => 'manage-item',
                                'label' => 'Manage'
                            ],
                        ]
                    ])
                @endforeach
            </a17-fieldset>
        @endforeach
    @stop
  @endunless
@endcan

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
    @unless($item->is_superadmin)
        @can('edit-user-role')
            window.STORE.publication.userInfo = {
                user_name: '{{ $item->name }}',
                registered_at: '{{ $item->activated ? $item->registered_at->format('d M Y') : "Pending ({$item->created_at->format('d M Y')})" }}',
                last_login_at: '{{ $item->activated && $item->last_login_at ? $item->last_login_at->format('d M Y, H:i') : null }}',
                resend_registration_link: '{{ !$item->activated ? route('admin.users.resend.registrationEmail', ['user' => $item]) : null }}',
                is_activated: {{ $item->activated }}
            }
        @endcan
    @endunless
    @if ($item->id == $currentUser->id)
        window.STORE.publication.withPublicationToggle = false
    @endif
@endpush

@push('extra_js')
  <script>
    const formFields = {!! json_encode($form_fields) !!};
    const groupPermissionMapping = {!! json_encode($groupPermissionMapping) !!};
    var selectedGroups = formFields.browsers.groups;

    window.vm.$store.subscribe((mutation, state) => {
        const { type, payload } = mutation;
        switch (type) {
          case 'saveSelectedItems':
            selectedGroups = JSON.parse(JSON.stringify(payload));
            selectedGroups.forEach((group) => {
              const permissions = groupPermissionMapping[group['id']];
              permissions.forEach((permission) => {
                const fieldName = `${permission['permissionable_module']}_${permission['permissionable_id']}_permission`;
                const currentPermission = state['form']['fields'].find(function(e) {
                    return e.name === fieldName;
                });
                // Only update when the permission is none.
                if (!currentPermission || currentPermission.value === '') {
                  const field = {
                    name: fieldName,
                    value: 'view-item'
                  };
                  window.vm.$store.commit('updateFormField', field);
                }
              })
            })
            break;

          case 'destroySelectedItem':
            const group = selectedGroups[payload.index];
            const permissions = groupPermissionMapping[group['id']];
            permissions.forEach((permission) => {
              const fieldName = `${permission['permissionable_module']}_${permission['permissionable_id']}_permission`;
              const currentPermission = state['form']['fields'].find(function(e) {
                    return e.name === fieldName;
                });
              if (currentPermission && currentPermission.value === 'view-item') {
                const field = {
                  name: fieldName,
                  value: ''
                };
                window.vm.$store.commit('updateFormField', field);
              }
            })
            break;
        }
    })
  </script>
@endpush
