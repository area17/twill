@php
    $max = $max ?? 1;
    $required = $required ?? false;
    $note = $note ?? '';
    $withAddInfo = $withAddInfo ?? true;
    $withVideoUrl = $withVideoUrl ?? true;
    $withCaption = $withCaption ?? true;
    $extraMetadatas = $extraMetadatas ?? false;
@endphp

@if (config('twill.media_library.translated_form_fields', $translated ?? false) && ($translated ?? true))
    <a17-locale
        type="a17-mediafield-translated"
        :attributes="{
            label: '{{ $label }}',
            cropContext: '{{ $name }}',
            max: {{ $max }},
            @if ($extraMetadatas) extraMetadatas: {{ json_encode($extraMetadatas) }}, @endif
            @if ($required) required: true, @endif
            @if (!$withAddInfo) withAddInfo: false, @endif
            @if (!$withVideoUrl) withVideoUrl: false, @endif
            @if (!$withCaption) withCaption: false, @endif
            @include('twill::partials.form.utils._field_name', ['asAttributes' => true])
        }"
    ></a17-locale>

    @unless($renderForBlocks)
    @push('vuexStore')
        @foreach(getLocales() as $locale)
            @if (isset($form_fields['medias']) && isset($form_fields['medias'][$locale][$name]))
                window.STORE.medias.selected["{{ $name }}[{{ $locale }}]"] = {!! json_encode($form_fields['medias'][$locale][$name]) !!}
            @endif
        @endforeach
    @endpush
    @endunless
@else
    <a17-inputframe label="{{ $label }}" name="medias.{{ $name }}" @if ($required) :required="true" @endif>
        @if($max > 1 || $max == 0)
            <a17-slideshow
                @include('twill::partials.form.utils._field_name')
                :max="{{ $max }}"
                crop-context="{{ $name }}"
                @if ($extraMetadatas) :extra-metadatas="{{ json_encode($extraMetadatas) }}" @endif
                @if ($required) :required="true" @endif
                @if (!$withAddInfo) :with-add-info="false" @endif
                @if (!$withVideoUrl) :with-video-url="false" @endif
                @if (!$withCaption) :with-caption="false" @endif
            >{{ $note }}</a17-slideshow>
        @else
            <a17-mediafield
                @include('twill::partials.form.utils._field_name')
                crop-context="{{ $name }}"
                @if ($extraMetadatas) :extra-metadatas="{{ json_encode($extraMetadatas) }}" @endif
                @if ($required) :required="true" @endif
                @if (!$withAddInfo) :with-add-info="false" @endif
                @if (!$withVideoUrl) :with-video-url="false" @endif
                @if (!$withCaption) :with-caption="false" @endif
            >{{ $note }}</a17-mediafield>
        @endif
    </a17-inputframe>

    @unless($renderForBlocks)
    @push('vuexStore')
        @if (isset($form_fields['medias']) && isset($form_fields['medias'][$name]))
            window.STORE.medias.selected["{{ $name }}"] = {!! json_encode($form_fields['medias'][$name]) !!}
        @endif
    @endpush
    @endunless
@endif
