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
    $inline = $inline ?? false;

    # Add new option
    $addNew = $addNew ?? false;
    $moduleName = $moduleName ?? null;
    $storeUrl = $storeUrl ?? '';
    $inModal = $fieldsInModal ?? false;
@endphp

<a17-singleselect
    label="{{ $label }}"
    @include('cms-toolkit::partials.form.utils._field_name')
    :options="{{ json_encode($options) }}"
    @if ($default) selected="{{ $default }}" @endif
    :grid="false"
    :inline='{{ $inline ? 'true' : 'false' }}'
    @if ($required) :required="true" @endif

    @if ($inModal) :in-modal="true" @endif
    @if ($addNew) add-new='{{ $name }}Modal'
    @elseif ($note) note='{{ $note }}'
    @endif

    :has-default-store="true"
    in-store="value"
></a17-singleselect>

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
