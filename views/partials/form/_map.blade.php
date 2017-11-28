<a17-locationfield
    label="{{ $label }}"
    name="{{ $name }}"
    in-store="value"
    @if ($showMap ?? true) show-map @else :show-map="false" @endif
    @if ($openMap ?? false) open-map @endif
></a17-locationfield>

@push('fieldsStore')
    @if (isset($item->$name))
        window.STORE.form.fields.push({
            name: '{{ $name }}',
            value: {
                'latlng': '{{ $item->$name }}',
                'address': ''
            }
        })
    @endif
@endpush
