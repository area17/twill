<hr/>

<a17-content title="Add content"></a17-content>

@php
    $availableBlocks = isset($blocks) ? collect($blocks)->map(function ($block) {
        return config('cms-toolkit.block_editor.blocks.' . $block);
    })->filter()->toArray() : config('cms-toolkit.block_editor.blocks');
@endphp

@push('vuexStore')
    window.STORE.form.content = {!! json_encode(array_values($availableBlocks)) !!}
    window.STORE.form.blocks = {!! json_encode($form_fields['blocks'] ?? []) !!}

    @foreach($form_fields['blocksFields'] ?? [] as $field)
        window.STORE.form.fields.push({!! json_encode($field) !!})
    @endforeach

    @foreach($form_fields['blocksMedias'] ?? [] as $name => $medias)
        window.STORE.medias.selected["{{ $name }}"] = {!! json_encode($form_fields['blocksMedias'][$name]) !!}
    @endforeach
@endpush
