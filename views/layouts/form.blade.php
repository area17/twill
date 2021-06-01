@extends('twill::layouts.main')

@section('appTypeClass', 'body--form')

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
        <link href="{{ twillAsset('main-form.js') }}" rel="preload" as="script" crossorigin/>
    @endif
@endpush

@php
    $editor = $editor ?? false;
    $translate = $translate ?? false;
    $translateTitle = $translateTitle ?? $translate ?? false;
    $titleFormKey = $titleFormKey ?? 'title';
    $customForm = $customForm ?? false;
    $controlLanguagesPublication = $controlLanguagesPublication ?? true;
    $disableContentFieldset = $disableContentFieldset ?? false;
    $editModalTitle = ($createWithoutModal ?? false) ? twillTrans('twill::lang.modal.create.title') : null;
@endphp

@section('content')
    <div class="form" v-sticky data-sticky-id="navbar" data-sticky-offset="0" data-sticky-topoffset="12" >
        <div class="navbar navbar--sticky" data-sticky-top="navbar">
            @php
                $additionalFieldsets = $additionalFieldsets ?? [];
                if(!$disableContentFieldset) {
                    array_unshift($additionalFieldsets, [
                        'fieldset' => 'content',
                        'label' => $contentFieldsetLabel ?? twillTrans('twill::lang.form.content')
                    ]);
                }
            @endphp
            <a17-sticky-nav data-sticky-target="navbar" :items="{{ json_encode($additionalFieldsets) }}">
                <a17-title-editor
                    name="{{ $titleFormKey }}"
                    :editable-title="{{ json_encode($editableTitle ?? true) }}"
                    custom-title="{{ $customTitle ?? '' }}"
                    custom-permalink="{{ $customPermalink ?? '' }}"
                    slot="title"
                    @if($createWithoutModal ?? false) :show-modal="true" @endif
                    @if(isset($editModalTitle)) modal-title="{{ $editModalTitle }}" @endif
                >
                    <template slot="modal-form">
                        @partialView(($moduleName ?? null), 'create')
                    </template>
                </a17-title-editor>
                <div slot="actions">
                    <a17-langswitcher :all-published="{{ json_encode(!$controlLanguagesPublication) }}"></a17-langswitcher>
                    <a17-button v-if="editor" type="button" variant="editor" size="small" @click="openEditor(-1)">
                        <span v-svg symbol="editor"></span>{{ twillTrans('twill::lang.form.editor') }}
                    </a17-button>
                </div>
            </a17-sticky-nav>
        </div>
        <form action="{{ $saveUrl }}" novalidate method="POST" @unless($customForm) v-on:submit.prevent="submitForm" @endif>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="container">
                <div class="wrapper wrapper--reverse" v-sticky data-sticky-id="publisher" data-sticky-offset="80">
                    <aside class="col col--aside">
                        <div class="publisher" data-sticky-target="publisher">
                            <a17-publisher
                                {!! !empty($publishDateDisplayFormat) ? "date-display-format='{$publishDateDisplayFormat}'" : '' !!}
                                {!! !empty($publishDateFormat) ? "date-format='{$publishDateFormat}'" : '' !!}
                                {!! !empty($publishDate24Hr) && $publishDate24Hr ? ':date_24h="true"' : '' !!}
                                :show-languages="{{ json_encode($controlLanguagesPublication) }}"
                            >
                                @yield('publisherRows')
                            </a17-publisher>
                            <a17-page-nav
                                placeholder="Go to page"
                                previous-url="{{ $parentPreviousUrl ?? '' }}"
                                next-url="{{ $parentNextUrl ?? '' }}"
                            ></a17-page-nav>
                            @hasSection('sideFieldset')
                                <a17-fieldset title="{{ $sideFieldsetLabel ?? 'Options' }}" id="options">
                                    @yield('sideFieldset')
                                </a17-fieldset>
                            @endif
                            @yield('sideFieldsets')
                        </div>
                    </aside>
                    <section class="col col--primary" data-sticky-top="publisher">
                        @unless($disableContentFieldset)
                            <a17-fieldset title="{{ $contentFieldsetLabel ?? twillTrans('twill::lang.form.content') }}" id="content">
                                @yield('contentFields')
                            </a17-fieldset>
                        @endunless

                        @yield('fieldsets')
                    </section>
                </div>
            </div>
            <a17-spinner v-if="loading"></a17-spinner>
        </form>
    </div>
    <a17-modal class="modal--browser" ref="browser" mode="medium" :force-close="true">
        <a17-browser></a17-browser>
    </a17-modal>
    <a17-modal class="modal--browser" ref="browserWide" mode="wide" :force-close="true">
        <a17-browser></a17-browser>
    </a17-modal>
    <a17-editor v-if="editor" ref="editor" bg-color="{{ config('twill.block_editor.background_color') ?? '#FFFFFF' }}"></a17-editor>
    <a17-previewer ref="preview"></a17-previewer>
        <a17-dialog ref="warningContentEditor" modal-title="{{ twillTrans('twill::lang.form.dialogs.delete.title') }}" confirm-label="{{ twillTrans('twill::lang.form.dialogs.delete.confirm') }}">
        <p class="modal--tiny-title"><strong>{{ twillTrans('twill::lang.form.dialogs.delete.delete-content') }}</strong></p>
        <p>{!! twillTrans('twill::lang.form.dialogs.delete.confirmation') !!}</p>
    </a17-dialog>
@stop

@section('initialStore')
    window['{{ config('twill.js_namespace') }}'].STORE.form = {
        baseUrl: '{{ $baseUrl ?? '' }}',
        saveUrl: '{{ $saveUrl }}',
        previewUrl: '{{ $previewUrl ?? '' }}',
        restoreUrl: '{{ $restoreUrl ?? '' }}',
        blocks: {
            available: {},
            used: {},
        },
        blockPreviewUrl: '{{ $blockPreviewUrl ?? '' }}',
        availableRepeaters: {!! $availableRepeaters ?? '{}' !!},
        repeaters: {!! json_encode(($form_fields['repeaters'] ?? []) + ($form_fields['blocksRepeaters'] ?? [])) !!},
        fields: [],
        editor: {{ $editor ? 'true' : 'false' }},
        isCustom: {{ $customForm ? 'true' : 'false' }},
        reloadOnSuccess: {{ ($reloadOnSuccess ?? false) ? 'true' : 'false' }}
    }

    window['{{ config('twill.js_namespace') }}'].STORE.publication = {
        withPublicationToggle: {{ json_encode(($publish ?? true) && isset($item) && $item->isFillable('published')) }},
        published: {{ isset($item) && $item->published ? 'true' : 'false' }},
        createWithoutModal: {{ isset($createWithoutModal) && $createWithoutModal ? 'true' : 'false' }},
        withPublicationTimeframe: {{ json_encode(($schedule ?? true) && isset($item) && $item->isFillable('publish_start_date')) }},
        publishedLabel: '{{ $customPublishedLabel ?? twillTrans('twill::lang.main.published') }}',
        draftLabel: '{{ $customDraftLabel ?? twillTrans('twill::lang.main.draft') }}',
        submitDisableMessage: '{{ $submitDisableMessage ?? '' }}',
        startDate: '{{ $item->publish_start_date ?? '' }}',
        endDate: '{{ $item->publish_end_date ?? '' }}',
        visibility: '{{ isset($item) && $item->isFillable('public') ? ($item->public ? 'public' : 'private') : false }}',
        reviewProcess: {!! isset($reviewProcess) ? json_encode($reviewProcess) : '[]' !!},
        submitOptions: @if(isset($item) && $item->cmsRestoring) {
            draft: [
                {
                    name: 'restore',
                    text: '{{ twillTrans('twill::lang.publisher.restore-draft') }}'
                },
                {
                    name: 'restore-close',
                    text: '{{ twillTrans('twill::lang.publisher.restore-draft-close') }}'
                },
                {
                    name: 'restore-new',
                    text: '{{ twillTrans('twill::lang.publisher.restore-draft-new') }}'
                },
                {
                    name: 'cancel',
                    text: '{{ twillTrans('twill::lang.publisher.cancel') }}'
                }
            ],
            live: [
                {
                    name: 'restore',
                    text: '{{ twillTrans('twill::lang.publisher.restore-live') }}'
                },
                {
                    name: 'restore-close',
                    text: '{{ twillTrans('twill::lang.publisher.restore-live-close') }}'
                },
                {
                    name: 'restore-new',
                    text: '{{ twillTrans('twill::lang.publisher.restore-live-new') }}'
                },
                {
                    name: 'cancel',
                    text: '{{ twillTrans('twill::lang.publisher.cancel') }}'
                }
            ],
            update: [
                {
                    name: 'restore',
                    text: '{{ twillTrans('twill::lang.publisher.restore-live') }}'
                },
                {
                    name: 'restore-close',
                    text: '{{ twillTrans('twill::lang.publisher.restore-live-close') }}'
                },
                {
                    name: 'restore-new',
                    text: '{{ twillTrans('twill::lang.publisher.restore-live-new') }}'
                },
                {
                    name: 'cancel',
                    text: '{{ twillTrans('twill::lang.publisher.cancel') }}'
                }
            ]
        } @else null @endif
    }

    window['{{ config('twill.js_namespace') }}'].STORE.revisions = {!! json_encode($revisions ?? []) !!}

    window['{{ config('twill.js_namespace') }}'].STORE.parentId = {{ $item->parent_id ?? 0 }}
    window['{{ config('twill.js_namespace') }}'].STORE.parents = {!! json_encode($parents ?? [])  !!}

    window['{{ config('twill.js_namespace') }}'].STORE.medias.crops = {!! json_encode(($item->mediasParams ?? []) + config('twill.block_editor.crops') + (config('twill.settings.crops') ?? [])) !!}
    window['{{ config('twill.js_namespace') }}'].STORE.medias.selected = {}

    window['{{ config('twill.js_namespace') }}'].STORE.browser = {}
    window['{{ config('twill.js_namespace') }}'].STORE.browser.selected = {}

    window['{{ config('twill.js_namespace') }}'].APIKEYS = {
        'googleMapApi': '{{ config('twill.google_maps_api_key') }}'
    }
@stop

@prepend('extra_js')
    @includeWhen(config('twill.block_editor.inline_blocks_templates', true), 'twill::partials.form.utils._blocks_templates')
    <script src="{{ twillAsset('main-form.js') }}" crossorigin></script>
@endprepend
