@extends('twill::layouts.main')

@section('appTypeClass', 'body--listing')

@php
    $translate = $translate ?? false;
    $translateTitle = $translateTitle ?? $translate ?? false;
    $reorder = $reorder ?? false;
    $nested = $nested ?? false;
    $bulkEdit = $bulkEdit ?? true;
    $create = $create ?? false;
    $skipCreateModal = $skipCreateModal ?? false;
    $controlLanguagesPublication = $controlLanguagesPublication ?? true;

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

                    @php /** @var \A17\Twill\Services\Listings\Filters\BasicFilter $filter */ @endphp
                    @if (count($hiddenFilters) > 0)
                        <div slot="hidden-filters">
                            @foreach($hiddenFilters as $filter)
                                @php
                                    $options = $filter->getOptions($repository)->map(function($label, $value) use($filter) {
                                            if ($value === \A17\Twill\Services\Listings\Filters\BasicFilter::OPTION_ALL) {
                                                // @PRtodo: TwillTrans
                                                $label = "All " . $filter->getLabel() ?? '';
                                            }
                                            return [
                                                'value' => $value,
                                                'label' => $label,
                                            ];
                                        })->values()->toArray();

                                    $currentValue = $requestFilter[$filter->getQueryString()] ?? $filter->getDefaultValue();

                                    $selectedIndex = array_search($currentValue, array_column($options, 'value'));
                                    // @todo: When the user presses "Reset" in the filters, we should reset them to our
                                    // default ($filter->getDefaultValue)
                                @endphp
                                <a17-vselect
                                    name="{{ $filter->getQueryString() }}"
                                    :options="{{ json_encode($options) }}"
                                    @if ($selectedIndex !== false)
                                        :selected="{{ json_encode($options[$selectedIndex]) }}"
                                    @endif
                                    placeholder="All {{ strtolower(\Illuminate\Support\Str::plural($filter->getQueryString())) }}"
                                    ref="filterDropdown[{{ $loop->index }}]"
                                ></a17-vselect>
                            @endforeach
                        </div>
                    @else
                        @hasSection('hiddenFilters')
                            <div slot="hidden-filters">
                                @yield('hiddenFilters')
                            </div>
                        @endif
                    @endif

                    @if($create)
                        <div slot="additional-actions">
                            <a17-button
                                variant="validate"
                                size="small"
                                @if($skipCreateModal) href={{$createUrl ?? ''}} el="a" @endif
                                @if(!$skipCreateModal) v-on:click="create" @endif
                            >
                                {{ twillTrans('twill::lang.listing.add-new-button') }}
                            </a17-button>
                            @foreach($filterLinks as $link)
                                <a17-button el="a" href="{{ $link['url'] ?? '#' }}"
                                            download="{{ $link['download'] ?? '' }}" rel="{{ $link['rel'] ?? '' }}"
                                            target="{{ $link['target'] ?? '' }}"
                                            variant="small secondary">{{ $link['label'] }}</a17-button>
                            @endforeach
                        </div>
                    @elseif(isset($filterLinks) && count($filterLinks))
                        <div slot="additional-actions">
                            @foreach($filterLinks as $link)
                                <a17-button el="a" href="{{ $link['url'] ?? '#' }}"
                                            download="{{ $link['download'] ?? '' }}" rel="{{ $link['rel'] ?? '' }}"
                                            target="{{ $link['target'] ?? '' }}"
                                            variant="small secondary">{{ $link['label'] }}</a17-button>
                            @endforeach
                        </div>
                    @endif

                    @if(isset($additionalTableActions) && count($additionalTableActions))
                        <div slot="additional-actions">
                            @foreach($additionalTableActions as $additionalTableAction)
                                <a17-button
                                    variant="{{ $additionalTableAction['variant'] ?? 'primary' }}"
                                    size="{{ $additionalTableAction['size'] ?? 'small' }}"
                                    el="{{ $additionalTableAction['type'] ?? 'button' }}"
                                    href="{{ $additionalTableAction['link'] ?? '#' }}"
                                    target="{{ $additionalTableAction['target'] ?? '_self' }}"
                                >
                                    {{ $additionalTableAction['name'] }}
                                </a17-button>
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
                @if ($publishedLabel ?? false) published-label="{{ $publishedLabel }}" @endif
                @if ($draftLabel ?? false) draft-label="{{ $draftLabel }}" @endif
            >
                <a17-langmanager
                    :control-publication="{{ json_encode($controlLanguagesPublication) }}"
                ></a17-langmanager>
                @partialView(($moduleName ?? null), 'create', ['renderForModal' => true])
            </a17-modal-create>
        @endif

        <a17-dialog ref="warningDeleteRow" modal-title="{{ twillTrans('twill::lang.listing.dialogs.delete.title') }}"
                    confirm-label="{{ twillTrans('twill::lang.listing.dialogs.delete.confirm') }}">
            <p class="modal--tiny-title">
                <strong>{{ twillTrans('twill::lang.listing.dialogs.delete.move-to-trash') }}</strong></p>
            <p>{{ twillTrans('twill::lang.listing.dialogs.delete.disclaimer') }}</p>
        </a17-dialog>

        <a17-dialog ref="warningDestroyRow" modal-title="{{ twillTrans('twill::lang.listing.dialogs.destroy.title') }}"
                    confirm-label="{{ twillTrans('twill::lang.listing.dialogs.destroy.confirm') }}">
            <p class="modal--tiny-title">
                <strong>{{ twillTrans('twill::lang.listing.dialogs.destroy.destroy-permanently') }}</strong></p>
            <p>{{ twillTrans('twill::lang.listing.dialogs.destroy.disclaimer') }}</p>
        </a17-dialog>
    </div>
@stop

@section('initialStore')

    window['{{ config('twill.js_namespace') }}'].CMS_URLS = {
    index: @if(isset($indexUrl))
        '{{ $indexUrl }}'
    @else
        window.location.href.split('?')[0]
    @endif,
    publish: '{{ $publishUrl }}',
    bulkPublish: '{{ $bulkPublishUrl }}',
    restore: '{{ $restoreUrl }}',
    bulkRestore: '{{ $bulkRestoreUrl }}',
    forceDelete: '{{ $forceDeleteUrl }}',
    bulkForceDelete: '{{ $bulkForceDeleteUrl }}',
    reorder: '{{ $reorderUrl }}',
    create: '{{ $createUrl ?? '' }}',
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
    page: '{{ request('page') ?? 1 }}',
    maxPage: '{{ $maxPage ?? 1 }}',
    defaultMaxPage: '{{ $defaultMaxPage ?? 1 }}',
    offset: '{{ request('offset') ?? $offset ?? 60 }}',
    defaultOffset: '{{ $defaultOffset ?? 60 }}',
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
