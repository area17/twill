@extends('twill::layouts.main')

@section('appTypeClass', 'body--hasSideNav')

@push('extra_css')
    @if(app()->isProduction())
        <link href="{{ twillAsset('main-form.css') }}" rel="preload" as="style" crossorigin/>
    @endif

    @unless(config('twill.dev_mode', false))
        <link href="{{ twillAsset('main-form.css') }}" rel="stylesheet" crossorigin/>
    @endunless
@endpush

@push('extra_js_head')
    @if(app()->isProduction())
        <link href="{{ twillAsset('main-dam.js') }}" rel="preload" as="script" crossorigin/>
    @endif
@endpush

@section('content')
    @yield('customPageContent')
    <a17-modal class="modal--browser" ref="browser" mode="medium" :force-close="true">
        <a17-browser></a17-browser>
    </a17-modal>
@stop

@section('initialStore')
    window['{{ config('twill.js_namespace') }}'].STORE.medias.crops = {!! json_encode(config('twill.settings.crops') ?? []) !!}
    window['{{ config('twill.js_namespace') }}'].STORE.medias.selected = {}
    window['{{ config('twill.js_namespace') }}'].STORE.medias.browserFields = {}

    @php
        $mediaBrowsers = [];
        foreach (config('twill.media_library.browsers') as $browser) {
            $mediaBrowsers[] = array_merge(
                $browser,
                [
                    'endpoint' => moduleRoute($browser['name'], $browser['prefix'] ?? null, 'browser', [], false),
                ]
            );
        }

        $tagfields = [
            ['name' => 'tags', 'label' => 'Keywords', 'multiple' => true, 'searchable' => true, 'taggable' => true]
        ];

        foreach (config('twill.media_library.extra_tag_fields') as $field) {
            $tagfields[] = [
                'name' => $field['name'],
                'label' => $field['label'],
                'multiple' => $field['multiple'] ?? false,
                'key' => 'label'
            ];
        }
    @endphp
    window['{{ config('twill.js_namespace') }}'].STORE.medias.browserFields = {!! json_encode($mediaBrowsers) !!}
    window['{{ config('twill.js_namespace') }}'].STORE.medias.tagFields = {!! json_encode($tagfields) !!}

    window['{{ config('twill.js_namespace') }}'].STORE.browser = {}
    window['{{ config('twill.js_namespace') }}'].STORE.browser.selected = {}

    @yield('extraStore')
@stop


@prepend('extra_js')
    <script src="{{ twillAsset('main-dam.js') }}" crossorigin></script>
@endprepend
