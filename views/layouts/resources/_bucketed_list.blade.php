@php
    $columns = $columns ?? [
        'title' => [
            'title' => 'Title',
            'present' => true,
            'field' => 'titleInBucket',
        ],
    ];
@endphp
<div class="table_container">
    <table data-bucket="{{ $bucketKey }}"
            data-behavior="sortable"
            data-bucket-name="{{ $bucket['name'] }}"
            data-bucket-limit="{{ $bucket['max_items'] }}"
            data-bucket-add-url="{{ route("admin.featured.$sectionKey.add", ['bucket' => $bucketKey]) }}"
            data-bucket-remove-url="{{ route("admin.featured.$sectionKey.remove", ['bucket' => $bucketKey]) }}"
            data-sortable-update-url="{{ route("admin.featured.$sectionKey.sortable", ['bucket' => $bucketKey]) }}">
        <thead>
            <tr>
                @if ($bucket['max_items'] > 1)
                    <th class="tool" data-content="span.icon.icon-handle"><span class="icon icon-bucket{{ $loop->index + 1 }}"></th>
                @endif
                @foreach($columns as $column)
                    <th>{{ $column['title'] }}</th>
                @endforeach
                <th>Type</th>
                <th class="tool" data-content="a.icon.icon-remove:Remove">—</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                @php
                    $feature = $item;
                    $item = $item->featured;
                @endphp
                <tr data-id="{{ $item->id }}" data-type="{{ $feature->featured_type }}">
                    @if ($bucket['max_items'] > 1)
                        <td><span class="icon icon-handle"></span></td>
                    @endif
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
            @endforeach
            <tr class="empty_table" @if(count($items) > 0) style="display: none;" @endif>
                <td colspan="7"><h2>Nothing featured yet.</h2></td>
            </tr>
        </tbody>
    </table>
</div>
