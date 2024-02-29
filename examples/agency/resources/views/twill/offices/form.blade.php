@extends('twill::layouts.form')

@section('contentFields')
    @formField('input', [
        'name' => 'description',
        'label' => 'Short Description',
        'translated' => true,
        'maxlength' => 100,
        'type' => 'textarea',
        'rows' => 3,
        'required' => true
    ])

    @formField('medias', [
        'name' => 'cover',
        'label' => 'Office',
        'max' => 5,
        'fieldNote' => 'Minimum image width: 1500px (Multiple images turn into a slideshow)'
    ])

    @formField('input', [
        'name' => 'email',
        'label' => 'Email',
    ])

    @formField('input', [
        'name' => 'phone',
        'label' => 'Phone Number',
    ])

    @formField('input', [
        'name' => 'street',
        'label' => 'Street',
    ])

    @formField('input', [
        'name' => 'city',
        'label' => 'City',
        'translated' => true,
    ])

    @formField('input', [
        'name' => 'zip_code',
        'label' => 'Zipcode',
    ])

    @formField('input', [
        'name' => 'country',
        'label' => 'Country',
        'translated' => true,
    ])

    @formField('input', [
        'name' => 'directions',
        'label' => 'Directions URL',
    ])

    @formField('select', [
        'name' => 'timezone',
        'label' => 'Timezone',
        'options' => $timezones
    ])

@stop
