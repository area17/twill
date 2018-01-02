<a17-checkboxgroup
    @include('cms-toolkit::partials.form.utils._field_name')
    :options="[ { value: '{{ $value or 1 }}', label: '{{ $label }}' } ]"
    in-store="currentValue"
></a17-checkboxgroup>

@unless($renderForBlocks || $renderForModal)
@push('vuexStore')
    window.STORE.form.fields.push({
        name: '{{ $name }}',
        value: @if($item->$name) [1] @else [] @endif
    })
@endpush
@endunless
