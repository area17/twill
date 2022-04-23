@if(isset($livewire) && $livewire)
    Repeater
    @foreach ($repeaters[$type] as $index => $data)
        {!! $repeaterForm($index, $data) !!}
        <br />
    @endforeach
    <br />
    <button wire:click.prevent="addRepeater('{{$type}}')">Add new {{$type}}</button>
    <br/>
@else
    <a17-repeater
        type="{{ $type }}"
        @if ($max) :max="{{$max}}" @endif
        @if (!$reorder) :draggable="false" @endif
        @if ($renderForBlocks) :name="repeaterName('{{ $name }}')" @else name="{{ $name }}" @endif
        @if ($buttonAsLink) :button-as-link="true" @endif
    ></a17-repeater>

    @push('vuexStore')
        @foreach($form_fields['repeaterFields'][$name] ?? [] as $field)
            window['{{ config('twill.js_namespace') }}'].STORE.form.fields.push({!! json_encode($field) !!})
        @endforeach

        @foreach($form_fields['repeaterMedias'][$name] ?? [] as $repeater => $medias)
            window['{{ config('twill.js_namespace') }}'].STORE.medias.selected["{{ $repeater }}"] = {!! json_encode($medias) !!}
        @endforeach

        @foreach($form_fields['repeaterFiles'][$name] ?? [] as $repeater => $files)
            window['{{ config('twill.js_namespace') }}'].STORE.medias.selected["{{ $repeater }}"] = {!! json_encode($files) !!}
        @endforeach

        @foreach($form_fields['repeaterBrowsers'][$name] ?? [] as $repeater => $fields)
            window['{{ config('twill.js_namespace') }}'].STORE.browser.selected["{{ $repeater }}"] = {!! json_encode($fields) !!}
        @endforeach
    @endpush
@endif
