@php
    $note = $note ?? false;
    $inline = $inline ?? false;
@endphp

<a17-checkboxgroup
    @include('cms-toolkit::partials.form.utils._field_name')
    :options="[ { value: '{{ $value or 1 }}', label: '{{ $label }}' } ]"
    :inline='{{ $inline ? 'true' : 'false' }}'
    @if ($note) note='{{ $note }}' @endif
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
