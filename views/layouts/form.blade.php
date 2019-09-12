@extends('twill::layouts.main')

@section('appTypeClass', 'body--form')

@php
    $editor = $editor ?? false;
    $translate = $translate ?? false;
    $translateTitle = $translateTitle ?? $translate ?? false;
    $titleFormKey = $titleFormKey ?? 'title';
    $customForm = $customForm ?? false;
    $controlLanguagesPublication = $controlLanguagesPublication ?? true;
    $users = app()->make('A17\Twill\Repositories\UserRepository')->published()->where('is_superadmin', false)->get();
    $groups = app()->make('A17\Twill\Repositories\GroupRepository')->get();
@endphp

@section('content')
    <div class="form" v-sticky data-sticky-id="navbar" data-sticky-offset="0" data-sticky-topoffset="12" >
        <div class="navbar navbar--sticky" data-sticky-top="navbar">
            @php
                $additionalFieldsets = $additionalFieldsets ?? [];
                array_unshift($additionalFieldsets, [
                    'fieldset' => 'content',
                    'label' => $contentFieldsetLabel ?? 'Content'
                ]);
            @endphp
            <a17-sticky-nav data-sticky-target="navbar" :items="{{ json_encode($additionalFieldsets) }}">
                <a17-title-editor
                    name="{{ $titleFormKey }}"
                    :editable-title="{{ json_encode($editableTitle ?? true) }}"
                    custom-title="{{ $customTitle ?? '' }}"
                    custom-permalink="{{ $customPermalink ?? '' }}"
                    slot="title"
                    @if(isset($editModalTitle)) modal-title="{{ $editModalTitle }}" @endif
                >
                    <template slot="modal-form">
                        @partialView(($moduleName ?? null), 'create')
                    </template>
                </a17-title-editor>
                <div slot="actions">
                    <a17-langswitcher :all-published="{{ json_encode(!$controlLanguagesPublication) }}"></a17-langswitcher>
                    <a17-button v-if="editor" type="button" variant="editor" size="small" @click="openEditor(-1)">
                        <span v-svg symbol="editor"></span>Editor
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
                            <a17-publisher :show-languages="{{ json_encode($controlLanguagesPublication) }}"></a17-publisher>
                            <a17-page-nav
                                placeholder="Go to page"
                                previous-url="{{ $parentPreviousUrl ?? '' }}"
                                next-url="{{ $parentNextUrl ?? '' }}"
                            ></a17-page-nav>
                        </div>
                    </aside>
                    <section class="col col--primary">
                        @unless($disableContentFieldset ?? false)
                            <a17-fieldset title="{{ $contentFieldsetLabel ?? 'Content' }}" id="content" data-sticky-top="publisher">
                                @yield('contentFields')
                            </a17-fieldset>
                        @endunless

                        @if($showPermissionFieldset ?? null)
                            @can('manage-item', $item)
                                <a17-fieldset title="User Permissions" id="permissions">
                                    @foreach($users as $user)
                                        @formField('select', [
                                            'name' => 'user_' . $user->id . '_permission',
                                            'label' => $user->name,
                                            'unpack' => true,
                                            'options' => [
                                                [
                                                    'value' => '',
                                                    'label' => 'None'
                                                ],
                                                [
                                                    'value' => 'view-item',
                                                    'label' => 'View'
                                                ],
                                                [
                                                    'value' => 'edit-item',
                                                    'label' => 'Edit'
                                                ],
                                                [
                                                    'value' => 'manage-item',
                                                    'label' => 'Manage'
                                                ],
                                            ]
                                        ])
                                    @endforeach
                                </a17-fieldset>
                                <a17-fieldset title="Group Permissions" id="permissions">
                                    @foreach($groups as $group)
                                        @formField('checkbox', [
                                            'name' => $group->id . '_group_authorized',
                                            'label' => $group->name
                                        ])
                                    @endforeach
                                </a17-fieldset>
                            @endcan
                        @endif

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
    <a17-dialog ref="warningContentEditor" modal-title="Delete content" confirm-label="Delete">
        <p class="modal--tiny-title"><strong>Delete content</strong></p>
        <p>Are you sure ?<br />This change can't be undone.</p>
    </a17-dialog>
@stop

@section('initialStore')

    window.STORE.form = {
        baseUrl: '{{ $baseUrl ?? '' }}',
        saveUrl: '{{ $saveUrl }}',
        previewUrl: '{{ $previewUrl ?? '' }}',
        restoreUrl: '{{ $restoreUrl ?? '' }}',
        blockPreviewUrl: '{{ $blockPreviewUrl ?? '' }}',
        availableRepeaters: {!! json_encode(config('twill.block_editor.repeaters')) !!},
        repeaters: {!! json_encode(($form_fields['repeaters'] ?? []) + ($form_fields['blocksRepeaters'] ?? [])) !!},
        fields: [],
        editor: {{ $editor ? 'true' : 'false' }},
        isCustom: {{ $customForm ? 'true' : 'false' }},
        reloadOnSuccess: {{ ($reloadOnSuccess ?? false) ? 'true' : 'false' }}
    }

    window.STORE.publication = {
        withPublicationToggle: {{ json_encode(($publish ?? true) && isset($item) && $item->isFillable('published')) }},
        published: {{ json_encode(isset($item) ? $item->published : false) }},
        withPublicationTimeframe: {{ json_encode(($schedule ?? true) && isset($item) && $item->isFillable('publish_start_date')) }},
        publishedLabel: '{{ $customPublishedLabel ?? 'Live' }}',
        draftLabel: '{{ $customDraftLabel ?? 'Draft' }}',
        startDate: '{{ $item->publish_start_date ?? '' }}',
        endDate: '{{ $item->publish_end_date ?? '' }}',
        visibility: '{{ isset($item) && $item->isFillable('public') ? ($item->public ? 'public' : 'private') : false }}',
        reviewProcess: {!! isset($reviewProcess) ? json_encode($reviewProcess) : '[]' !!},
        submitOptions: @if(isset($item) && $item->cmsRestoring) {
            draft: [
                {
                    name: 'restore',
                    text: 'Restore as a draft'
                },
                {
                    name: 'restore-close',
                    text: 'Restore as a draft and close'
                },
                {
                    name: 'restore-new',
                    text: 'Restore as a draft and create new'
                },
                {
                    name: 'cancel',
                    text: 'Cancel'
                }
            ],
            live: [
                {
                    name: 'restore',
                    text: 'Restore as published'
                },
                {
                    name: 'restore-close',
                    text: 'Restore as published and close'
                },
                {
                    name: 'restore-new',
                    text: 'Restore as published and create new'
                },
                {
                    name: 'cancel',
                    text: 'Cancel'
                }
            ],
            update: [
                {
                    name: 'restore',
                    text: 'Restore as published'
                },
                {
                    name: 'restore-close',
                    text: 'Restore as published and close'
                },
                {
                    name: 'restore-new',
                    text: 'Restore as published and create new'
                },
                {
                    name: 'cancel',
                    text: 'Cancel'
                }
            ]
        } @else null @endif
    }

    window.STORE.revisions = {!! json_encode($revisions ?? []) !!}

    window.STORE.parentId = {{ $item->parent_id ?? 0 }}
    window.STORE.parents = {!! json_encode($parents ?? [])  !!}

    window.STORE.medias.crops = {!! json_encode(($item->mediasParams ?? []) + config('twill.block_editor.crops') + (config('twill.settings.crops') ?? [])) !!}
    window.STORE.medias.selected = {}

    window.STORE.browser = {}
    window.STORE.browser.selected = {}

    window.APIKEYS = {
        'googleMapApi': '{{ config('twill.google_maps_api_key') }}'
    }
@stop

@prepend('extra_js')
    <script src="{{ mix('/assets/admin/js/manifest.js') }}"></script>
    <script src="{{ mix('/assets/admin/js/vendor.js') }}"></script>
    <script src="{{ mix('/assets/admin/js/main-form.js') }}"></script>

    <script>
        const groupUserMapping = {!! isset($groupUserMapping) ? json_encode($groupUserMapping) : '[]' !!};
        window.vm.$store.subscribe((mutation, state) => {
            if (mutation.type === 'updateFormField' && mutation.payload.name.endsWith('group_authorized')) {
                const groupId = mutation.payload.name.replace('_group_authorized', '');
                const checked = mutation.payload.value;
                if (!isNaN(groupId)) {
                    const users = groupUserMapping[groupId];
                    users.forEach(function (userId) {
                        // If the user's permission is <= view, it will be updated
                        const currentPermission = state['form']['fields'].find(function(e) {
                            return e.name == `user_${userId}_permission`
                        }).value;
                        if (currentPermission === '' || currentPermission === 'view-item') {
                            const field = {
                                name: `user_${userId}_permission`,
                                value: checked ? 'view-item' : ''
                            };
                            window.vm.$store.commit('updateFormField', field)
                        }
                    })
                }
            }
        })
    </script>
@endprepend

