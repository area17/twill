@extends('cms-toolkit::layouts.main')

@section('content')
    <div class="app app--listing" id="app" v-cloak>
        <div class="listing">
            <div class="listing__nav">
                <div class="container" ref="form">
                    <a17-filter v-on:submit="filterListing">
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
            <a17-datatable :draggable="true"></a17-datatable>
            <a17-modal class="modal--form" ref="addNewModal" title="Add New">
                <form action="#">
                    <a17-modal-title-editor v-bind:base-url="baseUrl"></a17-modal-title-editor>
                    <a17-modal-validation v-bind:mode="'create'"></a17-modal-validation>
                </form>
            </a17-modal>
        </div>
    </div>
@stop



@push('extra_js')
    <script src="{{ mix('/assets/admin/js/manifest.js') }}"></script>
    <script src="{{ mix('/assets/admin/js/vendor.js') }}"></script>
    <script src="{{ mix('/assets/admin/js/main-listing.js') }}"></script>
@endpush
