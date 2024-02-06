@php
    $isEqual = $isEqual ?? true;
    $inModal = $fieldsInModal ?? false;
    $isBrowser = $isBrowser ?? false;
    $matchEmptyBrowser = $matchEmptyBrowser ?? false;
    $arrayContains = $arrayContains ?? true;
    $keepAlive = $keepAlive ?? false;

    if (! $isBrowser) {
        // Field values misc updates
        $fieldType = gettype($fieldValues);
        if ($fieldType === 'boolean') $fieldValues = $fieldValues ? "true" :  "false";
        else if ($fieldType === 'array') $fieldValues = json_encode($fieldValues);
    }
@endphp
<a17-connectorfield
    @if ($isEqual) :is-value-equal="true" @else :is-value-equal="false" @endif
    @if ($inModal) :in-modal="true" @endif
    @if ($keepAlive) :keep-alive="true" @endif

    @if ($arrayContains) :array-contains="true" @else :array-contains="false" @endif

    @if ($isBrowser) :is-browser="true" @endif
    @if ($matchEmptyBrowser) :match-empty-browser="true" @endif

    @if ($renderForBlocks) :field-name="fieldName('{{ $fieldName }}')"
    @else field-name="{{ $fieldName }}"
    @endif

    @unless($isBrowser)
        @if ($fieldType === 'array') :required-field-values='{!! $fieldValues !!}'
        @elseif ($fieldType === 'string') :required-field-values="'{{ $fieldValues }}'"
        @else :required-field-values="{{ $fieldValues }}"
        @endif
    @endunless
>
    {!! $slot !!}
</a17-connectorfield>
