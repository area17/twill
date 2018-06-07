@extends('twill::layouts.main')

@php
    $emptyMessage = $emptyMessage ?? "You don't have any activity yet.";
@endphp

@section('appTypeClass', 'body--dashboard')

@section('primaryNavigation')
  <div class="dashboardSearch" id="searchApp" v-cloak>
    <a17-search endpoint="{{ route(config('twill.dashboard.search_endpoint')) }}" type="dashboard"></a17-search>
  </div>
@stop

@section('content')
    <div class="dashboard">
        <a17-shortcut-creator :entities="{{ json_encode($shortcuts ?? []) }}"></a17-shortcut-creator>

        <div class="container">
            @if($facts ?? false)
                <div class="wrapper wrapper--reverse">
                    <aside class="col col--aside">
                        <a17-stat-feed :facts="{{ json_encode($facts ?? []) }}">
                            Statistics
                        </a17-stat-feed>
                            <a17-feed :entities="[ { name: 'Garden Museum', url: '/fe/templates/form', type: 'Work' }, { name: 'London Design Festival 2018', url: '/fe/templates/form', type: 'News' }, { name: 'The Hollow Woods: Storytelling', url: '/fe/templates/form', type: 'News' }, { name: 'William Russell: A Collection', url: '/fe/templates/form', type: 'News' }, { name: 'Michael Bierut', url: '/fe/templates/form', type: 'Partner' }, { name: 'Musuem für Film umd Fernsehen', url: '/fe/templates/form', type: 'Work' }, { name: 'Shakespeare in the Park 2017', url: '/fe/templates/form', type: 'News' }, { name: 'Artifact', url: '/fe/templates/form', type: 'News' }, { name: 'Dance Ink (Vol. 8, No. 2)', url: '/fe/templates/form', type: 'Work' } ]">My drafts</a17-feed>
                    </aside>
                    <div class="col col--primary">
            @endif
                <a17-activity-feed empty-message="{{ __($emptyMessage)  }}"></a17-activity-feed>
            @if($facts ?? false)
                </div>
            </div>
            @endif
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
