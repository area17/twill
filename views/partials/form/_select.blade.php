@php
    $note = $note ?? false;
    $options = method_exists($options, 'map') ? $options->map(function($label, $value) {
        return [
            'value' => $value,
            'label' => $label
        ];
    })->values()->toArray() : $options;
    $placeholder = $placeholder ?? false;
    $required = $required ?? false;
    $default = $default ?? false;

    # Add new option
    $addNew = $addNew ?? false;
    $moduleName = $moduleName ?? null;
    $storeUrl = $storeUrl ?? '';
    $inModal = $fieldsInModal ?? false;
@endphp

@if ($unpack ?? false)
    <a17-singleselect
        label="{{ $label }}"
        @include('cms-toolkit::partials.form.utils._field_name')
        :options='{!! json_encode($options) !!}'
        @if ($default) selected="{{ $default }}" @endif
        @if ($required) :required="true" @endif

        @if ($inModal) :in-modal="true" @endif
        @if ($addNew) add-new='{{ $name }}Modal'
        @elseif ($note) note='{{ $note }}'
        @endif

        :has-default-store="true"
        in-store="value"
    ></a17-singleselect>
@elseif ($native ?? false)
    <a17-select
        label="{{ $label }}"
        @include('cms-toolkit::partials.form.utils._field_name')
        :options='{!! json_encode($options) !!}'
        @if ($placeholder) placeholder="{{ $placeholder }}" @endif
        @if ($default) selected="{{ $default }}" @endif

        @if ($required) :required="true" @endif
        @if ($inModal) :in-modal="true" @endif
        @if ($addNew) add-new='{{ $name }}Modal'
        @elseif ($note) note='{{ $note }}'
        @endif

        :has-default-store="true"
        size="large"
        in-store="value"
    ></a17-select>
@else
    <a17-vselect
        label="{{ $label }}"
        @include('cms-toolkit::partials.form.utils._field_name')
        :options='{!! json_encode($options) !!}'
        @if ($emptyText ?? false) empty-text="{{ $emptyText }}" @endif
        @if ($placeholder) placeholder="{{ $placeholder }}" @endif
        @if ($default) :selected="{{ json_encode(collect($options)->first(function ($option) use ($default) {
            return $option['value'] === $default;
        })) }}" @endif

        @if ($required) :required="true" @endif
        @if ($inModal) :in-modal="true" @endif
        @if ($addNew) add-new='{{ $name }}Modal'
        @elseif ($note) note='{{ $note }}'
        @endif

        :has-default-store="true"
        size="large"
        in-store="inputValue"
    ></a17-vselect>
@endif

@unless($renderForBlocks || $renderForModal || (!isset($item->$name) && null == $formFieldsValue = getFormFieldsValue($form_fields, $name)))
@push('vuexStore')
    window.STORE.form.fields.push({
        name: '{{ $name }}',
        value: @if(isset($item) && is_numeric($item->$name)) {{ $item->$name }} @else {!! json_encode($item->$name ?? $formFieldsValue) !!} @endif
    })
@endpush
@endunless

@if($addNew)
@push('modalAttributes')
    <a17-modal-add ref="{{ $name }}Modal" name="{{ $name }}" :form-create="'{{ $storeUrl }}'">
        {{-- fieldsInModal will manage fields separately --}}
        {{-- permalink and translateTitle should not be defined here --}}
        @partialView(($moduleName ?? null), 'create', ['renderForModal' => true, 'fieldsInModal' => true, 'permalink' => false, 'translateTitle' => false])
    </a17-modal-add>
@endpush
@endif
