@extends('twill::layouts.form', [
    'contentFieldsetLabel' => 'Account',
    'editModalTitle' => 'Edit user name',
    'reloadOnSuccess' => false
])

@section('contentFields')
  @can('edit-user')
    <p><strong>Registered at: </strong> {{ $item->activated ? $item->registered_at->format('d M Y') : "Pending ({$item->created_at->format('d M Y')})" }}</p>
    @if($item->activated)
    
      @if($item->last_login_at)
        <p><strong>Last login: </strong> {{ $item->last_login_at->format('d M Y, H:i') }}</p>
      @endif

      @formField('checkbox', [
        'name' => 'reset_password',
        'label' => 'Reset Password'
      ])

      @component('twill::partials.form.utils._connected_fields', [
        'fieldName' => 'reset_password',
        'fieldValues' => true,
        'renderForBlocks' => false
      ])
        @formField('input', [
          'name' => 'new_password',
          'type' => 'password',
          'label' => 'Reset password',
          'required' => true,
          'maxlength' => 50,
        ])
      @endcomponent
    @else
      <br />
      <a type="submit" href="{{ route('admin.users.resend.registrationEmail', ['user' => $item]) }}">Resend registration email</a>

      @formField('checkbox', [
          'name' => 'register_account_now',
          'label' => 'Register Account Now'
      ])
      @component('twill::partials.form.utils._connected_fields', [
        'fieldName' => 'register_account_now',
        'fieldValues' => true,
        'renderForBlocks' => false
      ])
        @formField('input', [
          'name' => 'new_password',
          'type' => 'password',
          'label' => 'New password',
          'required' => true,
          'maxlength' => 50,
        ])

        @formField('checkbox', [
          'name' => 'require_password_change',
          'label' => 'Require password change at next login'
        ])
      @endcomponent
    @endif
  @endcan

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
      @formField('browser', [
        'moduleName' => 'groups',
        'name' => 'groups',
        'label' => 'Groups',
        'sortable' => false,
        'max' => 100
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
        @foreach($permission_modules as $module_name => $module_items)
            <a17-fieldset title='{{ ucfirst($module_name) . " Permissions"}}' id='{{ $module_name }}'>
                <h2>{{ ucfirst($module_name) .' permission' }}</h2>
                @foreach ($module_items as $module_item)
                    @formField('select', [
                        'name' => $module_name . '_' . $module_item->id . '_permission',
                        'label' => $module_item->title,
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
    @if ($item->id == $currentUser->id)
        window.STORE.publication.withPublicationToggle = false
    @endif
@endpush