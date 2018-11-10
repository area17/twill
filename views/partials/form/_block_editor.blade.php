@php
    $availableBlocks = isset($blocks) ? collect($blocks)->map(function ($blocks) {
        return config('twill.block_editor.blocks.' . $blocks);
    })->filter()->toArray() : config('twill.block_editor.blocks');

    $title = $title ?? $section ?? 'content';
    $connectorKey = $section ?? 'default';
@endphp

@unless ($withoutSeparator ?? false)
    <hr/>
@endif
<a17-blocks title="Add {{$title}}" section="{{$connectorKey}}"></a17-blocks>

@push('vuexStore')
    {{--window.STORE.form.content = {!! json_encode(array_values($availableBlocks)) !!}--}}

    window.STORE.form.blocks.available['{{ $connectorKey }}'] = {!! json_encode(array_values($availableBlocks)) !!}
    window.STORE.form.blocks.used['{{ $connectorKey }}'] = {!! json_encode($form_fields['blocks'] ?? []) !!}

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
