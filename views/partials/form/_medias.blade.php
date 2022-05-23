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
            @if ($disabled) disabled: true, @endif
            @if ($extraMetadatas) extraMetadatas: {{ json_encode($extraMetadatas) }}, @endif
            @if ($altTextMaxLength) :altTextMaxLength: {{ $altTextMaxLength }}, @endif
            @if ($captionMaxLength) :captionMaxLength: {{ $captionMaxLength }}, @endif
            @if ($required) required: true, @endif
            @if (!$withAddInfo) withAddInfo: false, @endif
            @if (!$withVideoUrl) withVideoUrl: false, @endif
            @if (!$withCaption) withCaption: false, @endif
            @if ($buttonOnTop) buttonOnTop: true, @endif
            @if (!$activeCrop) activeCrop: false, @endif
            {{$formFieldName(true)}}
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
    <a17-inputframe label="{{ $label }}" name="medias.{{ $name }}" @if ($required) :required="true" @endif @if ($fieldNote) note="{{ $fieldNote }}" @endif>
        @if($multiple) <a17-slideshow @else <a17-mediafield @endif
            {{$formFieldName()}}
            crop-context="{{ $name }}"
            :width-min="{{ $widthMin }}"
            :height-min="{{ $heightMin }}"
            @if($multiple) :max="{{ $max }}" @endif
            @if($disabled) disabled @endif
            @if($extraMetadatas) :extra-metadatas="{{ json_encode($extraMetadatas) }}" @endif
            @if($required) :required="true" @endif
            @if(!$withAddInfo) :with-add-info="false" @endif
            @if(!$withVideoUrl) :with-video-url="false" @endif
            @if(!$withCaption) :with-caption="false" @endif
            @if($altTextMaxLength) :alt-text-max-length="{{ $altTextMaxLength }}" @endif
            @if($captionMaxLength) :caption-max-length="{{ $captionMaxLength }}" @endif
            @if($buttonOnTop) :button-on-top="true" @endif
            @if(!$activeCrop) :active-crop="false" @endif
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
