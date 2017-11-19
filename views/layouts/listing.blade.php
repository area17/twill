@extends('cms-toolkit::layouts.main')

@php
    $emptyDataTable = $emptyMessage ?? "There is not yet items here."
@endphp

@section('content')
    <div class="app app--listing" id="app" v-cloak>
        <div class="listing">
            <div class="listing__nav">
                <div class="container" ref="form">
                    <a17-filter v-on:submit="filterListing" v-bind:closed="hasBulkIds">
                        <ul class="secondarynav secondarynav--desktop" slot="navigation">
                            <li v-for="(navItem, index) in navFilters" class="secondarynav__item" :class="{ 's--on' : navActive === navItem.slug }"><a href="#" v-on:click.prevent="filterStatus(navItem.slug)"><span class="secondarynav__link">@{{ navItem.name }}</span><span class="secondarynav__number">(@{{ navItem.number }})</span></a></li>
                        </ul>

                        <div class="secondarynav secondarynav--mobile secondarynav--dropdown" slot="navigation">
                            <a17-dropdown ref="secondaryNavDropdown" position="bottom-left" width="full" :offset="0">
                                <a17-button variant="dropdown" size="small" @click="$refs.secondaryNavDropdown.toggle()">
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

                        {{--<a17-vselect class="secondarynav secondarynav--mobile secondarynav--select" slot="navigation" name="filters-navigation" :options="selectItems" :selected="selectedNav">--}}
                        {{--</a17-vselect>--}}

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
            <a17-datatable :draggable="{{ $sort ? 'true' : 'false' }}" empty-message="{{ __($emptyDataTable) }}"></a17-datatable>
            <a17-modal class="modal--form" ref="addNewModal" title="Add New">
                <form action="#">
                    <a17-modal-title-editor v-bind:base-url="baseUrl" @unless($permalink ?? true) :with-permalink="false" @endunless></a17-modal-title-editor>
                    <a17-modal-validation v-bind:mode="'create'"></a17-modal-validation>
                </form>
            </a17-modal>
            <a17-modal ref="mediaLibrary" title="Media Library" mode="wide">
                <a17-medialibrary endpoint="https://www.mocky.io/v2/59edf8273300000e00b5c7d6" />
            </a17-modal>
        </div>
        <a17-notif variant="success"></a17-notif>
        <a17-notif variant="error"></a17-notif>
    </div>
@stop

@section('initialStore')
    window.CMS_URLS = {
        index: @if(isset($indexUrl)) '{{ $indexUrl }}' @else window.location.href.split('?')[0] @endif,
        publish: '{{ $publishUrl }}',
        restore: '{{ $restoreUrl }}',
        reorder: '{{ $reorderUrl }}',
        feature: '{{ $featureUrl }}'
    }

    window.STORE.datatable = {
      baseUrl: 'http://pentagram.com/work/',
      page: {{ request('page') ?? 1 }},
      maxPage: {{ $maxPage ?? 1 }},
      defaultMaxPage: {{ $defaultMaxPage ?? 1 }},
      offset: {{ request('offset') ?? $offset ?? 60 }},
      defaultOffset: {{ $defaultOffset ?? 60 }},
      sortKey: '{{ $sort ? '' : 'name' }}',
      sortDir: '{{ request('sortDir') ?? 'asc' }}'
    }

    window.STORE.datatable.data = {!! json_encode($tableData) !!}
    window.STORE.datatable.columns = {!! json_encode($tableColumns) !!}

    window.STORE.datatable.navigation = {!! json_encode($tableMainFilters) !!}

    window.STORE.datatable.filter = { status: '{{ $filters['status'] ?? 'all' }}' }
@stop

@push('extra_js')
    <script src="{{ mix('/assets/admin/js/manifest.js') }}"></script>
    <script src="{{ mix('/assets/admin/js/vendor.js') }}"></script>
    <script src="{{ mix('/assets/admin/js/main-listing.js') }}"></script>
@endpush
