@extends('twill::layouts.main')

@section('appTypeClass', 'body--listing')

@php
    $translate = $translate ?? false;
    $translateTitle = $translateTitle ?? $translate ?? false;
    $reorder = $reorder ?? false;
    $nested = $nested ?? false;
    $bulkEdit = $bulkEdit ?? true;
    $create = $create ?? false;

    $requestFilter = json_decode(request()->get('filter'), true) ?? [];
@endphp

@push('extra_css')
    @if(app()->isProduction())
        <link href="{{ twillAsset('main-listing.css') }}" rel="preload" as="style" crossorigin/>
    @endif
    @unless(config('twill.dev_mode', false))
        <link href="{{ twillAsset('main-listing.css') }}" rel="stylesheet" crossorigin/>
    @endunless
@endpush

@push('extra_js_head')
    @if(app()->isProduction())
        <link href="{{ twillAsset('main-listing.js') }}" rel="preload" as="script" crossorigin/>
    @endif
@endpush

@section('content')
    <div class="listing">
        <div class="listing__nav">
            <div class="container" ref="form">
                <a17-filter v-on:submit="filterListing" v-bind:closed="hasBulkIds"
                            initial-search-value="{{ $filters['search'] ?? '' }}" :clear-option="true"
                            v-on:clear="clearFiltersAndReloadDatas">
                    <a17-table-filters slot="navigation"></a17-table-filters>

                    @forelse($hiddenFilters as $filter)
                        @if ($loop->first)
                            <div slot="hidden-filters">
                        @endif

                        @if (isset(${$filter.'List'}))
                            @php
                                $list = ${$filter.'List'};
                                $options = method_exists($list, 'map') ?
                                    $list->map(function($label, $value) {
                                        return [
                                            'value' => $value,
                                            'label' => $label,
                                        ];
                                    })->values()->toArray() : $list;
                                $selectedIndex = isset($requestFilter[$filter]) ? array_search($requestFilter[$filter], array_column($options, 'value')) : false;
                            @endphp
                            <a17-vselect
                                name="{{ $filter }}"
                                :options="{{ json_encode($options) }}"
                                @if ($selectedIndex !== false)
                                    :selected="{{ json_encode($options[$selectedIndex]) }}"
                                @endif
                                placeholder="All {{ strtolower(\Illuminate\Support\Str::plural($filter)) }}"
                                ref="filterDropdown[{{ $loop->index }}]"
                            ></a17-vselect>
                        @endif

                        @if ($loop->last)
                            </div>
                        @endif
                    @empty
                        @hasSection('hiddenFilters')
                            <div slot="hidden-filters">
                                @yield('hiddenFilters')
                            </div>
                        @endif
                    @endforelse

                    @if($create)
                        <div slot="additional-actions">
                            <a17-button variant="validate" size="small" v-on:click="create">{{ twillTrans('twill::lang.listing.add-new-button') }}</a17-button>
                            @foreach($filterLinks as $link)
                                <a17-button el="a" href="{{ $link['url'] ?? '#' }}" download="{{ $link['download'] ?? '' }}" rel="{{ $link['rel'] ?? '' }}" target="{{ $link['target'] ?? '' }}" variant="small secondary">{{ $link['label'] }}</a17-button>
                            @endforeach
                        </div>
                    @elseif(isset($filterLinks) && count($filterLinks))
                        <div slot="additional-actions">
                            @foreach($filterLinks as $link)
                                <a17-button el="a" href="{{ $link['url'] ?? '#' }}" download="{{ $link['download'] ?? '' }}" rel="{{ $link['rel'] ?? '' }}" target="{{ $link['target'] ?? '' }}" variant="small secondary">{{ $link['label'] }}</a17-button>
                            @endforeach
                        </div>
                    @endif
                </a17-filter>
            </div>
            @if($bulkEdit)
                <a17-bulk></a17-bulk>
            @endif
        </div>

        @if($nested)
            <a17-nested-datatable
                :draggable="{{ $reorder ? 'true' : 'false' }}"
                :max-depth="{{ $nestedDepth ?? '1' }}"
                :bulkeditable="{{ $bulkEdit ? 'true' : 'false' }}"
                empty-message="{{ twillTrans('twill::lang.listing.listing-empty-message') }}">
            </a17-nested-datatable>
        @else
            <a17-datatable
                :draggable="{{ $reorder ? 'true' : 'false' }}"
                :bulkeditable="{{ $bulkEdit ? 'true' : 'false' }}"
                empty-message="{{ twillTrans('twill::lang.listing.listing-empty-message') }}">
            </a17-datatable>
        @endif

        @if($create)
            <a17-modal-create
                ref="editionModal"
                form-create="{{ $storeUrl }}"
                v-on:reload="reloadDatas"
                @if ($customPublishedLabel ?? false) published-label="{{ $customPublishedLabel }}" @endif
                @if ($customDraftLabel ?? false) draft-label="{{ $customDraftLabel }}" @endif
            >
                <a17-langmanager></a17-langmanager>
                @partialView(($moduleName ?? null), 'create', ['renderForModal' => true])
            </a17-modal-create>
        @endif

        <a17-dialog ref="warningDeleteRow" modal-title="Delete item" confirm-label="Delete">
            <p class="modal--tiny-title"><strong>Move to trash</strong></p>
            <p>The item won't be deleted but moved to trash.</p>
        </a17-dialog>

        <a17-dialog ref="warningDestroyRow" modal-title="Destroy item" confirm-label="Destroy">
            <p class="modal--tiny-title"><strong>Destroy permanently</strong></p>
            <p>The item won't be able to be restored anymore.</p>
        </a17-dialog>
    </div>
@stop

@section('initialStore')

    window['{{ config('twill.js_namespace') }}'].CMS_URLS = {
        index: @if(isset($indexUrl)) '{{ $indexUrl }}' @else window.location.href.split('?')[0] @endif,
        publish: '{{ $publishUrl }}',
        bulkPublish: '{{ $bulkPublishUrl }}',
        restore: '{{ $restoreUrl }}',
        bulkRestore: '{{ $bulkRestoreUrl }}',
        forceDelete: '{{ $forceDeleteUrl }}',
        bulkForceDelete: '{{ $bulkForceDeleteUrl }}',
        reorder: '{{ $reorderUrl }}',
        feature: '{{ $featureUrl }}',
        bulkFeature: '{{ $bulkFeatureUrl }}',
        bulkDelete: '{{ $bulkDeleteUrl }}'
    }

    window['{{ config('twill.js_namespace') }}'].STORE.form = {
        fields: []
    }

    window['{{ config('twill.js_namespace') }}'].STORE.datatable = {
        data: {!! json_encode($tableData) !!},
        columns: {!! json_encode($tableColumns) !!},
        navigation: {!! json_encode($tableMainFilters) !!},
        filter: { status: '{{ $filters['status'] ?? $defaultFilterSlug ?? 'all' }}' },
        page: {{ request('page') ?? 1 }},
        maxPage: {{ $maxPage ?? 1 }},
        defaultMaxPage: {{ $defaultMaxPage ?? 1 }},
        offset: {{ request('offset') ?? $offset ?? 60 }},
        defaultOffset: {{ $defaultOffset ?? 60 }},
        sortKey: '{{ $reorder ? (request('sortKey') ?? '') : (request('sortKey') ?? '') }}',
        sortDir: '{{ request('sortDir') ?? 'asc' }}',
        baseUrl: '{{ rtrim(config('app.url'), '/') . '/' }}',
        localStorageKey: '{{ isset($currentUser) ? $currentUser->id : 0 }}__{{ $moduleName ?? Route::currentRouteName() }}'
    }

    @if ($create && ($openCreate ?? false))
        window['{{ config('twill.js_namespace') }}'].openCreate = {!! json_encode($openCreate) !!}
    @endif
@stop

@push('extra_js')
    <script src="{{ twillAsset('main-listing.js') }}" crossorigin></script>
@endpush
