<a17-locale
    type="a17-filefield"
    :attributes="{
        label: '{{ $label }}',
        itemLabel: '{{ $itemLabel }}',
        note: '{{ $note }}',
        fieldNote: '{{ $fieldNote }}',
        max: {{ $max }},
        filesizeMax: {{ $filesizeMax }},
        @if ($buttonOnTop) buttonOnTop: true, @endif
        {!! $formFieldName(true) !!}
    }"
></a17-locale>

@unless($renderForBlocks)
@push('vuexStore')
    @foreach(getLocales() as $locale)
        @if (isset($form_fields['files']) && isset($form_fields['files'][$locale][$name]))
            window['{{ config('twill.js_namespace') }}'].STORE.medias.selected["{{ $name }}[{{ $locale }}]"] = {!! json_encode($form_fields['files'][$locale][$name]) !!}
        @endif
    @endforeach
@endpush
@endunless
