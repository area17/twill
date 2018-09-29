@php
    $type = $type ?? 'text';
    $translated = $translated ?? false;
    $required = $required ?? false;
    $note = $note ?? false;
    $placeholder = $placeholder ?? false;
    $maxlength = $maxlength ?? false;
    $disabled = $disabled ?? false;
    $readonly = $readonly ?? false;
    $rows = $rows ?? false;
    $ref = $ref ?? false;
    $onChange = $onChange ?? false;
    $onChangeAttribute = $onChangeAttribute ?? false;
    $onChangeFullAttribute = $onChangeAttribute ? "('".$onChangeAttribute."', ...arguments)" : "";
    $prefix = $prefix ?? false;
    $inModal = $fieldsInModal ?? false;
@endphp

@if($translated)
    <a17-locale
        type="a17-textfield"
        :attributes="{
            label: '{{ $label }}',
            @include('twill::partials.form.utils._field_name', ['asAttributes' => true])
            type: '{{ $type }}',
            @if ($required) required: true, @endif
            @if ($note) note: '{{ $note }}', @endif
            @if ($placeholder) placeholder: '{{ $placeholder }}', @endif
            @if ($maxlength) maxlength: {{ $maxlength }}, @endif
            @if ($disabled) disabled: true, @endif
            @if ($readonly) readonly: true, @endif
            @if ($rows) rows: {{ $rows }}, @endif
            @if ($prefix) prefix: '{{ $prefix }}', @endif
            @if ($inModal) inModal: true, @endif
            inStore: 'value'
        }"
        @if ($ref) ref="{{ $ref }}" @endif
        @if ($onChange) v-on:change="{{ $onChange }}{{ $onChangeFullAttribute }}" @endif
    ></a17-locale>
@else
    <a17-textfield
        label="{{ $label }}"
        @include('twill::partials.form.utils._field_name')
        type="{{ $type }}"
        @if ($required) :required="true" @endif
        @if ($note) note="{{ $note }}" @endif
        @if ($placeholder) placeholder="{{ $placeholder }}" @endif
        @if ($maxlength) :maxlength="{{ $maxlength }}" @endif
        @if ($disabled) disabled @endif
        @if ($readonly) readonly @endif
        @if ($rows) :rows="{{ $rows }}" @endif
        @if ($ref) ref="{{ $ref }}" @endif
        @if ($onChange) v-on:change="{{ $onChange }}{{ $onChangeFullAttribute }}" @endif
        @if ($prefix) prefix="{{ $prefix }}" @endif
        @if ($inModal) :in-modal="true" @endif
        in-store="value"
    ></a17-textfield>
@endif

@unless($renderForBlocks || $renderForModal)
@push('vuexStore')

@if($translated && isset($form_fields['translations']) && isset($form_fields['translations'][$name]))
    window.STORE.form.fields.push({
        name: '{{ $name }}',
        value: {
            @foreach(getLocales() as $locale)
                '{{ $locale }}': {!! json_encode(
                    $form_fields['translations'][$name][$locale] ?? '')
                !!}@unless($loop->last),@endunless
            @endforeach
        }
    })
@elseif(isset($item->$name) || null !== $formFieldsValue = getFormFieldsValue($form_fields, $name))
    window.STORE.form.fields.push({
        name: '{{ $name }}',
        value: {!! json_encode(isset($item->$name) ? $item->$name : $formFieldsValue) !!}
    })
@endif

@endpush
@endunless
