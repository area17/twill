<a17-inputframe label="{{ $label }}" name="browsers.{{ $name }}" note="{{ $fieldNote }}">
    <a17-browserfield
        {!! $formFieldName() !!}
        item-label="{{ $itemLabel }}"
        :max="{{ $max }}"
        :wide="{{ json_encode($wide) }}"
        endpoint="{{ $endpoint }}"
        :endpoints="{{ json_encode($endpoints) }}"
        modal-title="{{ twillTrans('twill::lang.fields.browser.attach') . ' ' . strtolower($label) }}"
        :draggable="{{ json_encode($sortable) }}"
        browser-note="{{ $browserNote }}"
        @if($buttonOnTop) :button-on-top="true" @endif
        @if($disabled) disabled @endif
        @if($renderForBlocks && $connectedBrowserField) :connected-browser-field="fieldName('{{ $connectedBrowserField }}')"
        @elseif($connectedBrowserField) connected-browser-field="{{ $connectedBrowserField }}"
        @endif
    >{{ $note }}</a17-browserfield>
</a17-inputframe>

@unless($renderForBlocks)
    @push('vuexStore')
        @if (isset($form_fields['browsers']) && isset($form_fields['browsers'][$name]))
            window['{{ config('twill.js_namespace') }}'].STORE.browser.selected["{{ $name }}"] = {!! json_encode($form_fields['browsers'][$name]) !!}
        @endif
    @endpush
@endunless
