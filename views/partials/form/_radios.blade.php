@php
    $note = $note ?? false;
    $options = method_exists($options, 'map') ? $options->map(function($label, $value) {
        return [
            'value' => $value,
            'label' => $label
        ];
    })->values()->toArray() : $options;

    $required = $required ?? false;
    $default = $default ?? false;
    $inline = $inline ?? false;

    // do not use for now, but this will allow you to create a new option directly from the form
    $addNew = $addNew ?? false;
    $moduleName = $moduleName ?? null;
    $storeUrl = $storeUrl ?? '';
    $inModal = $fieldsInModal ?? false;
@endphp

<a17-singleselect
    label="{{ $label }}"
    @include('twill::partials.form.utils._field_name')
    :options="{{ json_encode($options) }}"
    @if ($default) selected="{{ $default }}" @endif
    :grid="false"
    @if ($inline) :inline="true" @endif
    @if ($required) :required="true" @endif
    @if ($inModal) :in-modal="true" @endif
    @if ($addNew) add-new='{{ $storeUrl }}' @elseif ($note) note='{{ $note }}' @endif
    :has-default-store="true"
    in-store="value"
>
    @if($addNew)
        <div slot="addModal">
            {{-- unset($note, $options, $required, $default, $inline, $addNew, $inModal); --}}
            @partialView(($moduleName ?? null), 'create', ['renderForModal' => true, 'fieldsInModal' => true])
        </div>
    @endif
</a17-singleselect>

@unless($renderForBlocks || $renderForModal || (!isset($item->$name) && null == $formFieldsValue = getFormFieldsValue($form_fields, $name)))
@push('vuexStore')
    @include('twill::partials.form.utils._selector_input_store')
@endpush
@endunless
