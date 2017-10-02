@php
    $columns = $columns ?? [
        'title' => [
            'title' => 'Title',
            'present' => true,
            'field' => 'titleInBucket',
        ],
        'type' => [
            'title' => 'Type',
            'hidden' => true,
            'value' => $bucketableName,
        ],
    ];

    $bucketTarget = '';
    foreach ($buckets as $bucket) {
        $bucketTarget .= $bucket . ':icon-bucket' . (array_search($bucket, $all_buckets->keys()->toArray()) + 1) . ',';
    }
    $bucketTarget =  rtrim($bucketTarget, ',');
@endphp
<div class="table_container">
    {{-- <div class="filter" style="margin: -15px 0 0 0; background-color: #fff;">
        <form method="GET" action="" accept-charset="UTF-8" novalidate="novalidate">
            <input type="hidden" name="bucketable" value="{{ $bucketable }}">
            <input type="text" name="search_{{ $bucketable }}" placeholder="Search" autocomplete="off" size="20" value="{{ $search or '' }}">
            <input type="submit" class="btn btn-small" value="Search">
            <a href="#" data-role="clear">Clear</a>
        </form>
    </div> --}}
    <table data-behavior="add_to_bucket" data-bucket-target="{{ $bucketTarget }}" data-bucket-restricted>
        <thead>
            <tr>
                @foreach($columns as $column)
                    <th @if($column['hidden'] ?? false)class="hide"@endif>{{ $column['title'] }}</th>
                @endforeach
                @foreach($buckets as $bucket)
                    <th class="tool"></th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
                <tr data-id="{{ $item->id }}" data-type="{{ $bucketable }}">
                    @foreach($columns as $columnOptions)
                        <td class="{{ isset($columnOptions['hidden']) && $columnOptions['hidden'] ? 'hide' : (isset($columnOptions['thumb']) && $columnOptions['thumb'] ? 'thumb' : '') }}">
                            @resourceView($bucketable, 'column')
                        </td>
                    @endforeach
                    @foreach($buckets as $bucket)
                        <td><a href="#" class="icon icon-bucket{{ array_search($bucket, $all_buckets->keys()->toArray()) + 1 }}">Bucket {{ $loop->index + 1 }}</a></td>
                    @endforeach
                </tr>
            @empty
                <tr class="empty_table">
                    <td colspan="7">
                        <h2>No {{ $bucketable }}</h2>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@if (method_exists($items, 'total'))
    @resourceView($bucketable, 'paginator', ['items' => $items])
@endif
