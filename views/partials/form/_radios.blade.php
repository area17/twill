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

<a17-singleselect
    label="{{ $label }}"
    @include('cms-toolkit::partials.form.utils._field_name')
    :options="{{ json_encode($options) }}"
    @if ($default) selected="{{ $default }}" @endif
    :grid="false"
    :inline='{{ $inline ? 'true' : 'false' }}'
    @if ($note) note='{{ $note }}' @endif
    :has-default-store="true"
    in-store="value"
></a17-singleselect>

@unless($renderForBlocks || $renderForModal || !isset($item->$name))
@push('vuexStore')
    window.STORE.form.fields.push({
        name: '{{ $name }}',
        value: @if(is_numeric($item->$name)) {{ $item->$name }} @else {!! json_encode($item->$name) !!} @endif
    })
@endpush
@endunless
