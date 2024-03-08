@if ($translated ?? true)
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
@else
    <a17-filefield
        {!! $formFieldName() !!}
        :max="{{ $max }}"
        label="{{ $label }}"
        item-label="{{ $itemLabel }}"
        note="{{ $note }}"
        field-note="{{ $fieldNote }}"
        :filesize-max="{{ $filesizeMax }}"
        @if ($buttonOnTop) :button-on-top="true", @endif
    ></a17-filefield>

    @unless($renderForBlocks)
        @push('vuexStore')
            @if (isset($form_fields['files']) && isset($form_fields['files'][config('app.locale')]) && isset($form_fields['files'][config('app.locale')][$name]))
                window['{{ config('twill.js_namespace') }}'].STORE.medias.selected["{{ $name }}"] = {!! json_encode($form_fields['files'][config('app.locale')][$name]) !!}
            @endif
        @endpush
    @endunless
@endif

