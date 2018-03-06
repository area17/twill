@php
    $note = $note ?? false;
    $options = method_exists($options, 'map') ? $options->map(function($label, $value) {
        return [
            'value' => $value,
            'label' => $label
        ];
    })->values()->toArray() : $options;
    $inline = $inline ?? false;

    # Add new option
    $addNew = $addNew ?? false;
    $moduleName = $moduleName ?? null;
    $storeUrl = $storeUrl ?? '';
    $inModal = $fieldsInModal ?? false;
@endphp

<a17-multiselect
    label="{{ $label }}"
    @include('cms-toolkit::partials.form.utils._field_name')
    :options="{{ json_encode($options) }}"
    :grid="false"
    :inline='{{ $inline ? 'true' : 'false' }}'
    @if ($min ?? false) :min="{{ $min }}" @endif
    @if ($max ?? false) :max="{{ $max }}" @endif
    @if ($note) note='{{ $note }}' @endif
    @if ($inModal) :in-modal="true" @endif
    in-store="currentValue"
></a17-multiselect>

@unless($renderForBlocks || $renderForModal || (!isset($item->$name) && null == $formFieldsValue = getFormFieldsValue($form_fields, $name)))
@push('vuexStore')
    window.STORE.form.fields.push({
        name: '{{ $name }}',
        value: {!! json_encode(isset($item) && isset($item->$name) ? array_pluck($item->$name, 'id') : $formFieldsValue) !!}
    })
@endpush
@endunless

@if($addNew && isset($item->$name))
@push('modalAttributes')
    <a17-modal-add ref="{{ $name }}Modal" :form-create="'{{ $storeUrl }}'">
        {{-- fieldsInModal will manage fields separately --}}
        {{-- permalink and translateTitle should not be defined here --}}
        @partialView(($moduleName ?? null), 'create', ['renderForModal' => true, 'fieldsInModal' => true, 'permalink' => false, 'translateTitle' => false])
    </a17-modal-add>
@endpush
@endif
