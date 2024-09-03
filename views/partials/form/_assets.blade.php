<a17-locale
    type="a17-assetfield"
    :attributes="{
        label: '{{ $label }}',
        note: '{{ $note }}',
        fieldNote: '{{ $fieldNote }}',
        max: {{ $max }},
        @if ($buttonOnTop) buttonOnTop: true, @endif
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
