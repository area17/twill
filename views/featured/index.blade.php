{{-- @extends('cms-toolkit::layouts.main')

@section('content')
    <div class="columns">
        <div class="col">
            @php
                $bucketablesList = collect(array_keys($featurableItemsByBucketable))->toArray();
            @endphp
            @if (count($bucketablesList) > 1)
                <div class="box">
                    <header class="header_small">
                        <h3>What would you like to feature today?</h3>
                    </header>
                    <div class="simple_form">
                        @formField('select', [
                            'field' => "bucketable",
                            'field_name' => "",
                            'list' => collect($featurableItemsByBucketable)->map(function ($bucketable) {
                                return $bucketable['name'];
                            })->values()->toArray(),
                            'data_behavior' => 'selector connected_select',
                            'data_connected_actions' => 'featurable_actions',
                        ])
                        <script>
                            var featurable_actions = [
                                @foreach($bucketablesList as $index => $bucketable)
                                    {
                                      "target": "#{{ $bucketable }}",
                                      "value": "",
                                      "perform": "hide, disable"
                                    },
                                    {
                                      "target": "#{{ $bucketable }}",
                                      "value": "{{ $index }}",
                                      "perform": "show, enable"
                                    }@unless($loop->last),@endunless
                                @endforeach
                            ];
                        </script>
                    </div>
                </div>
            @endif
            @foreach($featurableItemsByBucketable as $bucketable => $featurableItems)
                <section class="box" data-behavior="ajax_listing" id="{{ $bucketable }}">
                    <header class="header_small">
                        <h3><b>{{ $featurableItems['name'] }}</b></h3>
                    </header>
                    @resourceView($bucketable, 'bucketable_list', [
                        'bucketable' => $bucketable,
                        'bucketableName' => $featurableItems['name'],
                        'items' => $featurableItems['items'],
                        'buckets' => $featurableItems['buckets'],
                        'all_buckets' => $buckets
                    ])
                </section>
            @endforeach
        </div>
        <div class="col">
            @foreach($buckets as $bucketKey => $bucket)
                <section class="box box-bucket{{ $loop->index + 1 }}">
                    <header>
                        <h3>{{ $bucket['name'] }}</h3>
                    </header>
                    @extendableView('bucketed_list', [
                        'bucketKey' => $bucketKey,
                        'bucket' => $bucket,
                        'sectionKey' => $sectionKey,
                        'items' => $featuredItemsByBucket[$bucketKey] ?? [],
                    ])
                </section>
            @endforeach
        </div>
    </div>
@endsection

@section('footer')
    <footer id="footer">
        <ul>
            <li><a href="{{ route("admin.featured.$sectionKey.save") }}" class="btn btn-primary">Save</a></li>
            <li><a href="{{ route("admin.featured.$sectionKey.cancel")  }}" class="btn btn-secondary">Cancel</a></li>
        </ul>
    </footer>
@stop
 --}}
