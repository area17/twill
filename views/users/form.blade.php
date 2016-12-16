@extends('cms-toolkit::layouts.resources.form')

@php
    $isSuperAdmin = isset($form_fields['role']) ? $form_fields['role'] === 'SUPERADMIN' : false;
@endphp

@section('form')
    {{ Form::model($form_fields, $form_options) }}
    {{-- <section class="box"> --}}
        <div class="columns">
            <div class="col">
                <section class="box">
                    <header class="header_small">
                        <h3><b>{{ isset($form_fields['name']) ? $form_fields['name'] . ($isSuperAdmin ? ' (SuperAdmin)' : '') : 'New user' }}</b></h3>
                    </header>
                    @formField('input', ['field' => 'name', 'field_name' => 'Name'])
                    @formField('input', ['field' => 'email', 'field_name' => 'Email'] + (isset($form_fields['id']) ? ['disabled' => 'disabled'] : []))
                </section>
                @formField('medias', ['media_role' => 'profile'])
            </div>
            <div class="col">
                @formField('publish_status')
                @can('edit-user-role')
                    @if((!isset($form_fields['id']) || (!$isSuperAdmin && ($form_fields['id'] !== $currentUser->id))))
                        <section class="box">
                            <header class="header_small">
                                <h3><b>Role</b></h3>
                            </header>
                            @formField('select', [
                                'field' => "role",
                                'field_name' => "",
                                'list' => $roleList,
                                'data_behavior' => 'selector',
                                'placeholder' => 'Select a role'
                            ])
                        </section>
                    @endif
                @endcan
            </div>
        </div>
    {{-- </section> --}}
@stop
