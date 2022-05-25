@extends('twill::layouts.form')

@section('contentFields')
    <x-twill::input
        name="description"
        label="description"
        :translated="true"
        :maxlength="100"
    />

    {{--
    -- This is the repeater field allow to reference partners. We chose a name project_partner to indicate that it is
    -- a repeater specifically for selecting partners and filling in their role.
    -- See repeaters/project_partner.blade.php for the form itself.
    --}}
    <x-twill::repeater
        label="Partners"
        type="project_partner"
        :allow-create="false"
        relation="partners"
        :browser-module="[
            'label' => 'Partner',
            'name' => 'partners',
        ]"
    />

    <x-twill::repeater type="comment"/>

    <x-twill::block-editor/>
@stop
