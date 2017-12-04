@php
    $renderForBlocks = $renderForBlocks ?? false;
@endphp

<a17-colorfield
    label="{{ $label }}"
    @if ($renderForBlocks) :name="fieldName('{{ $name }}')" @else name="{{ $name }}" @endif
    in-store="value"
></a17-colorfield>
