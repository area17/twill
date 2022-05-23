@extends('twill::layouts.form', [
    'contentFieldsetLabel' => $contentFieldsetLabel ?? __('twill::lang.settings.fieldset-label'),
    'controlLanguagesPublication' => false
])

@section('contentFields')
    @yield('fields')
@stop

@push('vuexStore')
  window['{{ config('twill.js_namespace') }}'].STORE.publication.submitOptions = {
    update: [
      {
        name: 'update',
        text: {!! json_encode(__('twill::lang.settings.update')) !!}
      },
      {
        name: 'cancel',
        text: {!! json_encode(__('twill::lang.settings.cancel')) !!}
      }
    ]
  }
@endpush
