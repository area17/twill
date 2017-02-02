@if (isset($columnOptions['relationship']))
    {{ array_get($item, "{$columnOptions['relationship']}.{$columnOptions['field']}") }}
@elseif (isset($columnOptions['count']) && $columnOptions['count'])
    {{ $item->{$columnOptions['field']}->count() }}
@elseif (isset($columnOptions['nested']) && $columnOptions['nested'])
    <a class="main" href="{{ moduleRoute("{$columnOptions['nested']['parent']}.{$columnOptions['nested']['field']}", $routePrefix, 'index', [$item->id]) }}">
    @if(($nb = $item->{$columnOptions['nested']['field']}->count()) > 0)
        {{ $nb }} {{ $nb > 1 ? $columnOptions['title'] : $columnOptions['title'] }}
    @else
        Create {{$columnOptions['title']}}
    @endif
    </a>
@else
    @if(isset($columnOptions['present']) && $columnOptions['present'])
        {!! $item->presentAdmin()->{$columnOptions['field']} !!}
    @else
        {{ $item->{$columnOptions['field']} }}
    @endif
@endif
