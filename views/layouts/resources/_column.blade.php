@if (isset($columnOptions['relationship']))
    {{ array_get($item, "{$columnOptions['relationship']}.{$columnOptions['field']}") }}
@elseif (isset($columnOptions['count']) && $columnOptions['count'])
    {{ $item->{$columnOptions['field']}->count() }}
@else
    @if(isset($columnOptions['present']) && $columnOptions['present'])
        {!! $item->presentAdmin()->{$columnOptions['field']} !!}
    @else
        {{ $item->{$columnOptions['field']} }}
    @endif
@endif
