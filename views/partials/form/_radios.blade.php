@php
    $note = $note ?? false;
    $options = method_exists($options, 'map') ? $options->map(function($label, $value) {
        return [
            'value' => $value,
            'label' => $label
        ];
    })->values()->toArray() : $options;
    $placeholder = $placeholder ?? false;
    $default = $default ?? false;
    $inline = $inline ?? false;
@endphp

<a17-radiogroup
    label="{{ $label }}"
    @include('cms-toolkit::partials.form.utils._field_name')
    :radios='{!! json_encode($options) !!}'
    :inline='{{ $inline ? 'true' : 'false' }}'
    @if ($default) initial-value='{{ $default }}' @endif
    @if ($note) note='{{ $note }}' @endif
    :has-default-store="true"
    in-store="currentValue"
></a17-radiogroup>

@unless($renderForBlocks || $renderForModal || !isset($item->$name))
@push('vuexStore')
    window.STORE.form.fields.push({
        name: '{{ $name }}',
        value: @if(is_numeric($item->$name)) {{ $item->$name }} @else {!! json_encode($item->$name) !!} @endif
    })
@endpush
@endunless
