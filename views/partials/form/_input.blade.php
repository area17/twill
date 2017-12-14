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
@endphp

@if($translated)
    <a17-locale
        type="a17-textfield"
        :attributes="{
            label: '{{ $label }}',
            @include('cms-toolkit::partials.form.utils._field_name', ['asAttributes' => true])
            type: '{{ $type }}',
            @if ($required) required: true, @endif
            @if ($note) note: '{{ $note }}', @endif
            @if ($placeholder) placeholder: '{{ $placeholder }}', @endif
            @if ($maxlength) maxlength: {{ $maxlength }}, @endif
            @if ($disabled) disabled: true, @endif
            @if ($readonly) readonly: true, @endif
            @if ($rows) rows: {{ $rows }}, @endif
            inStore: 'value'
        }"
    ></a17-locale>
@else
    <a17-textfield
        label="{{ $label }}"
        @include('cms-toolkit::partials.form.utils._field_name')
        type="{{ $type }}"
        @if ($required) required @endif
        @if ($note) note="{{ $note }}" @endif
        @if ($placeholder) placeholder="{{ $placeholder }}" @endif
        @if ($maxlength) :maxlength="{{ $maxlength }}" @endif
        @if ($disabled) disabled @endif
        @if ($readonly) readonly @endif
        @if ($rows) :rows="{{ $rows }}" @endif
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
@elseif(isset($item->$name))
    window.STORE.form.fields.push({
        name: '{{ $name }}',
        value: {!! json_encode($item->$name) !!}
    })
@endif

@endpush
@endunless
