@extends('twill::layouts.main')

@section('appTypeClass', 'body--buckets')

@php
    $bucketSectionLinks = $bucketSectionLinks ?? [];
@endphp

@push('extra_css')
    @if(app()->isProduction())
        <link href="{{ twillAsset('main-buckets.css') }}" rel="preload" as="style" crossorigin/>
    @endif
    <link href="{{ twillAsset('main-buckets.css') }}" rel="stylesheet" crossorigin/>
@endpush

@push('extra_js_head')
    @if(app()->isProduction())
        <link href="{{ twillAsset('main-buckets.js') }}" rel="preload" as="script" crossorigin/>
    @endif
@endpush

@section('content')
    <a17-buckets
        title="{{ $bucketSourceTitle ?? 'Available items' }}"
        empty-buckets="No items featured."
        empty-source="No items available."
        :restricted="{!! json_encode($restricted ?? true) !!}"
        :extra-actions="{{ json_encode($bucketSectionLinks) }}"
    >
        {{ $bucketsSectionIntro ?? 'What would you like to feature today?' }}
    </a17-buckets>
@stop

@section('initialStore')
    window['{{ config('twill.js_namespace') }}'].STORE.buckets = {
        saveUrl: {!! json_encode($saveUrl) !!},
        items: {!! json_encode($items) !!},
        source: {!! json_encode($source) !!},
        dataSources: {!! json_encode($dataSources) !!},
        page: 1,
        maxPage: {{ $maxPage ?? 1 }},
        offset: {{ $offset ?? 10 }},
        filter: {}
    }
@stop

@push('extra_js')
    <script src="{{ twillAsset('main-buckets.js') }}" crossorigin></script>
@endpush
