@php
    $columns = $columns ?? [
        'title' => [
            'title' => 'Title',
            'field' => 'title',
        ],
    ];
@endphp

@if ($items->currentPage() === 1 && count($items) > 0)
    <div class="row_item" style="background-color: #eee;">
        @foreach($columns as $column)
            <div class="row_item_col">
                {{ $column['title'] }}
            </div>
        @endforeach
    </div>
@endif

@forelse($items as $item)
    <a class="row_item" data-id="{{ $item->id }}" data-name="{{ $item->title or $item->id }}" href="#">
        @foreach ($columns as $column)
            @php
                $columnOptions = $column;
            @endphp
            @if(isset($column['thumb']) && $column['thumb'])
                <div class="row_item_col thumb">
                    @if(head($item->mediasParams))
                        <img src="{{ $item->cmsImage(
                            isset($column['variant']) ? $column['variant']['role'] : head(array_keys($item->mediasParams)),
                            isset($column['variant']) ? $column['variant']['crop'] : head(array_keys(head($item->mediasParams))),
                            isset($column['variant']) && isset($column['variant']['params']) ? $column['variant']['params'] : ['w' => 80, 'h' => 80, 'fit' => 'crop']) }}" width="80" height="80">
                    @endif
                </div>
            @else
                <div class="row_item_col">
                    @resourceView($moduleName, 'column')
                </div>
            @endif
        @endforeach
    </a>
@empty
    <table style="height: 100%">
        <tbody>
          <tr class="empty_table">
            <td><h2>No {{ $title }} found</h2></td>
          </tr>
        </tbody>
    </table>
@endforelse
