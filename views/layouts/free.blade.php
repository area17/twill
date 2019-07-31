@extends('twill::layouts.main')

@section('appTypeClass', 'body--free')

@section('content')
    @yield('contentHTML')
@stop

@push('extra_js')
    <script src="{{ mix('/assets/admin/js/manifest.js') }}"></script>
    <script src="{{ mix('/assets/admin/js/vendor.js') }}"></script>
    <script src="{{ mix('/assets/admin/js/main-free.js') }}"></script>
@endpush
