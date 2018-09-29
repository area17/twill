<a17-repeater
    type="{{ $type }}"
    @if ($renderForBlocks) :name="repeaterName('{{ $type }}')" @else name="{{ $type }}" @endif
></a17-repeater>

@push('vuexStore')
    @foreach($form_fields['repeaterFields'][$type] ?? [] as $field)
        window.STORE.form.fields.push({!! json_encode($field) !!})
    @endforeach

    @foreach($form_fields['repeaterMedias'][$type] ?? [] as $name => $medias)
        window.STORE.medias.selected["{{ $name }}"] = {!! json_encode($medias) !!}
    @endforeach

    @foreach($form_fields['repeaterFiles'][$type] ?? [] as $name => $files)
        window.STORE.medias.selected["{{ $name }}"] = {!! json_encode($files) !!}
    @endforeach

    @foreach($form_fields['repeaterBrowsers'][$type] ?? [] as $name => $fields)
        window.STORE.browser.selected["{{ $name }}"] = {!! json_encode($fields) !!}
    @endforeach
@endpush
