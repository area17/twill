<a17-hiddenfield
    {!! $formFieldName() !!}
    @if ($value) :initial-value="'{{ $value }}'" @endif
    :has-default-store="true"
    in-store="value"
></a17-hiddenfield>

@unless($renderForBlocks || $renderForModal)
@push('vuexStore')

@if(isset($item->$name))
    window['{{ config('twill.js_namespace') }}'].STORE.form.fields.push({
        name: '{{ $name }}',
        value: {!! json_encode($item->$name) !!}
    })
@endif

@endpush
@endunless
