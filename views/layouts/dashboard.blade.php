@extends('cms-toolkit::layouts.main')

@php
    $emptyMessage = $emptyMessage ?? "You don't have any activity yet.";
@endphp

@section('appTypeClass', 'app--dashboard')

@section('primaryNavigation')
  <div class="dashboard__search" id="searchApp" v-cloak>
    <a17-search endpoint="http://www.mocky.io/v2/5a7b81d43000004b0028bf3d" type="dashboard"></a17-search>
  </div>
@stop

@section('content')
    <div class="dashboard">
        <a17-shortcut-creator :entities="[ { label: 'Projects', singular: 'Project', number: 2257, url: '/templates/listing' }, { label: 'News articles', singular: 'News article', number: 9434, url: '/templates/listing' }, { label: 'People', singular: 'Member', number: 46, url: '/templates/listing' } ]"></a17-shortcut-creator>

        <div class="container">
            <div class="wrapper wrapper--reverse">
                <aside class="col col--aside">
                    <a17-stat-feed :facts="[ { label: 'Users', figure: '7K', insight: '53% Bounce rate', trend: 'up', url: '/analytics' }, { label: 'Pageviews', figure: '8,4K', insight: '3,8 Pages / Session', trend: 'down', url: '/analytics' }, { label: 'Contact requests', figure: '534', insight: '5 Abandoned', trend: 'up', url: '/analytics' }, { label: 'Newsletters sign up', figure: '15', trend: 'down', insight: '3 Unverified', url: '/analytics' } ]">
                        Statistics
                    </a17-stat-feed>

                    <a17-popular-feed :entities="[ { name: 'Garden Museum', number: 2546, url: '/templates/listing' }, { name: 'London Design Festival 2017', number: 1345, url: '/templates/listing' }, { name: 'The National', number: 1344, url: '/templates/listing' }, { name: 'The Hollow Woods: Storytelling', number: 1125, url: '/templates/listing' }, { name: 'William Russell: A Collection', number: 852, url: '/templates/listing' }, { name: 'Red Trees', number: 340, url: '/templates/listing' }, { name: 'Musuem für Film umd Fernsehen', number: 178, url: '/templates/listing' }, { name: 'Shakespeare in the Park 2017', number: 152, url: '/templates/listing' }, { name: 'Artifact', number: 150, url: '/templates/listing' }, { name: 'Dance Ink (Vol. 8, No. 2)', number: 150, url: '/templates/listing' } ]">
                        Most Viewed
                    </a17-popular-feed>
                </aside>
                <div class="col col--primary">
                    <a17-activity-feed empty-message="{{ __($emptyMessage)  }}"></a17-activity-feed>
                </div>
            </div>
        </div>
    </div>
@stop

@section('initialStore')
    window.CMS_URLS = {
        publish: '{{ $publishUrl }}'
    }

    window.STORE.datatable = {}

    window.STORE.datatable.data = {!! json_encode($tableData) !!}
    window.STORE.datatable.columns = {!! json_encode($tableColumns) !!}
@stop

@push('extra_js')
    <script src="{{ mix('/assets/admin/js/manifest.js') }}"></script>
    <script src="{{ mix('/assets/admin/js/vendor.js') }}"></script>
    <script src="{{ mix('/assets/admin/js/main-dashboard.js') }}"></script>
@endpush
