@if(isset($livewire) && $livewire)
    <x-twill::field-wrapper :label="$label" :name="$name">
        <x-twill::list>
            @foreach ($selected_items as $item)
                <x-twill::list-item-basic
                    :title="$item['name']"
                    :description="$item['endpointType']"
                    :link-text="__('Edit')"
                    :link="$item['edit']"
                    on-delete="deleteBrowserItem('{{$name}}', {{$item['id']}})"
                />
            @endforeach
        </x-twill::list>
        <x-twill::button type="secondary"
                         class="flex-1 mt-6 self-start"
                         wire:click="$emit('openModal', 'livewire-browser-picker', {{ $modal_data }})">@lang('Add :type', ['type'=> $label])
        </x-twill::button>
    </x-twill::field-wrapper>
@else
    <a17-inputframe label="{{ $label }}" name="browsers.{{ $name }}" note="{{ $fieldNote }}">
        <a17-browserfield
            @include('twill::partials.form.utils._field_name')
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
@endif
