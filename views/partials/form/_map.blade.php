<a17-locationfield
    label="{{ $label }}"
    @include('cms-toolkit::partials.form.utils._field_name')
    @if ($showMap ?? true) show-map @else :show-map="false" @endif
    @if ($openMap ?? false) open-map @endif
    in-store="value"
></a17-locationfield>

@unless($renderForBlocks || !isset($item->$name))
@push('vuexStore')
    window.STORE.form.fields.push({
        name: '{{ $name }}',
        value: {
            'latlng': {!! json_encode($item->$name) !!},
            'address': ''
        }
    })
@endpush
@endunless
