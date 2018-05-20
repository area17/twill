@php
    $value = $value ?? false;
@endphp

<a17-hiddenfield
    @include('twill::partials.form.utils._field_name')
    @if ($value) :initial-value="'{{ $value }}'" @endif
    :has-default-store="true"
    in-store="value"
></a17-hiddenfield>

@unless($renderForBlocks || $renderForModal)
@push('vuexStore')

@if(isset($item->$name))
    window.STORE.form.fields.push({
        name: '{{ $name }}',
        value: {!! json_encode($item->$name) !!}
    })
@endif

@endpush
@endunless
