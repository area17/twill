<a17-inputframe>
    <a17-multiselect
        :options="{{ json_encode($options) }}"
        label="{{ $label }}"
        name="{{ $name }}"
        @if ($min ?? false) :min="{{ $min }}" @endif
        @if ($max ?? false) :max="{{ $max }}" @endif
        in-store="currentValue"
    ></a17-multiselect>
</a17-inputframe>

@push('fieldsStore')
    @if (isset($item->$name))
        window.STORE.form.fields.push({
            name: '{{ $name }}',
            value: {!! json_encode(array_pluck($item->$name, 'id')) !!}
        })
    @endif
@endpush
