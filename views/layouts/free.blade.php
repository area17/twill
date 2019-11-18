@extends('twill::layouts.main')

@section('appTypeClass', 'body--free')

@section('content')
  <div class="free">
    <div class="container">
      @yield('contentHTML')
    </div>
  </div>
  <a17-modal class="modal--browser" ref="browser" mode="medium" :force-close="true">
      <a17-browser></a17-browser>
  </a17-modal>
@stop

@section('initialStore')
    window.STORE.medias.crops = {!! json_encode(config('twill.settings.crops') ?? []) !!}
    window.STORE.medias.selected = {}

    window.STORE.browser = {}
    window.STORE.browser.selected = {}
@stop

@push('extra_js')
    <script src="{{ mix('/assets/admin/js/manifest.js') }}"></script>
    <script src="{{ mix('/assets/admin/js/vendor.js') }}"></script>
    <script src="{{ mix('/assets/admin/js/main-free.js') }}"></script>
@endpush
