@extends('twill::layouts.main')

@php
    $emptyMessage = $emptyMessage ?? twillTrans('twill::lang.dashboard.empty-message');
    $isDashboard = true;
    $translate = true;
@endphp

@push('extra_css')
    @if(app()->isProduction())
        <link href="{{ twillAsset('main-dashboard.css') }}" rel="preload" as="style" crossorigin/>
    @endif
    @unless(config('twill.dev_mode', false))
        <link href="{{ twillAsset('main-dashboard.css') }}" rel="stylesheet" crossorigin/>
    @endunless
@endpush

@push('extra_js_head')
    @if(app()->isProduction())
        <link href="{{ twillAsset('main-dashboard.js') }}" rel="preload" as="script" crossorigin/>
    @endif
@endpush

@section('appTypeClass', 'body--dashboard')

@section('primaryNavigation')
    @if (config('twill.enabled.search', false))
        <div class="dashboardSearch" id="searchApp" v-cloak>
            <a17-search endpoint="{{ route(config('twill.dashboard.search_endpoint')) }}" type="dashboard" placeholder="{{ twillTrans('twill::lang.dashboard.search-placeholder') }}"></a17-search>
        </div>
    @endif
@stop

@section('content')
    <div class="dashboard">
        <a17-shortcut-creator :entities="{{ json_encode($shortcuts ?? []) }}"></a17-shortcut-creator>

        <div class="container">
            @if(($facts ?? false) || (!$drafts->isEmpty()))
                <div class="wrapper wrapper--reverse">
                    <aside class="col col--aside">
                        @if($facts ?? false)
                            <a17-stat-feed :facts="{{ json_encode($facts ?? []) }}">
                                {{ twillTrans('twill::lang.dashboard.statitics') }}
                            </a17-stat-feed>
                        @endif

                        @if(!$drafts->isEmpty())
                            <a17-feed :entities="{{ json_encode($drafts ?? []) }}">{{ twillTrans('twill::lang.dashboard.my-drafts') }}</a17-feed>
                        @endif
                    </aside>
                    <div class="col col--primary">
                        @endif
                        <a17-activity-feed empty-message="{{ $emptyMessage }}" ajax-base-url="{{$ajaxBaseUrl}}"></a17-activity-feed>
                        @if(($facts ?? false) || (!$drafts->isEmpty()))
                    </div>
                </div>
            @endif
        </div>
    </div>
@stop

@section('initialStore')
    window['{{ config('twill.js_namespace') }}'].STORE.datatable = {}

    window['{{ config('twill.js_namespace') }}'].STORE.datatable.mine = {!! json_encode($myActivityData) !!}
    window['{{ config('twill.js_namespace') }}'].STORE.datatable.all = {!! json_encode($allActivityData) !!}

    window['{{ config('twill.js_namespace') }}'].STORE.datatable.data = window['{{ config('twill.js_namespace') }}'].STORE.datatable.all
    window['{{ config('twill.js_namespace') }}'].STORE.datatable.columns = {!! json_encode($tableColumns) !!}
@stop


@push('extra_js')
    <script src="{{ twillAsset('main-dashboard.js') }}" crossorigin></script>
@endpush
