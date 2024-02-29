@unless($withoutSeparator)
    <hr />
@endunless

<a17-blocks title="{{ $label }}"
    availability-id="{{ $availabilityId }}"
    @if ($renderForBlocks) :editor-name="nestedEditorName('{{ $name }}')" @else editor-name="{{ $name }}" @endif

    trigger="{{ $trigger }}" :is-settings="{{ $isSettings ? 'true' : 'false' }}">
</a17-blocks>

@push('vuexStore')
    window['{{ config('twill.js_namespace') }}'].STORE.form.availableBlocks['{{ $availabilityId }}'] =
    {!! json_encode(array_values($allowedBlocks)) !!}
    window['{{ config('twill.js_namespace') }}'].STORE.form.editorNames.push({!! json_encode($editorName) !!})
@endpush

@pushOnce('vuexStore', 'form:block_editor')
    @include('twill::partials.form.utils._block_editor_store')
@endPushOnce
