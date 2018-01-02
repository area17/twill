@formField('input', [
    'name' => 'name',
    'label' => 'Name'
])

@unless($item ?? null)
    @formField('input', [
        'name' => 'email',
        'label' => 'Email'
    ])
    @can('edit-user-role')
        @formField('select', [
            'name' => "role",
            'label' => "Role",
            'native' => true,
            'options' => $roleList,
            'placeholder' => 'Select a role'
        ])
    @endcan
@endunless
