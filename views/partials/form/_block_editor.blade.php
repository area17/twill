@php
    $trigger = $trigger ?? $label ?? twillTrans('twill::lang.fields.block-editor.add-content');
    $name = $name ?? 'default';
    $title = $title ?? Str::title($name);
    $allowedBlocks = generate_list_of_available_blocks($blocks ?? null, $group ?? $groups ?? null);

    $editorName = [
        'label' => $title,
        'value' => $name,
    ];
@endphp

@unless($withoutSeparator ?? false)
<hr/>
@endunless
<a17-blocks
    title="{{ $title }}"
    @if ($renderForBlocks) :editor-name="repeaterName('{{ $name }}')" @else editor-name="{{ $name }}" @endif
    trigger="{{ $trigger }}">
</a17-blocks>

@push('vuexStore')
    window['{{ config('twill.js_namespace') }}'].STORE.form.availableBlocks['{{ $name }}'] = {!! json_encode(array_values($allowedBlocks)) !!}
    window['{{ config('twill.js_namespace') }}'].STORE.form.editorNames.push({!! json_encode($editorName) !!})
@endpush

@pushonce('vuexStore:block_editor')
    @include('twill::partials.form.utils._block_editor_store')
@endpushonce
