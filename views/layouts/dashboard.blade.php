@extends('cms-toolkit::layouts.main')

@php
    $emptyMessage = $emptyMessage ?? "You don't have any activity yet.";
@endphp

@section('appTypeClass', 'body--dashboard')

@section('primaryNavigation')
  <div class="dashboardSearch" id="searchApp" v-cloak>
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

                    <a17-feed :entities="[ { name: 'Garden Museum', number: 2546, url: '/templates/listing', external: true }, { name: 'London Design Festival 2017', number: 1345, url: '/templates/listing', external: true }, { name: 'The National', number: 1344, url: '/templates/listing', external: true }, { name: 'The Hollow Woods: Storytelling', number: 1125, url: '/templates/listing', external: true }, { name: 'William Russell: A Collection', number: 852, url: '/templates/listing', external: true }, { name: 'Red Trees', number: 340, url: '/templates/listing', external: true }, { name: 'Musuem für Film umd Fernsehen', number: 178, url: '/templates/listing', external: true }, { name: 'Shakespeare in the Park 2017', number: 152, url: '/templates/listing', external: true }, { name: 'Artifact', number: 150, url: '/templates/listing', external: true }, { name: 'Dance Ink (Vol. 8, No. 2)', number: 150, url: '/templates/listing', external: true } ]">
                        Most Viewed
                    </a17-feed>

                    <a17-feed :entities="[ { name: 'Garden Museum', url: '/fe/templates/form', type: 'Projects' }, { name: 'London Design Festival 2017', url: '/fe/templates/form', type: 'News' }, { name: 'The National', url: '/fe/templates/form', type: 'Partners' }, { name: 'The Hollow Woods: Storytelling', url: '/fe/templates/form', type: 'News' }, { name: 'William Russell: A Collection', url: '/fe/templates/form', type: 'News' }, { name: 'Red Trees', url: '/fe/templates/form', type: 'Partners' }, { name: 'Musuem für Film umd Fernsehen', url: '/fe/templates/form', type: 'Projects' }, { name: 'Shakespeare in the Park 2017', url: '/fe/templates/form', type: 'News' }, { name: 'Artifact', url: '/fe/templates/form', type: 'News' }, { name: 'Dance Ink (Vol. 8, No. 2)', url: '/fe/templates/form', type: 'Projects' } ]">
                        My drafts
                    </a17-feed>

                    <a17-feed :entities="[ { thumbnail: 'https://source.unsplash.com/random/80x80?sig=1', name: 'Garden Museum', url: '/fe/templates/form' }, { thumbnail: 'https://source.unsplash.com/random/80x80?sig=1', name: 'London Design Festival 2017', url: '/fe/templates/form' }, { thumbnail: 'https://source.unsplash.com/random/80x80?sig=1', name: 'The National', url: '/fe/templates/form' }, { thumbnail: 'https://source.unsplash.com/random/80x80?sig=1', name: 'The Hollow Woods: Storytelling', url: '/fe/templates/form' }, { thumbnail: 'https://source.unsplash.com/random/80x80?sig=1', name: 'William Russell: A Collection', url: '/fe/templates/form' }, { thumbnail: 'https://source.unsplash.com/random/80x80?sig=1', name: 'Red Trees', url: '/fe/templates/form' }, { thumbnail: 'https://source.unsplash.com/random/80x80?sig=1', name: 'Musuem für Film umd Fernsehen', url: '/fe/templates/form' }, { thumbnail: 'https://source.unsplash.com/random/80x80?sig=1', name: 'Shakespeare in the Park 2017', url: '/fe/templates/form' }, { thumbnail: 'https://source.unsplash.com/random/80x80?sig=1', name: 'Artifact', url: '/fe/templates/form' }, { thumbnail: 'https://source.unsplash.com/random/80x80?sig=1', name: 'Dance Ink (Vol. 8, No. 2)', url: '/fe/templates/form' } ]">
                        My drafts (thumbnails)
                    </a17-feed>
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

    window.STORE.datatable.mine = {!! json_encode($myActivityData) !!}
    window.STORE.datatable.all = {!! json_encode($allActivityData) !!}

    window.STORE.datatable.data = window.STORE.datatable.all
    window.STORE.datatable.columns = {!! json_encode($tableColumns) !!}
@stop

@push('extra_js')
    <script src="{{ mix('/assets/admin/js/manifest.js') }}"></script>
    <script src="{{ mix('/assets/admin/js/vendor.js') }}"></script>
    <script src="{{ mix('/assets/admin/js/main-dashboard.js') }}"></script>
@endpush
