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
    $item = isset($item) ? $item : null;

    // TODO: cache and move out of view
    if (config('twill.enabled.permissions-management')) {
        $users = app()->make('A17\Twill\Repositories\UserRepository')->published()->notSuperAdmin()->get();
        $groups = app()->make('A17\Twill\Repositories\GroupRepository')->get()->map(function ($group) {
            return [
                'name' => $group->id . '_group_authorized',
                'value' => $group->id,
                'label' => $group->name
            ];
        });
    }
@endphp

@section('content')
    <div class="form" v-sticky data-sticky-id="navbar" data-sticky-offset="0" data-sticky-topoffset="12">
        <div class="navbar navbar--sticky" data-sticky-top="navbar">
            @php
                $additionalFieldsets = $additionalFieldsets ?? $formBuilder->getAdditionalFieldsets();
                if(!$disableContentFieldset && $formBuilder->hasFieldsInBaseFieldset()) {
                    array_unshift($additionalFieldsets, [
                        'fieldset' => 'content',
                        'label' => $contentFieldsetLabel ?? twillTrans('twill::lang.form.content')
                    ]);
                }
            @endphp
            <a17-sticky-nav data-sticky-target="navbar" :items="{{ json_encode($additionalFieldsets) }}">
                <a17-title-editor name="{{ $titleFormKey }}" thumbnail="{{ $titleThumbnail ?? '' }}"
                                  :editable-title="{{ json_encode($editableTitle ?? true) }}"
                                  :control-languages-publication="{{ json_encode($controlLanguagesPublication) }}"
                                  custom-title="{{ $customTitle ?? '' }}"
                                  custom-permalink="{{ $customPermalink ?? '' }}"
                                  localized-permalinkbase="{{ json_encode($localizedPermalinkBase ?? '') }}"
                                  localized-custom-permalink="{{ json_encode($localizedCustomPermalink ?? '') }}"
                                  slot="title" @if($createWithoutModal ?? false) :show-modal="true"
                                  @endif @if(isset($editModalTitle)) modal-title="{{ $editModalTitle }}" @endif>
                    <template slot="modal-form">
                        @partialView(($moduleName ?? null), 'create')
                    </template>
                </a17-title-editor>
                <div slot="actions">
                    <a17-langswitcher
                        :all-published="{{ json_encode(!$controlLanguagesPublication) }}"></a17-langswitcher>
                    <a17-button v-if="editor" type="button" variant="editor" size="small"
                                @click="openEditor(-1)">
                        <span v-svg
                              symbol="editor"></span>{{ twillTrans('twill::lang.form.editor') }}
                    </a17-button>
                </div>
            </a17-sticky-nav>
        </div>
        <form action="{{ $saveUrl }}" novalidate method="POST" @if($customForm) ref="customForm"
              @else v-on:submit.prevent="submitForm" @endif>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="container">
                <div class="wrapper wrapper--reverse" v-sticky data-sticky-id="publisher"
                     data-sticky-offset="80">
                    <aside class="col col--aside">
                        <div class="publisher" data-sticky-target="publisher">
                            <a17-publisher
                                {!! !empty($publishDateDisplayFormat) ? "date-display-format='{$publishDateDisplayFormat}'" : '' !!} {!! !empty($publishDateFormat) ? "date-format='{$publishDateFormat}'" : '' !!} {!! !empty($publishDate24Hr) && $publishDate24Hr ? ':date_24h="true"' : '' !!} :show-languages="{{ json_encode($controlLanguagesPublication) }}">
                                @yield('publisherRows')
                            </a17-publisher>
                            <a17-page-nav placeholder="Go to page"
                                          previous-url="{{ $parentPreviousUrl ?? '' }}"
                                          next-url="{{ $parentNextUrl ?? '' }}"></a17-page-nav>

                            @if ($formBuilder->hasSideForm())
                                {!! $formBuilder->renderSideForm() !!}
                            @else
                                @hasSection('sideFieldset')
                                    <x-twill::formFieldset
                                        id="options"
                                        title="{{ $sideFieldsetLabel ?? 'Options' }}"
                                    >
                                        @yield('sideFieldset')
                                    </x-twill::formFieldset>
                                @endif
                                @yield('sideFieldsets')
                            @endif
                        </div>
                    </aside>
                    <section class="col col--primary" data-sticky-top="publisher">
                        @if ($formBuilder->hasForm())
                            {!! $formBuilder->renderBaseForm() !!}
                        @else
                            @unless($disableContentFieldset)
                                <x-twill::formFieldset
                                    id="content"
                                    title="{{ $contentFieldsetLabel ?? twillTrans('twill::lang.form.content') }}"
                                >
                                    @yield('contentFields')
                                </x-twill::formFieldset>
                            @endunless

                            @yield('fieldsets')
                        @endif

                        @if(\A17\Twill\Facades\TwillPermissions::levelIs(\A17\Twill\Enums\PermissionLevel::LEVEL_ROLE_GROUP_ITEM))
                            @if($showPermissionFieldset ?? null)
                                @can('manage-item', isset($item) ? $item : null)
                                    <x-twill::formFieldset id="permissions"
                                                           title="User Permissions"
                                                           :open="false">
                                        <x-twill::select-permissions
                                            :items-in-selects-tables="$users"
                                            label-key="name"
                                            name-pattern="user_%id%_permission"
                                            :list-user="true"/>
                                    </x-twill::formFieldset>
                                @endcan
                            @endif
                        @endif
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
    <a17-editor v-if="editor" ref="editor"
                bg-color="{{ config('twill.block_editor.background_color') ?? '#FFFFFF' }}"></a17-editor>
    <a17-previewer ref="preview" :breakpoints-config="{{ json_encode(config('twill.preview.breakpoints')) }}"></a17-previewer>
    <a17-dialog ref="warningContentEditor" modal-title="{{ twillTrans('twill::lang.form.dialogs.delete.title') }}"
                confirm-label="{{ twillTrans('twill::lang.form.dialogs.delete.confirm') }}">
        <p class="modal--tiny-title">
            <strong>{{ twillTrans('twill::lang.form.dialogs.delete.delete-content') }}</strong>
        </p>
        <p>{!! twillTrans('twill::lang.form.dialogs.delete.confirmation') !!}</p>
    </a17-dialog>
@stop

@section('initialStore')
    window['{{ config('twill.js_namespace') }}'].STORE.form = {
    baseUrl: '{{ $baseUrl ?? '' }}',
    saveUrl: '{{ $saveUrl }}',
    previewUrl: '{{ $previewUrl ?? '' }}',
    restoreUrl: '{{ $restoreUrl ?? '' }}',
    availableBlocks: {},
    blocks: {},
    allAvailableBlocks: {!! json_encode($allBlocks ?? []) !!},
    blockPreviewUrl: '{{ $blockPreviewUrl ?? '' }}',
    repeaters: {!! json_encode(($form_fields['repeaters'] ?? []) + ($form_fields['blocksRepeaters'] ?? [])) !!},
    fields: [],
    editor: {{ $editor ? 'true' : 'false' }},
    isCustom: {{ $customForm ? 'true' : 'false' }},
    reloadOnSuccess: {{ ($reloadOnSuccess ?? false) ? 'true' : 'false' }},
    editorNames: []
    }

    window['{{ config('twill.js_namespace') }}'].STORE.publication = {
    withPublicationToggle: {{ json_encode(($publish ?? true) && $item?->isFillable('published')) }},
    published: {{ $item?->published ? 'true' : 'false' }},
    createWithoutModal: {{ isset($createWithoutModal) && $createWithoutModal ? 'true' : 'false' }},
    withPublicationTimeframe: {{ json_encode(($schedule ?? true) && $item?->isFillable('publish_start_date')) }},
    publishedLabel: '{{ $publishedLabel ?? twillTrans('twill::lang.main.published') }}',
    draftLabel: '{{ $draftLabel ?? twillTrans('twill::lang.main.draft') }}',
    expiredLabel: '{{twillTrans('twill::lang.publisher.expired')}}',
    scheduledLabel: '{{twillTrans('twill::lang.publisher.scheduled')}}',
    submitDisableMessage: '{{ $submitDisableMessage ?? '' }}',
    startDate: '{{ $item?->publish_start_date ?? '' }}',
    endDate: '{{ $item?->publish_end_date ?? '' }}',
    visibility: '{{ $item?->isFillable('public') ? ($item?->public ? 'public' : 'private') : false }}',
    reviewProcess: {!! isset($reviewProcess) ? json_encode($reviewProcess) : '[]' !!},
    submitOptions: {!! isset($submitOptions) ? json_encode($submitOptions) : 'null' !!}
    }

    window['{{ config('twill.js_namespace') }}'].STORE.revisions = {!! json_encode($revisions ?? []) !!}

    window['{{ config('twill.js_namespace') }}'].STORE.parentId = {{ $item?->parent_id ?? 0 }}
    window['{{ config('twill.js_namespace') }}'].STORE.parents = {!! json_encode($parents ?? []) !!}

    @if (isset($item) && classHasTrait($item, \A17\Twill\Models\Behaviors\HasMedias::class))
        window['{{ config('twill.js_namespace') }}'].STORE.medias.crops = {!! json_encode(($item->getMediasParams()) + \A17\Twill\Facades\TwillBlocks::getAllCropConfigs() + (config('twill.settings.crops') ?? [])) !!}
    @else
        window['{{ config('twill.js_namespace') }}'].STORE.medias.crops = {!! json_encode(\A17\Twill\Facades\TwillBlocks::getAllCropConfigs() + (config('twill.settings.crops') ?? [])) !!}
    @endif
    window['{{ config('twill.js_namespace') }}'].STORE.medias.selected = {}

    window['{{ config('twill.js_namespace') }}'].STORE.browser = {}
    window['{{ config('twill.js_namespace') }}'].STORE.browser.selected = {}

    window['{{ config('twill.js_namespace') }}'].APIKEYS = {
    'googleMapApi': '{{ config('twill.google_maps_api_key') }}'
    }

    {{-- Permissions --}}
    window['{{ config('twill.js_namespace') }}'].STORE.groups = {!! isset($groups) ? json_encode($groups) : '[]' !!};
    window['{{ config('twill.js_namespace') }}'].STORE.groupUserMapping = {!! isset($groupUserMapping) ? json_encode($groupUserMapping) : '[]' !!};
@stop

@prepend('extra_js')
    @includeWhen(config('twill.block_editor.inline_blocks_templates', true), 'twill::partials.form.utils._blocks_templates')
    <script src="{{ twillAsset('main-form.js') }}" crossorigin></script>
    <script>
        const groupUserMapping = {!! isset($groupUserMapping) ? json_encode($groupUserMapping) : '[]' !!};
        window['{{ config('twill.js_namespace') }}'].vm.$store.subscribe((mutation, state) => {
            if (mutation.type === 'updateFormField' && mutation.payload.name.endsWith('group_authorized')) {
                const groupId = mutation.payload.name.replace('_group_authorized', '')
                const checked = mutation.payload.value
                if (!isNaN(groupId)) {
                    const users = groupUserMapping[groupId]
                    users.forEach(function (userId) {
                        // If the user's permission is <= view, it will be updated
                        const currentPermission = state['form']['fields'].find(function (e) {
                            return e.name == `user_${userId}_permission`
                        }).value
                        if (currentPermission === '' || currentPermission === 'view-item') {
                            const field = {
                                name: `user_${userId}_permission`,
                                value: checked ? 'view-item' : ''
                            }
                            window['{{ config('twill.js_namespace') }}'].vm.$store.commit('updateFormField', field)
                        }
                    })
                }
            }
        })
    </script>
@endprepend
