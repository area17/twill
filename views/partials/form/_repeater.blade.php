<a17-repeater
    type="{{ $type }}"
    @if ($renderForBlocks ?? false) :name="repeaterName('{{ $type }}')" @else name="{{ $type }}" @endif
></a17-repeater>
