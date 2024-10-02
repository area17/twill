@if (config('twill.media_library.translated_asset_fields', $translated ?? false) && ($translated ?? true))
    <a17-locale
        type="a17-assetfield-translated"
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
@else
    <a17-inputframe @if($renderForBlocks) :fixed-error-key="$parent.blockFieldName !== undefined ? $parent.blockFieldName('{{$name}}') : ''" @endif label="{{ $label }}" name="medias.{{ $name }}" @if ($required) :required="true" @endif @if ($fieldNote) note="{{ $fieldNote }}" @endif>
        <a17-assetfield
            {!! $formFieldName() !!}
            crop-context="{{ $name }}"
            :max="{{ $max }}"
            @if($disabled ?? false) disabled @endif
            @if($extraMetadatas ?? false) :extra-metadatas="{{ json_encode($extraMetadatas) }}" @endif
            @if($required ?? false) :required="true" @endif
            @if(!$withAddInfo ?? false) :with-add-info="false" @endif
            @if(!$withVideoUrl ?? false) :with-video-url="false" @endif
            @if(!$withCaption ?? false) :with-caption="false" @endif
            @if($altTextMaxLength ?? false) :alt-text-max-length="{{ $altTextMaxLength }}" @endif
            @if($captionMaxLength ?? false) :caption-max-length="{{ $captionMaxLength }}" @endif
            @if($buttonOnTop ?? false) :button-on-top="true" @endif
            @if(!$activeCrop ?? false) :active-crop="false" @endif
        >{{ $note }}</a17-assetfield>
    </a17-inputframe>

    @unless($renderForBlocks)
        @push('vuexStore')
            @if (isset($form_fields['assets']) && isset($form_fields['assets'][$name]))
                window['{{ config('twill.js_namespace') }}'].STORE.medias.selected["{{ $name }}"] = {!! json_encode($form_fields['assets'][$name]) !!}
            @endif
        @endpush
    @endunless

@endif
