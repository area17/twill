@extends('cms-toolkit::layouts.main')

@section('content')
    <div class="app app--dashboard" id="app" v-cloak>
        <div class="dashboard">
            <a17-shortcut-creator :entities="[ { label: 'Projects', singular: 'Project', number: 2257, url: '/templates/listing' }, { label: 'News articles', singular: 'News article', number: 9434, url: '/templates/listing' }, { label: 'People', singular: 'Member', number: 46, url: '/templates/listing' } ]"></a17-shortcut-creator>

            <div class="container">
                <div class="wrapper wrapper--reverse">
                    <aside class="col col--aside">
                        <a17-stat-feed>
                            Statistics
                        </a17-stat-feed>

                        <a17-popular-feed :entities="[ { name: 'Garden Museum', number: 2257, url: '/templates/listing' }, { name: 'Garden Museum', number: 2257, url: '/templates/listing' }, { name: 'Garden Museum', number: 2257, url: '/templates/listing' } ]">
                            Most Viewed
                        </a17-popular-feed>
                    </aside>
                    <div class="col col--primary">
                        <a17-activity-feed>
                            Activity feed
                        </a17-activity-feed>
                    </div>
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

    window.STORE.datatable.data = {!! json_encode($mappedData) !!}
    window.STORE.datatable.columns = {!! json_encode($mappedColumns) !!}
@stop

@push('extra_js')
    <script src="{{ mix('/assets/admin/js/manifest.js') }}"></script>
    <script src="{{ mix('/assets/admin/js/vendor.js') }}"></script>
    <script src="{{ mix('/assets/admin/js/main-dashboard.js') }}"></script>
@endpush
