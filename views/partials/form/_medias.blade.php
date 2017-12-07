@php
    $max = $max ?? 1;
    $renderForBlocks = $renderForBlocks ?? false;
@endphp

<a17-inputframe label="{{ $label }}">
    @if($max > 1)
        <a17-slideshow
            @if ($renderForBlocks) :name="fieldName('{{ $name }}')" @else name="{{ $name }}" @endif
            :max="{{ $max ?? 1 }}"
            crop-context="{{ $name }}"
            @if ($required ?? false) required @endif
        >{{ $note or '' }}</a17-slideshow>
    @else
        <a17-mediafield
            @if ($renderForBlocks) :name="fieldName('{{ $name }}')" @else name="{{ $name }}" @endif
            crop-context="{{ $name }}"
            @if ($required ?? false) required @endif
        >{{ $note or '' }}</a17-mediafield>
    @endif
</a17-inputframe>

@unless($renderForBlocks)
@push('fieldsStore')
    @if (isset($form_fields['medias']) && isset($formFields['medias'][$name]))
        window.STORE.medias.selected["{{ $name }}"] = {!! json_encode($form_fields['medias'][$name]) !!}
    @endif
@endpush
@endunless
