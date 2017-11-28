<a17-datepicker
    name="{{ $name }}"
    label="{{ $label }}"
    place-holder="{{ $placeholder or $label }}"
    @if ($withTime ?? true) enable-time @endif
    @if ($allowInput ?? true) allow-input @endif
    @if ($allowClear ?? true) clear @endif
    @if (isset($minDate)) min-date="{{ $minDate }}" @endif
    @if (isset($maxDate)) max-date="{{ $maxDate }}" @endif
    in-store="date"
></a17-datepicker>

@push('fieldsStore')
    @if (isset($item->$name))
        window.STORE.form.fields.push({
            name: '{{ $name }}',
            value: '{{ $item->$name }}'
        })
    @endif
@endpush
