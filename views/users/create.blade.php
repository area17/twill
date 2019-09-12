@formField('input', [
    'name' => 'name',
    'label' => 'Name',
    'maxlength' => 70
])

@unless($item ?? null)
    @formField('input', [
        'name' => 'email',
        'label' => 'Email',
        'type' => 'email'
    ])
    @can('manage-users')
        @formField('select', [
            'name' => "role_id",
            'label' => "Role",
            'native' => true,
            'options' => $roleList,
            'placeholder' => 'Select a role'
        ])
    @endcan
@endunless
