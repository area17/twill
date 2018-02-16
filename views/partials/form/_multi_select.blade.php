@php
    $note = $note ?? false;
    $options = method_exists($options, 'map') ? $options->map(function($label, $value) {
        return [
            'value' => $value,
            'label' => $label
        ];
    })->values()->toArray() : $options;
@endphp

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

@unless($renderForBlocks || $renderForModal || !isset($item->$name))
@push('vuexStore')
    window.STORE.form.fields.push({
        name: '{{ $name }}',
        value: {!! json_encode(array_pluck($item->$name, 'id')) !!}
    })
@endpush
@endunless
