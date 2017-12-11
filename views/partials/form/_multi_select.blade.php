<a17-inputframe>
    <a17-multiselect
        :options="{{ json_encode($options) }}"
        label="{{ $label }}"
        @if ($renderForBlocks) :name="fieldName('{{ $name }}')" @else name="{{ $name }}" @endif
        @if ($min ?? false) :min="{{ $min }}" @endif
        @if ($max ?? false) :max="{{ $max }}" @endif
        in-store="currentValue"
    ></a17-multiselect>
</a17-inputframe>

@unless($renderForBlocks || $renderForModal)
@push('vuexStore')
    @if (isset($item->$name))
        window.STORE.form.fields.push({
            name: '{{ $name }}',
            value: {!! json_encode(array_pluck($item->$name, 'id')) !!}
        })
    @endif
@endpush
@endunless
