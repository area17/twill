<a17-inputframe>
    <a17-checkboxgroup
        name="{{ $name }}"
        :options="[ { value: '{{ $value or 1 }}', label: '{{ $label }}' } ]"
        in-store="currentValue"
    ></a17-checkboxgroup>
</a17-inputframe>

@push('fieldsStore')
    window.STORE.form.fields.push({
        name: '{{ $name }}',
        value: @if($item->$name) [1] @else [] @endif
    })
@endpush
