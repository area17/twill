@php
    $options = method_exists($options, 'map') ? $options->map(function($label, $value) {
        return [
            'value' => $value,
            'label' => $label
        ];
    })->values()->toArray() : $options;

    $note = $note ?? false;
    $inline = $inline ?? false;

    // do not use for now, but this will allow you to create a new option directly from the form
    $addNew = $addNew ?? false;
    $moduleName = $moduleName ?? null;
    $storeUrl = $storeUrl ?? '';
    $inModal = $fieldsInModal ?? false;
@endphp

<a17-multiselect
    label="{{ $label }}"
    @include('twill::partials.form.utils._field_name')
    :options="{{ json_encode($options) }}"
    :grid="false"
    :inline='{{ $inline ? 'true' : 'false' }}'
    @if ($min ?? false) :min="{{ $min }}" @endif
    @if ($max ?? false) :max="{{ $max }}" @endif
    @if ($inModal) :in-modal="true" @endif
    @if ($addNew) add-new='{{ $storeUrl }}' @elseif ($note) note='{{ $note }}' @endif
    in-store="currentValue"
>
    @if($addNew)
        <div slot="addModal">
            @partialView(($moduleName ?? null), 'create', ['renderForModal' => true, 'fieldsInModal' => true])
        </div>
    @endif
</a17-multiselect>

@unless($renderForBlocks || $renderForModal || (!isset($item->$name) && null == $formFieldsValue = getFormFieldsValue($form_fields, $name)))
@push('vuexStore')
    window.STORE.form.fields.push({
        name: '{{ $name }}',
        value: {!! json_encode(isset($item) && isset($item->$name) ? array_pluck($item->$name, 'id') : $formFieldsValue) !!}
    })
@endpush
@endunless
