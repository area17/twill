@extends('cms-toolkit::layouts.main')

@php
    $sort = $sort ?? true;
    $emptyDataTable = $emptyMessage ?? "There is not yet items here."
@endphp

@section('content')
    <div class="app app--listing" id="app" v-cloak>
        <div class="listing">
            <div class="listing__nav">
                <div class="container" ref="form">
                    <a17-filter v-on:submit="filterListing" v-bind:closed="hasBulkIds">
                        <ul class="secondarynav" slot="navigation">
                            <li v-for="(navItem, index) in navFilters" class="secondarynav__item" :class="{ 's--on' : navActive === index }"><a href="#" v-on:click.prevent="filterStatus(index, navItem.slug)"><span class="secondarynav__link">@{{ navItem.name }}</span><span class="secondarynav__number">(@{{ navItem.number }})</span></a></li>
                        </ul>
                        <div slot="hidden-filters">
                            @yield('hiddenFilters')
                        </div>
                        <div slot="additional-actions"><a17-button variant="validate" size="small" v-on:click="$refs.addNewModal.open()">Add New</a17-button></div>
                    </a17-filter>
                </div>
                <a17-bulk></a17-bulk>
            </div>
            <a17-datatable :draggable="{{ $sort ? 'true' : 'false' }}" empty-message="{{ __($emptyDataTable) }}"></a17-datatable>
            <a17-modal class="modal--form" ref="addNewModal" title="Add New">
                <form action="#">
                    <a17-modal-title-editor v-bind:base-url="baseUrl"></a17-modal-title-editor>
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
        publish: '{{ $publishUrl }}'
    }

    window.STORE.datatable = {
      baseUrl: 'http://pentagram.com/work/',
      page: 1,
      maxPage: {{ $maxPage }},
      offset: {{ $offset }},
      sortKey: 'name',
      sortDir: 'desc'
    }

    window.STORE.datatable.data = {!! json_encode($mappedData) !!}

    window.STORE.datatable.columns = {!! json_encode($mappedColumns) !!}

    window.STORE.datatable.navigation = [
      {
        name: 'All items',
        slug: 'all',
        number: 1253
      },
      {
        name: 'Mine',
        slug: 'mine',
        number: 3
      },
      {
        name: 'Published',
        slug: 'published',
        number: 6
      },
      {
        name: 'Draft',
        slug: 'draft',
        number: 1
      },
      {
        name: 'Trash',
        slug: 'trash',
        number: 1
      }
    ]

    window.STORE.datatable.filter = {
        status: 'all'
    }
@stop

@push('extra_js')
    <script src="{{ mix('/assets/admin/js/manifest.js') }}"></script>
    <script src="{{ mix('/assets/admin/js/vendor.js') }}"></script>
    <script src="{{ mix('/assets/admin/js/main-listing.js') }}"></script>
@endpush
