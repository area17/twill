<a17-repeater
    type="{{ $type }}"
    @if ($renderForBlocks) :name="repeaterName('{{ $type }}')" @else name="{{ $type }}" @endif
></a17-repeater>
