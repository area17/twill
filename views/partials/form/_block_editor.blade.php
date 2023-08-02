@unless($withoutSeparator)
    <hr />
@endunless

<a17-blocks title="{{ $label }}"
    @if ($renderForBlocks) :editor-name="nestedEditorName('{{ $name }}')" @else editor-name="{{ $name }}" @endif
    trigger="{{ $trigger }}" :is-settings="{{ (bool) $isSettings ? 'true' : 'false' }}">
</a17-blocks>

@push('vuexStore')
    window['{{ config('twill.js_namespace') }}'].STORE.form.availableBlocks['{{ $name }}'] =
    {!! json_encode(array_values($allowedBlocks)) !!}
    window['{{ config('twill.js_namespace') }}'].STORE.form.editorNames.push({!! json_encode($editorName) !!})
@endpush

@pushonce('vuexStore:block_editor')
    @include('twill::partials.form.utils._block_editor_store')
@endpushonce
