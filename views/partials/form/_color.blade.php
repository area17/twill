<a17-colorfield
    label="{{ $label }}"
    default-value="{{ $default ?? '' }}"
    @include('twill::partials.form.utils._field_name')
    in-store="value"
></a17-colorfield>

@unless($renderForBlocks || $renderForModal || (!isset($item->$name) && null == $formFieldsValue = getFormFieldsValue($form_fields, $name)))
@push('vuexStore')
    window['{{ config('twill.js_namespace') }}'].STORE.form.fields.push({
        name: '{{ $name }}',
        value: {!! json_encode($item->$name ?? $formFieldsValue) !!}
    })
@endpush
@endunless
