@formField('input', [
    'name' => 'name',
    'label' => __('twill::lang.user-management.name')
])

@unless($item ?? null)
    @formField('input', [
        'name' => 'email',
        'label' => __('twill::lang.user-management.email')
    ])
    @can('manage-users')
        @formField('select', [
            'name' => "role",
            'label' => __('twill::lang.user-management.role'),
            'native' => true,
            'options' => $roleList,
            'placeholder' => 'Select a role'
        ])
    @endcan
@endunless
