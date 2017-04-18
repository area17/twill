@php
    $columns = $columns ?? [
        'title' => [
            'title' => 'Title',
            'field' => 'title',
        ]
    ];
@endphp
<div class="table_container">
    <table data-bucket="{{ $bucketKey }}"
            data-behavior="sortable"
            data-bucket-limit="{{ $bucket['max_items'] }}"
            data-bucket-add-url="{{ route("admin.featured.$sectionKey.add", ['bucket' => $bucketKey]) }}"
            data-bucket-remove-url="{{ route("admin.featured.$sectionKey.remove", ['bucket' => $bucketKey]) }}"
            data-sortable-update-url="{{ route("admin.featured.$sectionKey.sortable", ['bucket' => $bucketKey]) }}">
        <thead>
            <tr>
                <th class="tool" data-content="span.icon.icon-handle"><span class="icon icon-bucket{{ $loop->index + 1 }}"></th>
                @foreach($columns as $column)
                    <th>{{ $column['title'] }}</th>
                @endforeach
                <th>Type</th>
                <th class="tool" data-content="a.icon.icon-remove:Remove">—</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
                @php
                    $feature = $item;
                    $item = $item->featured;
                @endphp
                <tr data-id="{{ $item->id }}" data-type="{{ $feature->featured_type }}">
                    <td><span class="icon icon-handle"></span></td>
                    @foreach($columns as $columnOptions)
                        <td>
                            @resourceView($feature->featured_type, 'column')
                        </td>
                    @endforeach
                        <td>
                            {{ array_key_exists($feature->featured_type, $featurableItemsByBucketable) ? $featurableItemsByBucketable[$feature->featured_type]['name'] : ucfirst($feature->featured_type) }}
                        </td>
                    <td><a href="#" class="icon icon-remove">Delete</a></td>
                </tr>
            @empty
            <tr class="empty_table">
                <td colspan="7"><h2>Nothing featured yet.</h2></td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
