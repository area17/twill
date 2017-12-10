@php
    $type = $type ?? 'text';
@endphp

@if($translated ?? false)
    <a17-locale
        type="a17-textfield"
        :attributes="{
            label: '{{ $label }}',
            @if ($renderForBlocks) name: fieldName('{{ $name }}'), @else name: '{{ $name }}', @endif
            type: '{{ $type }}',
            @if ($required ?? false) required: true, @endif
            @if ($note ?? false) note: '{{ $note }}', @endif
            @if ($placeholder ?? false) placeholder: '{{ $placeholder }}', @endif
            @if ($maxlength ?? false) maxlength: {{ $maxlength }}, @endif
            @if ($disabled ?? false) disabled: true, @endif
            @if ($readonly ?? false) readonly: true, @endif
            @if ($rows ?? false) rows: {{ $rows }}, @endif
            inStore: 'value'
        }"
    ></a17-locale>
@else
    <a17-textfield
        label="{{ $label }}"
        @if ($renderForBlocks) :name="fieldName('{{ $name }}')" @else name="{{ $name }}" @endif
        type="{{ $type }}"
        @if ($required ?? false) required @endif
        @if ($note ?? false) note="{{ $note }}" @endif
        @if ($placeholder ?? false) placeholder="{{ $placeholder }}" @endif
        @if ($maxlength ?? false) :maxlength="{{ $maxlength }}" @endif
        @if ($disabled ?? false) disabled @endif
        @if ($readonly ?? false) readonly @endif
        @if ($rows ?? false) :rows="{{ $rows }}" @endif
        in-store="value"
    ></a17-textfield>
@endif

@unless($renderForBlocks || $renderForModal)
    @push('fieldsStore')
        @if($translated ?? false && isset($form_fields['translations']) && isset($form_fields['translations'][$name]))
            window.STORE.form.fields.push({
                name: '{{ $name }}',
                value: {
                    @foreach(getLocales() as $locale)
                        '{{ $locale }}': "{!! $form_fields['translations'][$name][$locale] ?? '' !!}"@unless($loop->last),@endif
                    @endforeach
                }
            })
        @elseif(isset($item->$name))
            window.STORE.form.fields.push({
                name: '{{ $name }}',
                value: "{{ $item->$name }}"
            })
        @endif
    @endpush
@endunless
