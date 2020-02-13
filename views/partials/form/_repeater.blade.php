@php
    $name = $name ?? $type;
@endphp

<a17-repeater
    type="{{ $type }}"
    @if ($renderForBlocks) :name="repeaterName('{{ $name }}')" @else name="{{ $name }}" @endif
></a17-repeater>

@push('vuexStore')
    @foreach($form_fields['repeaterFields'][$name] ?? [] as $field)
        window.STORE.form.fields.push({!! json_encode($field) !!})
    @endforeach

    @foreach($form_fields['repeaterMedias'][$name] ?? [] as $repeater => $medias)
        window.STORE.medias.selected["{{ $repeater }}"] = {!! json_encode($medias) !!}
    @endforeach

    @foreach($form_fields['repeaterFiles'][$name] ?? [] as $repeater => $files)
        window.STORE.medias.selected["{{ $repeater }}"] = {!! json_encode($files) !!}
    @endforeach

    @foreach($form_fields['repeaterBrowsers'][$name] ?? [] as $repeater => $fields)
        window.STORE.browser.selected["{{ $repeater }}"] = {!! json_encode($fields) !!}
    @endforeach
@endpush
