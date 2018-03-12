@php
    $note = $note ?? false;
    $options = method_exists($options, 'map') ? $options->map(function($label, $value) {
        return [
            'value' => $value,
            'label' => $label
        ];
    })->values()->toArray() : $options;
@endphp

@if ($unpack ?? true)
    <a17-multiselect
        label="{{ $label }}"
        @include('cms-toolkit::partials.form.utils._field_name')
        :options="{{ json_encode($options) }}"
        :grid="true"
        :inline="false"
        @if ($min ?? false) :min="{{ $min }}" @endif
        @if ($max ?? false) :max="{{ $max }}" @endif
        @if ($note) note='{{ $note }}' @endif
        in-store="currentValue"
    ></a17-multiselect>
@else
    <a17-vselect
        label="{{ $label }}"
        @include('cms-toolkit::partials.form.utils._field_name')
        :options='{!! json_encode($options) !!}'
        @if ($emptyText ?? false) empty-text="{{ $emptyText }}" @endif
        @if ($placeholder ?? false) placeholder="{{ $placeholder }}" @endif
        @if ($note) note='{{ $note }}' @endif
        :multiple="true"
        in-store="inputValue"
    ></a17-vselect>
@endif

@unless($renderForBlocks || $renderForModal || (!isset($item->$name) && null == $formFieldsValue = getFormFieldsValue($form_fields, $name)))
@push('vuexStore')
    window.STORE.form.fields.push({
        name: '{{ $name }}',
        value: {!! json_encode(isset($item) && isset($item->$name) ? array_pluck($item->$name, 'id') : $formFieldsValue) !!}
    })
@endpush
@endunless
