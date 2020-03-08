@php
    $label = $label ?? 'Add content'
@endphp

@unless ($withoutSeparator ?? false)
    <hr/>
@endif
<a17-content title="{{ $label }}"></a17-content>

@php
    $allowedBlocks = generate_list_of_available_blocks($blocks ?? null, $groups ?? null);
@endphp

@push('vuexStore')
    window.STORE.form.content = {!! json_encode(array_values($allowedBlocks)) !!}
    window.STORE.form.blocks = {!! json_encode($form_fields['blocks'] ?? []) !!}

    @foreach($form_fields['blocksFields'] ?? [] as $field)
        window.STORE.form.fields.push({!! json_encode($field) !!})
    @endforeach

    @foreach($form_fields['blocksMedias'] ?? [] as $name => $medias)
        window.STORE.medias.selected["{{ $name }}"] = {!! json_encode($medias) !!}
    @endforeach

    @foreach($form_fields['blocksFiles'] ?? [] as $name => $files)
        window.STORE.medias.selected["{{ $name }}"] = {!! json_encode($files) !!}
    @endforeach

    @foreach($form_fields['blocksBrowsers'] ?? [] as $name => $browser)
        window.STORE.browser.selected["{{ $name }}"] = {!! json_encode($browser) !!}
    @endforeach
@endpush
