@php
    $options = method_exists($options, 'map') ? $options->map(function($label, $value) {
        return [
            'value' => $value,
            'label' => $label
        ];
    })->values()->toArray() : $options;
    $placeholder = $placeholder ?? false;
    $default = $default ?? false;
@endphp

@if ($unpack ?? false)
    <a17-singleselect
        label="{{ $label }}"
        @include('cms-toolkit::partials.form.utils._field_name')
        :options="{{ json_encode($options) }}"
        @if ($default) selected="{{ $default }}" @endif
        :has-default-store="true"
        in-store="value"
    ></a17-singleselect>
@elseif ($native ?? false)
    <a17-select
        label="{{ $label }}"
        @include('cms-toolkit::partials.form.utils._field_name')
        :options="{{ json_encode($options) }}"
        @if ($placeholder) placeholder="{{ $placeholder }}" @endif
        @if ($default) selected="{{ $default }}" @endif
        :has-default-store="true"
        size="large"
        in-store="value"
    ></a17-vselect>
@else
    <a17-vselect
        label="{{ $label }}"
        @include('cms-toolkit::partials.form.utils._field_name')
        :options="{{ json_encode($options) }}"
        @if ($emptyText ?? false) empty-text="{{ $emptyText }}" @endif
        @if ($placeholder) placeholder="{{ $placeholder }}" @endif
        @if ($default) :selected="{{ json_encode(collect($options)->first(function ($option) use ($default) {
            return $option['value'] === $default;
        })) }}" @endif
        :has-default-store="true"
        size="large"
        in-store="inputValue"
    ></a17-vselect>
@endif

@unless($renderForBlocks || $renderForModal || !isset($item->$name))
@push('vuexStore')
    window.STORE.form.fields.push({
        name: '{{ $name }}',
        value: @if(is_numeric($item->$name)) {{ $item->$name }} @else {!! json_encode($item->$name) !!} @endif
    })
@endpush
@endunless
