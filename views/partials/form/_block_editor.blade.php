@php
    $trigger = $trigger ?? twillTrans('twill::lang.fields.block-editor.add-content');
    $name = $name ?? 'default';
    $label = $label ?? Str::title($name);
    $allowedBlocks = generate_list_of_available_blocks($blocks ?? null, $group ?? $groups ?? null);

    $editorName = [
        'label' => $label,
        'value' => $name,
    ];
@endphp

@unless($withoutSeparator ?? false)
<hr/>
@endunless
<a17-blocks title="{{ $label }}" trigger="{{ $trigger }}" editor-name="{{ $name }}"></a17-blocks>

@push('vuexStore')
    window['{{ config('twill.js_namespace') }}'].STORE.form.availableBlocks['{{ $name }}'] = {!! json_encode(array_values($allowedBlocks)) !!}
    window['{{ config('twill.js_namespace') }}'].STORE.form.editorNames.push({!! json_encode($editorName) !!})
@endpush

@pushonce('vuexStore:block_editor')
    @include('twill::partials.form.utils._block_editor_store')
@endpushonce
