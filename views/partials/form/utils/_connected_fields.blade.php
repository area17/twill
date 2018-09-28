@php
    $isEqual = $isEqual ?? true;
    $inModal = $fieldsInModal ?? false;
    $keepAlive = $keepAlive ?? false;

    // Field values misc updates
    $fieldType = gettype($fieldValues);
    if ($fieldType === 'boolean') $fieldValues = $fieldValues ? "true" :  "false";
    else if ($fieldType === 'array') $fieldValues = json_encode($fieldValues);
@endphp
<a17-connectorfield
    @if ($isEqual) :is-value-equal="true" @else :is-value-equal="false" @endif
    @if ($inModal) :in-modal="true" @endif
    @if ($keepAlive) :keep-alive="true" @endif

    @if ($renderForBlocks) :field-name="fieldName('{{ $fieldName }}')"
    @else field-name="{{ $fieldName }}"
    @endif

    @if ($fieldType === 'array') :required-field-values='{!! $fieldValues !!}'
    @elseif ($fieldType === 'string') :required-field-values="'{{ $fieldValues }}'"
    @else :required-field-values="{{ $fieldValues }}"
    @endif
>
    {{ $slot }}
</a17-connectorfield>
