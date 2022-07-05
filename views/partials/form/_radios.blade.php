<a17-singleselect
    label="{{ $label }}"
    {!! $formFieldName() !!}
    :options="{{ json_encode($options) }}"
    @if ($default) selected="{{ $default }}" @endif
    :grid="false"
    :columns="{{ $columns }}"
    :disabled="{{$disabled ? 'true' : 'false'}}"
    @if ($inline) :inline="true" @endif
    @if ($border) :border="true" @endif
    @if ($required) :required="true" @endif
    @if ($inModal) :in-modal="true" @endif
    @if ($addNew) add-new='{{ $storeUrl }}' @elseif ($note) note='{{ $note }}' @endif
    @if ($confirmMessageText) confirm-message-text="{{ $confirmMessageText }}"  @endif
    @if ($confirmTitleText) confirm-title-text="{{ $confirmTitleText }}"  @endif
    :has-default-store="true"
    @if ($requireConfirmation) :require-confirmation="true" @endif
    in-store="value"
>
    @if($addNew)
        <div slot="addModal">
            @partialView(($formModuleName ?? null), 'create', ['renderForModal' => true, 'fieldsInModal' => true])
        </div>
    @endif
</a17-singleselect>

@unless($renderForBlocks || $renderForModal || (!isset($item->$name) && null == $formFieldsValue = getFormFieldsValue($form_fields, $name, $default)))
@push('vuexStore')
    @include('twill::partials.form.utils._selector_input_store')
@endpush
@endunless
