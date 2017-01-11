@extends('cms-toolkit::layouts.resources.index', [
    'delete' => $currentUser->can('edit-user-role'),
    'create' => $currentUser->can('edit-user-role'),
    'publish' => $currentUser->can('edit-user-role'),
    'columns' => [
        'image' => [
            'title' => 'Image',
            'thumb' => true,
            'variant' => [
                'role' => 'profile',
                'crop' => 'square',
            ],
        ],
        'name' => [
            'title' => 'Name',
            'edit_link' => true,
            'field' => 'name'
        ],
        'email' => [
            'title' => 'Email',
            'field' => 'email'
        ],
        'role' => [
            'title' => 'Role',
            'field' => 'role_value'
        ],
    ]
])
