@if (config('twill.media_library.translated_form_fields', $translated ?? false) && ($translated ?? true))
    <a17-locale
        type="a17-mediafield-translated"
        :attributes="{
            label: '{{ $label }}',
            cropContext: '{{ $name }}',
            max: {{ $max }},
            widthMin: {{ $widthMin }},
            heightMin: {{ $heightMin }},
            note: '{{ $fieldNote }}',
            @if($renderForBlocks ?? false) fixedErrorKey: $parent.blockFieldName !== undefined ? $parent.blockFieldName('{{$name}}') : '', @endif
            @if ($disabled ?? false) disabled: true, @endif
            @if ($extraMetadatas ?? false) extraMetadatas: {{ json_encode($extraMetadatas) }}, @endif
            @if ($altTextMaxLength ?? false) :altTextMaxLength: {{ $altTextMaxLength }}, @endif
            @if ($captionMaxLength ?? false) :captionMaxLength: {{ $captionMaxLength }}, @endif
            @if ($required) required: true, @endif
            @if (!$withAddInfo ?? false) withAddInfo: false, @endif
            @if (!$withVideoUrl ?? false) withVideoUrl: false, @endif
            @if (!$withCaption ?? false) withCaption: false, @endif
            @if ($buttonOnTop) buttonOnTop: true, @endif
            @if (!$activeCrop ?? false) activeCrop: false, @endif
            {!! $formFieldName(true) !!}
        }"
    >
        {{ $note }}
    </a17-locale>

    @unless($renderForBlocks)
    @push('vuexStore')
        @foreach(getLocales() as $locale)
            @if (isset($form_fields['medias']) && isset($form_fields['medias'][$locale][$name]))
                window['{{ config('twill.js_namespace') }}'].STORE.medias.selected["{{ $name }}[{{ $locale }}]"] = {!! json_encode($form_fields['medias'][$locale][$name]) !!}
            @endif
        @endforeach
    @endpush
    @endunless
@else
    <a17-inputframe @if($renderForBlocks) :fixed-error-key="$parent.blockFieldName !== undefined ? $parent.blockFieldName('{{$name}}') : ''" @endif label="{{ $label }}" name="medias.{{ $name }}" @if ($required) :required="true" @endif @if ($fieldNote) note="{{ $fieldNote }}" @endif>
        @if($multiple) <a17-slideshow @else <a17-mediafield @endif
            {!! $formFieldName() !!}
            crop-context="{{ $name }}"
            :width-min="{{ $widthMin }}"
            :height-min="{{ $heightMin }}"
            @if($multiple ?? false) :max="{{ $max }}" @endif
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
        >{{ $note }}@if($multiple) </a17-slideshow> @else </a17-mediafield> @endif
    </a17-inputframe>

    @unless($renderForBlocks)
    @push('vuexStore')
        @if (isset($form_fields['medias']) && isset($form_fields['medias'][$name]))
            window['{{ config('twill.js_namespace') }}'].STORE.medias.selected["{{ $name }}"] = {!! json_encode($form_fields['medias'][$name]) !!}
        @endif
    @endpush
    @endunless
@endif
