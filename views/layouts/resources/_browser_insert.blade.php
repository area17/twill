@php
    $headers_only = $headers_only ?? false;
    $with_sort = $with_sort ?? true;
    $columns = $columns ?? [
        'title' => [
            'title' => 'Title',
            'field' => 'title',
        ],
    ];
@endphp

@if($headers_only)
    @if($with_multiple && $with_sort)
        <th class="tool"></th>
    @endif
    @foreach($columns as $column)
        <th>
            {{ $column['title'] }}
        </th>
    @endforeach
    <th class="tool"></th>
@else
    @foreach($items as $id => $item)
        <tr class="media-row media-row-new" id="media-box-{{ $item->id }}" data-id="{{ $item->id }}">
            @if($with_multiple && $with_sort)
                <td><span class="icon icon-handle"></span></td>
            @endif
            @foreach ($columns as $column)
                @php
                    $columnOptions = $column;
                @endphp
                <td class="{{ isset($column['thumb']) && $column['thumb'] ? 'thumb' : '' }}">
                    @if(isset($column['thumb']) && $column['thumb'])
                        @if(head($item->mediasParams))
                            <img src="{{ $item->cmsImage(
                                isset($column['variant']) ? $column['variant']['role'] : head(array_keys($item->mediasParams)),
                                isset($column['variant']) ? $column['variant']['crop'] : head(array_keys(head($item->mediasParams))),
                                isset($column['variant']) && isset($column['variant']['params']) ? $column['variant']['params'] : ['w' => 80, 'h' => 80, 'fit' => 'crop']) }}" width="80" height="80">
                        @endif
                    @elseif (isset($column['edit_link_url_segment']))
                        <a href="//admin.{{ config('app.url') }}/{{ $column['edit_link_url_segment'] }}/{{ $item->id }}/edit" class="main">
                            @resourceView((isset($element_role) ? camel_case($element_role) : $moduleName), 'column')
                        </a>
                    @else
                        @resourceView((isset($element_role) ? camel_case($element_role) : $moduleName), 'column')
                    @endif
                </td>
            @endforeach
            @if($with_delete ?? true)
                <td><a class="icon icon-trash" href="#" data-media-remove-trigger rel="nofollow">Destroy</a></td>
            @endif
        </tr>
    @endforeach
@endif
