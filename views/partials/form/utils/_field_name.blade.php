{{-- @todo: Deprecate, this is only used for custom components now. --}}
@if ($renderForBlocks)
    @if ($asAttributes ?? false)
        name: fieldName('{{ $name }}'),
    @else
        :name="fieldName('{{ $name }}')"
    @endif
@else
    @if ($asAttributes ?? false)
        name: '{{ $name }}',
    @else
        name="{{ $name }}"
    @endif
@endif
