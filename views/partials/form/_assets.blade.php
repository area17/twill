<a17-locale
    type="a17-assetfield"
    :attributes="{
        cropContext: '{{ $name }}',
        label: '{{ $label }}',
        note: '{{ $note }}',
        fieldNote: '{{ $fieldNote }}',
        max: {{ $max }},
        @if ($buttonOnTop) buttonOnTop: true, @endif
        @if($renderForBlocks) fixedErrorKey: $parent.blockFieldName !== undefined ? $parent.blockFieldName('{{$name}}') : '', @endif
        @if ($disabled) disabled: true, @endif
        @if ($extraMetadatas) extraMetadatas: {{ json_encode($extraMetadatas) }}, @endif
        @if ($altTextMaxLength) :altTextMaxLength: {{ $altTextMaxLength }}, @endif
        @if ($captionMaxLength) :captionMaxLength: {{ $captionMaxLength }}, @endif
        @if ($required) required: true, @endif
        @if (!$withAddInfo) withAddInfo: false, @endif
        @if (!$withVideoUrl) withVideoUrl: false, @endif
        @if (!$withCaption) withCaption: false, @endif
        {!! $formFieldName(true) !!}
    }"
></a17-locale>

@unless($renderForBlocks)
    @push('vuexStore')
        @foreach(getLocales() as $locale)
            @if (isset($form_fields['assets']) && isset($form_fields['assets'][$locale][$name]))
                window['{{ config('twill.js_namespace') }}'].STORE.medias.selected["{{ $name }}[{{ $locale }}]"] = {!! json_encode($form_fields['assets'][$locale][$name]) !!}
            @endif
        @endforeach
    @endpush
@endunless
