@extends('cms-toolkit::layouts.main')

@php
    $title = $title ?? "All Projects";
    $message = $message ?? "What would you like to feature today?";
    $emptyBucketMessage =  $emptyBucketMessage ?? "No files selected.";
    $emptySourceMessage =  $emptySourceMessage ?? "No items found.";
@endphp

@section('appTypeClass', 'app--buckets')

@section('content')
    <a17-buckets title="{{__($title)}}" empty-buckets="{{__($emptyBucketMessage)}}" empty-source="{{__($emptySourceMessage)}}">{{__($message)}}</a17-buckets>
@stop

@section('initialStore')
    window.STORE.buckets = {
        page: 1,
        maxPage: {{ $maxPage }},
        offset: {{ $offset }},
        availableOffsets: {!! json_encode($availableOffsets) !!},
    }

    window.STORE.buckets.filter = {}

    window.STORE.buckets.dataSources =  {!! json_encode($dataSources) !!}

    window.STORE.buckets.items = {!! json_encode($items) !!}

    window.STORE.buckets.source = {!! json_encode($source) !!}
@stop

@push('extra_js')
    <script src="{{ mix('/assets/admin/js/manifest.js') }}"></script>
    <script src="{{ mix('/assets/admin/js/vendor.js') }}"></script>
    <script src="{{ mix('/assets/admin/js/main-buckets.js') }}"></script>
@endpush
