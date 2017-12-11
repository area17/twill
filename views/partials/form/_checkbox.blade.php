<a17-inputframe>
    <a17-checkboxgroup
        @if ($renderForBlocks) :name="fieldName('{{ $name }}')" @else name="{{ $name }}" @endif
        :options="[ { value: '{{ $value or 1 }}', label: '{{ $label }}' } ]"
        in-store="currentValue"
    ></a17-checkboxgroup>
</a17-inputframe>

@unless($renderForBlocks || $renderForModal)
@push('vuexStore')
    window.STORE.form.fields.push({
        name: '{{ $name }}',
        value: @if($item->$name) [1] @else [] @endif
    })
@endpush
@endunless
