<a17-colorfield
    label="{{ $label }}"
    @include('twill::partials.form.utils._field_name')
    in-store="value"
></a17-colorfield>

@unless($renderForBlocks || $renderForModal || !isset($item->$name))
@push('vuexStore')
    window.STORE.form.fields.push({
        name: '{{ $name }}',
        value: {!! json_encode($item->$name) !!}
    })
@endpush
@endunless
