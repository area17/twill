@extends('twill::layouts.form')

@section('contentFields')
    @formField('input', [
        'name' => 'description',
        'label' => 'Description',
        'maxlength' => 100
    ])

    @formField('block_editor', [
        'blocks' => \App\Models\Article::AVAILABLE_BLOCKS,
    ])
@stop
