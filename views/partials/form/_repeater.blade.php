<a17-repeater
    type="{{ $type }}"
    @if ($renderForBlocks) :name="repeaterName('{{ $type }}')" @else name="{{ $type }}" @endif
></a17-repeater>

@push('vuexStore')
    @foreach($form_fields['repeaterFields'][$type] ?? [] as $field)
        window.STORE.form.fields.push({!! json_encode($field) !!})
    @endforeach

    @foreach($form_fields['repeaterMedias'][$type] ?? [] as $name => $medias)
        window.STORE.medias.selected["{{ $name }}"] = {!! json_encode($form_fields['repeaterMedias'][$type][$name]) !!}
    @endforeach
@endpush
