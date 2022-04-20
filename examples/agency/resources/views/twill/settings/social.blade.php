@extends('twill::layouts.settings', [
    'contentFieldsetLabel' => 'URLs'
])

@section('contentFields')
    @formField('input', [
        'label' => 'Facebook URL',
        'name' => 'facebook_url',
        'translated' => true
    ])

    @formField('input', [
        'label' => 'Twitter URL',
        'name' => 'twitter_url',
        'translated' => true
    ])

    @formField('input', [
        'label' => 'Instagram URL',
        'name' => 'instagram_url',
        'translated' => true
    ])
@stop


