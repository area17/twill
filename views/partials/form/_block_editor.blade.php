<hr/>

<a17-content title="Add Content"></a17-content>

@php
    $availableBlocks = isset($blocks) ? collect($blocks)->map(function ($block) {
        return config('cms-toolkit.block-editor.blocks.' . $block);
    })->filter()->toArray() : config('cms-toolkit.block-editor.blocks');
@endphp

@push('fieldsStore')
    window.STORE.form.content = {!! json_encode(array_values($availableBlocks)) !!}
@endpush
