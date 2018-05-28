@extends('twill::layouts.main')

@php
    $emptyMessage = $emptyMessage ?? "You don't have any activity yet.";
@endphp

@section('appTypeClass', 'body--dashboard')

@if($search ?? false)
@section('primaryNavigation')
  <div class="dashboardSearch" id="searchApp" v-cloak>
    <a17-search endpoint="http://www.mocky.io/v2/5a7b81d43000004b0028bf3d" type="dashboard"></a17-search>
  </div>
@stop
@endif

@section('content')
    <div class="dashboard">
        <a17-shortcut-creator :entities="{{ json_encode($shortcuts ?? []) }}"></a17-shortcut-creator>

        <div class="container">
            <div class="wrapper wrapper--reverse">
                <aside class="col col--aside">
                    <a17-stat-feed :facts="{{ json_encode($facts ?? []) }}">
                        Statistics
                    </a17-stat-feed>
                </aside>
                <div class="col col--primary">
                    <a17-activity-feed empty-message="{{ __($emptyMessage)  }}"></a17-activity-feed>
                </div>
            </div>
        </div>
    </div>
@stop

@section('initialStore')
    window.STORE.datatable = {}

    window.STORE.datatable.mine = {!! json_encode($myActivityData) !!}
    window.STORE.datatable.all = {!! json_encode($allActivityData) !!}

    window.STORE.datatable.data = window.STORE.datatable.all
    window.STORE.datatable.columns = {!! json_encode($tableColumns) !!}
@stop

@push('extra_js')
    <script src="{{ mix('/assets/admin/js/manifest.js') }}"></script>
    <script src="{{ mix('/assets/admin/js/vendor.js') }}"></script>
    <script src="{{ mix('/assets/admin/js/main-dashboard.js') }}"></script>
@endpush
