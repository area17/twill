@php
    $label = $label ?? 'Tags';
    $note = $note ?? false;
@endphp
<a17-vselect
    label="{{ $label }}"
    name="tags"
    @if ($note) note="{{ $note }}" @endif
    :multiple="true"
    :searchable="true"
    :taggable="true"
    :push-tags="true"
    endpoint="{{ moduleRoute($moduleName, $routePrefix, 'tags') }}"
    in-store="inputValue"
></a17-vselect>

@unless($renderForBlocks || $renderForModal || $item->tags->count() === 0)
@push('vuexStore')
    window['{{ config('twill.js_namespace') }}'].STORE.form.fields.push({
        name: 'tags',
        value: {!! json_encode($item->tags->map(function ($tag) { return $tag->name; })->toArray()) !!}
    })
@endpush
@endunless
