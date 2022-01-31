@unless(\A17\Twill\TwillServiceProvider::supportsBladeComponents())
    @php
        $showMap = $showMap ?? true;
        $openMap = $openMap ?? false;
        $inModal = $fieldsInModal ?? false;
        $saveExtendedData = $saveExtendedData ?? false;
        $autoDetectLatLngValue = $autoDetectLatLngValue ?? false;
    @endphp
@endunless

<a17-locationfield
    label="{{ $label }}"
    @include('twill::partials.form.utils._field_name')
    @if ($showMap) show-map @else :show-map="false" @endif
    @if ($openMap) open-map @endif
    @if ($inModal) :in-modal="true" @endif
    @if ($saveExtendedData) :save-extended-data="true" @endif
    @if ($autoDetectLatLngValue) auto-detect-lat-lng-value @endif
    in-store="value"
></a17-locationfield>

@unless($renderForBlocks || $renderForModal || !isset($item->$name))
@push('vuexStore')
    window['{{ config('twill.js_namespace') }}'].STORE.form.fields.push({
        name: '{{ $name }}',
        value: {!! json_encode($item->$name) !!}
    })
@endpush
@endunless
