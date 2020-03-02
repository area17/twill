@php
    $label = $label ?? twillTrans('twill::lang.fields.block-editor.add-content');
@endphp

@unless ($withoutSeparator ?? false)
    <hr/>
@endif
<a17-content title="{{ $label }}"></a17-content>

@php
    if (isset($blocks)) {
        $allowedBlocks = collect($blocks)->mapWithKeys(function ($block) {
            return [$block => config('twill.block_editor.blocks.' . $block)];
        })->filter()->toArray();
    } elseif (isset($group)) {
        $blocks = config('twill.block_editor.blocks');

        $allowedBlocks = array_filter($blocks, function ($block) use ($group) {
            return isset($block['group']) && $block['group'] === $group;
        });
    } else {
        $allowedBlocks = config('twill.block_editor.blocks');
    }
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
