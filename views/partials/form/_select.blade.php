@if ($unpack ?? false)
    <a17-inputframe>
        <a17-singleselect
            label="{{ $label }}"
            name="{{ $name }}"
            :options="{{ json_encode($options) }}"
            in-store="value"
        ></a17-singleselect>
    </a17-inputframe>
@else
    <a17-vselect
        label="{{ $label }}"
        name="{{ $name }}"
        :options="{{ json_encode($options) }}"
        @if ($emptyText ?? false) empty-text="{{ $emptyText }}" @endif
        @if ($placeholder ?? false) placeholder="{{ $placeholder }}" @endif
        size="large"
        in-store="inputValue"
    ></a17-vselect>
@endif

@push('fieldsStore')
    @if (isset($item->$name))
        window.STORE.form.fields.push({
            name: '{{ $name }}',
            value: @if(is_numeric($item->$name)) {{ $item->$name }} @else '{{ $item->$name }}' @endif
        })
    @endif
@endpush
