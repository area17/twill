@php
    $label = $label ?? twillTrans('twill::lang.fields.block-editor.add-content');
@endphp

@unless ($withoutSeparator ?? false)
    <hr/>
@endif
<a17-content title="{{ $label }}"></a17-content>

@php
    $allowedBlocks = generate_list_of_available_blocks($blocks ?? null, $group ?? $groups ?? null);
@endphp

@push('vuexStore')
    window['{{ config('twill.js_namespace') }}'].STORE.form.content = {!! json_encode(array_values($allowedBlocks)) !!}
    window['{{ config('twill.js_namespace') }}'].STORE.form.blocks = {!! json_encode($form_fields['blocks'] ?? []) !!}

    @foreach($form_fields['blocksFields'] ?? [] as $field)
        window['{{ config('twill.js_namespace') }}'].STORE.form.fields.push({!! json_encode($field) !!})
    @endforeach

    @foreach($form_fields['blocksMedias'] ?? [] as $name => $medias)
        window['{{ config('twill.js_namespace') }}'].STORE.medias.selected["{{ $name }}"] = {!! json_encode($medias) !!}
    @endforeach

    @foreach($form_fields['blocksFiles'] ?? [] as $name => $files)
        window['{{ config('twill.js_namespace') }}'].STORE.medias.selected["{{ $name }}"] = {!! json_encode($files) !!}
    @endforeach

    @foreach($form_fields['blocksBrowsers'] ?? [] as $name => $browser)
        window['{{ config('twill.js_namespace') }}'].STORE.browser.selected["{{ $name }}"] = {!! json_encode($browser) !!}
    @endforeach
@endpush
