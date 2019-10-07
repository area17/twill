@extends('twill::layouts.form', [
    'contentFieldsetLabel' => $contentFieldsetLabel ?? 'Edit settings',
    'controlLanguagesPublication' => false
])

@section('contentFields')
    @yield('fields')
@stop

@push('vuexStore')
  window['{{config('twill.browser')}}'].STORE.publication.submitOptions = {
    update: [
      {
        name: 'update',
        text: 'Update'
      },
      {
        name: 'cancel',
        text: 'Cancel'
      }
    ]
  }
@endpush
