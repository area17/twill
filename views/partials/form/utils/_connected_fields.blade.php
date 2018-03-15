@php
    if (is_bool($fieldValues)) $fieldValues = $fieldValues ? "true" :  "false";
    else if (is_string($fieldValues)) $fieldValues = "'$fieldValues'";
    else if (is_array($fieldValues)) $fieldValues = json_encode($fieldValues);
@endphp
<a17-connectorfield
    field-name="{{ $fieldName }}"
    @if (is_array($fieldValues)) :required-field-values='{!! $fieldValues !!}'
    @else :required-field-values="{{ $fieldValues }}"
    @endif
>
    {{ $slot }}
</a17-connectorfield>
