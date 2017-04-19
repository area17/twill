@if (isset($columnOptions['relationship']))
    {{ array_get($item, "{$columnOptions['relationship']}.{$columnOptions['field']}") }}
@elseif (isset($columnOptions['count']) && $columnOptions['count'])
    {{ $item->{$columnOptions['field']}->count() }}
@elseif (isset($columnOptions['nested']) && $columnOptions['nested'])
    @php
        $nestedCount = $item->{$columnOptions['nested']['field']}->count();
    @endphp
    <a class="main" href="{{ moduleRoute("{$columnOptions['nested']['parent']}.{$columnOptions['nested']['field']}", $routePrefix, $nestedCount > 0 ? 'index' : 'create', [$item->id]) }}">
    @if(($nestedCount) > 0)
        {{ $nestedCount }} {{ $nestedCount > 1 ? str_plural($columnOptions['title']) : str_singular($columnOptions['title']) }}
    @else
        Create {{$columnOptions['title']}}
    @endif
    </a>
@elseif (isset($columnOptions['value']))
    {{ $columnOptions['value'] }}
@elseif(isset($columnOptions['thumb']) && $columnOptions['thumb'])
    <img src="{{ $item->cmsImage(isset($columnOptions['variant']) ? $columnOptions['variant']['role'] : head(array_keys($item->mediasParams)), isset($columnOptions['variant']) ? $columnOptions['variant']['crop'] : head(array_keys(head($item->mediasParams))), isset($columnOptions['variant']) && isset($columnOptions['variant']['params']) ? $columnOptions['variant']['params'] : ['w' => 80, 'h' => 80, 'fit' => 'crop']) }}" width="80" height="80">
@else
    @if(isset($columnOptions['present']) && $columnOptions['present'])
        {!! $item->presentAdmin()->{$columnOptions['field']} !!}
    @else
        {{ $item->{$columnOptions['field']} or 'Empty field' }}
    @endif
@endif
