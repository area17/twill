@extends('twill::layouts.form')

@section('contentFields')
    @formField('input', [
        'name' => 'description',
        'label' => 'Description',
        'maxlength' => 250,
        'placeholder' => 'Enter the description for the group',
        'type' => 'textarea',
        'rows' => 3
    ])

    @formField('browser', [
        'moduleName' => 'users',
        'name' => 'users',
        'notes' => 'Add members to the groups',
        'label' => 'Users',
        'max' => 999
    ])

    @if(Config::get('twill.permissions.level') == 'roleGroup')
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
                @foreach($permissionModules as $moduleName => $moduleItems)
                    @formField('select', [
                        'name' => 'module_' . $moduleName . '_permissions',
                        'label' => ucfirst($moduleName) . ' permissions',
                        'placeholder' => 'Select a permission',
                        'options' => [
                            [
                                'value' => 'none',
                                'label' => 'None'
                            ],
                            [
                                'value' => 'view-module',
                                'label' => 'View ' . $moduleName
                            ],
                            [
                                'value' => 'edit-module',
                                'label' => 'Edit ' . $moduleName
                            ]
                        ]
                    ])
                @endforeach
            @endcomponent
        @endcomponent
    @endif

    @if(Config::get('twill.support_subdomain_admin_routing'))
        @component('twill::partials.form.utils._field_rows', [
            'title' => 'Subdomain Access'
        ])
            @foreach(Config::get('twill.app_names') as $subdomain => $subdomainTitle)
                @formField('checkbox', [
                    'name' => 'subdomain_access_' . $subdomain,
                    'label' => $subdomainTitle
                ])
            @endforeach
        @endcomponent
    @endif
@stop

@if(Config::get('twill.permissions.level') == 'roleGroupModule')
  @can('edit-user-groups')
      @section('fieldsets')
          @foreach($permissionModules as $moduleName => $moduleItems)
              <a17-fieldset title='{{ ucfirst($moduleName) . " Permissions"}}' id='{{ $moduleName }}'>
                  @formField('select_permissions', [
                      'itemsInSelectsTables' => $moduleItems,
                      'labelKey' => 'title',
                      'namePattern' => $moduleName . '_%id%_permission',
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
              </a17-fieldset>
          @endforeach
      @stop
  @endcan

@endif
