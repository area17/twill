@extends('cms-toolkit::layouts.main')

@php
    $emptyDataTable = $emptyMessage ?? "There is no items here yet.";
    $routeName = $moduleName ?? Route::getCurrentRoute();
@endphp

@section('appTypeClass', 'app--listing')

@section('content')
    <div class="listing" xmlns:v-on="http://www.w3.org/1999/xhtml">
        <div class="listing__nav">
            <div class="container" ref="form">
                <a17-filter v-on:submit="filterListing" v-bind:closed="hasBulkIds" initial-search-value="{{ $filters['search'] ?? '' }}">
                    <ul class="secondarynav secondarynav--desktop" slot="navigation">
                        <li v-for="(navItem, index) in navFilters" class="secondarynav__item" :class="{ 's--on' : navActive === navItem.slug }"><a href="#" v-on:click.prevent="filterStatus(navItem.slug)"><span class="secondarynav__link">@{{ navItem.name }}</span><span class="secondarynav__number">(@{{ navItem.number }})</span></a></li>
                    </ul>

                    <div class="secondarynav secondarynav--mobile secondarynav--dropdown" slot="navigation">
                        <a17-dropdown ref="secondaryNavDropdown" position="bottom-left" width="full" :offset="0">
                            <a17-button class="secondarynav__button" variant="dropdown-transparent" size="small" @click="$refs.secondaryNavDropdown.toggle()">
                                <span class="secondarynav__link">@{{ selectedNav.name }}</span><span class="secondarynav__number">(@{{ selectedNav.number }})</span>
                            </a17-button>
                            <div slot="dropdown__content">
                                <ul>
                                    <li v-for="(navItem, index) in navFilters" class="secondarynav__item">
                                        <a href="#" v-on:click.prevent="filterStatus(navItem.slug)"><span class="secondarynav__link">@{{ navItem.name }}</span><span class="secondarynav__number">(@{{ navItem.number }})</span></a>
                                    </li>
                                </ul>
                            </div>
                        </a17-dropdown>
                    </div>

                    @hasSection('hiddenFilters')
                        <div slot="hidden-filters">
                            @yield('hiddenFilters')
                        </div>
                    @endif

                    <div slot="additional-actions"><a17-button variant="validate" size="small" v-on:click="$refs.addNewModal.open()">Add New</a17-button></div>
                </a17-filter>
            </div>
            <a17-bulk></a17-bulk>
        </div>
        <a17-datatable :draggable="{{ $reorder ? 'true' : 'false' }}" empty-message="{{ __($emptyDataTable) }}"></a17-datatable>
        <a17-modal class="modal--form" ref="addNewModal" title="Add New">
            <form action="{{ $storeUrl }}" method="post">
                <a17-modal-title-editor v-bind:base-url="baseUrl" @unless($permalink ?? true) :with-permalink="false" @endunless></a17-modal-title-editor>
                <a17-modal-validation v-bind:mode="'create'"></a17-modal-validation>
            </form>
        </a17-modal>
    </div>
@stop

@section('initialStore')
    window.CMS_URLS = {
        index: @if(isset($indexUrl)) '{{ $indexUrl }}' @else window.location.href.split('?')[0] @endif,
        publish: '{{ $publishUrl }}',
        bulkPublish: '{{ $bulkPublishUrl }}',
        restore: '{{ $restoreUrl }}',
        bulkRestore: '{{ $bulkRestoreUrl }}',
        reorder: '{{ $reorderUrl }}',
        feature: '{{ $featureUrl }}',
        bulkFeature: '{{ $bulkFeatureUrl }}',
        bulkDelete: '{{ $bulkDeleteUrl }}'
    }

    window.STORE.datatable = {
      data: {!! json_encode($tableData) !!},
      columns: {!! json_encode($tableColumns) !!},
      navigation: {!! json_encode($tableMainFilters) !!},
      filter: { status: '{{ $filters['status'] ?? 'all' }}' },
      page: {{ request('page') ?? 1 }},
      maxPage: {{ $maxPage ?? 1 }},
      defaultMaxPage: {{ $defaultMaxPage ?? 1 }},
      offset: {{ request('offset') ?? $offset ?? 60 }},
      defaultOffset: {{ $defaultOffset ?? 60 }},
      sortKey: '{{ $reorder ? (request('sortKey') ?? '') : (request('sortKey') ?? 'name') }}',
      sortDir: '{{ request('sortDir') ?? 'asc' }}',
      baseUrl: 'https://cms-sandbox.a17.io/',
      localSlug: '{{ $currentUser->id }}__{{ $routeName }}'
    }
@stop

@push('extra_js')
    <script src="{{ mix('/assets/admin/js/manifest.js') }}"></script>
    <script src="{{ mix('/assets/admin/js/vendor.js') }}"></script>
    <script src="{{ mix('/assets/admin/js/main-listing.js') }}"></script>
@endpush
