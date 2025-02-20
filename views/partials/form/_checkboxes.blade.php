<a17-multiselect
    label="{{ $label }}"
    {!! $formFieldName() !!}
    :options="{{ json_encode($options) }}"
    :grid="false"
    :columns="{{ $columns }}"
    :inline='{{ $inline ? 'true' : 'false' }}'
    :border='{{ $border ? 'true' : 'false' }}'
    @if ($min ?? false) :min="{{ $min }}" @endif
    @if ($max ?? false) :max="{{ $max }}" @endif
    @if ($inModal) :in-modal="true" @endif
    @if ($addNew) add-new='{{ $storeUrl }}' @elseif ($note) note='{{ $note }}' @endif
    in-store="currentValue"
>
    @if($addNew)
        <div slot="addModal">
            @partialView(($formModuleName ?? null), 'create', ['renderForModal' => true, 'fieldsInModal' => true])
        </div>
    @endif
</a17-multiselect>

@unless($renderForBlocks || $renderForModal || (!isset($item->$name) && is_null($formFieldsValue = getFormFieldsValue($form_fields, $name))))
@push('vuexStore')
    window['{{ config('twill.js_namespace') }}'].STORE.form.fields.push({
        name: '{{ $name }}',
        value: {!! json_encode(isset($item) && isset($item->$name) ? (is_string($item->$name) ? json_decode($item->$name) : Arr::pluck($item->$name, 'id')) : $formFieldsValue) !!}
    })
@endpush
@endunless
