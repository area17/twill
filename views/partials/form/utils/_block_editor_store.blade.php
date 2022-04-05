window['{{ config('twill.js_namespace') }}'].STORE.form.blocks = {!! json_encode($form_fields['blocks']) !!}

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
