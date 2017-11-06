@extends('cms-toolkit::layouts.form')

@php
    $isSuperAdmin = isset($form_fields['role']) ? $form_fields['role'] === 'SUPERADMIN' : false;
@endphp

@section('form')
    {{ Form::model($form_fields, $form_options) }}
        @can('edit-user-role')
            @formField('publish_status', [
                'hiddenTitle' => 'Disabled',
                'publishedTitle' => 'Enabled'
            ])
        @endcan
        <section class="box">
            <header class="header_small">
                <h3><b>{{ isset($form_fields['name']) ? $form_fields['name'] . ($isSuperAdmin ? ' (SuperAdmin)' : '') : 'New user' }}</b></h3>
            </header>
            @formField('input', ['field' => 'name', 'field_name' => 'Name'])
            @formField('input', ['field' => 'email', 'field_name' => 'Email'] + (isset($form_fields['id']) ? ['disabled' => 'disabled'] : []))
            @can('edit-user-role')
                @if((!isset($form_fields['id']) || (!$isSuperAdmin && ($form_fields['id'] !== $currentUser->id))))
                    @formField('select', [
                        'field' => "role",
                        'field_name' => "Role",
                        'list' => $roleList,
                        'data_behavior' => 'selector',
                        'placeholder' => 'Select a role'
                    ])
                @endif
            @endcan
        </section>
        @if(config('cms-toolkit.enabled.users-image'))
            @formField('medias', ['media_role' => 'profile'])
        @endif
@stop
