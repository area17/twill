@extends('cms-toolkit::layouts.main')

@section('appTypeClass', 'app--form')

@section('content')
    <div class="form">
        <form action="{{ $saveUrl }}" v-sticky data-sticky-id="navbar" data-sticky-offset="0" data-sticky-topoffset="12" v-on:submit.prevent="submitForm">
            <div class="navbar navbar--sticky" data-sticky-top="navbar">
                @php
                    $additionalFieldsets = $additionalFieldsets ?? [];
                    array_unshift($additionalFieldsets, [
                        'fieldset' => 'content',
                        'label' => 'Content'
                    ]);
                @endphp
                <a17-sticky-nav data-sticky-target="navbar" :items="{{ json_encode($additionalFieldsets) }}">
                    <a17-title-editor slot="title"></a17-title-editor>
                    <a17-langswitcher slot="actions"></a17-langswitcher>
                </a17-sticky-nav>
            </div>
            <div class="container">
                <div class="wrapper wrapper--reverse" v-sticky data-sticky-id="publisher" data-sticky-offset="80">
                    <aside class="col col--aside">
                        <a17-publisher data-sticky-target="publisher"></a17-publisher>
                    </aside>
                    <section class="col col--primary">
                        <a17-fieldset title="Content" id="content" data-sticky-top="publisher">
                            @yield('contentFields')
                        </a17-fieldset>

                        @yield('fieldsets')
                    </section>
                </div>
            </div>

            <!-- Move to trash -->
            <a17-modal class="modal--tiny modal--form modal--withintro" ref="moveToTrashModal" title="Move To Trash">
                <p class="modal--tiny-title"><strong>Are you sure ?</strong></p>
                <p>This change can't be undone.</p>
                <a17-inputframe>
                    <a17-button variant="validate">Ok</a17-button> <a17-button variant="aslink" @click="$refs.moveToTrashModal.close()"><span>Cancel</span></a17-button>
                </a17-inputframe>
            </a17-modal>
        </form>
    </div>
    <a17-modal class="modal--browser" ref="browser" mode="medium">
        <a17-browser />
    </a17-modal>
    <a17-overlay ref="preview" title="Preview changes">
        <a17-previewer />
    </a17-overlay>
@stop

@section('initialStore')

    window.STORE.languages = {}
    window.STORE.languages.all = {!! json_encode(getLanguagesForVueStore($form_fields)) !!}
    window.STORE.form = {
        title: '{{ $item->title }}',
        permalink: '{{ $item->slug ?? '' }}',
        baseUrl: '{{ $baseUrl }}',
        saveUrl: '{{ $saveUrl }}',
        fields: []
    }

    window.STORE.publication = {
        withPublicationToggle: {{ json_encode($item->isFillable('published')) }},
        published: {{ json_encode($item->published) }},
        withPublicationTimeframe: {{ json_encode($item->isFillable('publish_start_date')) }},
        startDate: '{{ $item->publish_start_date ?? '' }}',
        endDate: '{{ $item->publish_end_date ?? '' }}',
        visibility: '{{ $item->isFillable('public') ? ($item->public ? 'public' : 'private') : false }}'
    }

    window.STORE.revisions = {!! json_encode($revisions)  !!}
    window.STORE.medias.crops = {!! json_encode($item->mediasParams + config('cms-toolkit.block-editor.crops')) !!}
    window.STORE.medias.selected = {}
    window.STORE.browser = {}
    window.STORE.browser.selected = {}

    window.STORE.form.availableRepeaters = {
        video: {
            title: 'Video',
            trigger: 'Add Videos',
            component: 'a17-block-test', // This will be project specific
            max: 4
        },
        gridItem: {
            title: 'Grid Item',
            trigger: 'Add Grid Item',
            component: 'a17-block-video', // This will be project specific
            max: 4
        },
        gridItemMore: {
            title: 'Grid Item',
            trigger: 'Add Grid Item',
            component: 'a17-block-video', // This will be project specific
            max: 6
        }
    }

    window.APIKEYS = {
        'googleMapApi': '{{ config('services.google.maps_api_key') }}'
    }
@stop

@push('extra_js')
    <script src="{{ mix('/assets/admin/js/manifest.js') }}"></script>
    <script src="{{ mix('/assets/admin/js/vendor.js') }}"></script>
    <script src="{{ mix('/assets/admin/js/main-form.js') }}"></script>
@endpush
