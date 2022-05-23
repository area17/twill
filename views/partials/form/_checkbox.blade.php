<a17-singlecheckbox
    {!! $formFieldName() !!}
    label="{{ $label ?? '' }}"
    :initial-value="{{ $default ? 'true' : 'false' }}"
    @if ($note) note='{{ $note }}' @endif
    @if ($disabled) disabled @endif
    @if ($border) :border="true" @endif
    @if ($requireConfirmation) :require-confirmation="true" @endif
    @if ($confirmMessageText) confirm-message-text="{{ $confirmMessageText }}" @endif
    @if ($confirmTitleText) confirm-title-text="{{ $confirmTitleText }}" @endif
    :has-default-store="true"
    in-store="currentValue"
></a17-singlecheckbox>

@unless($renderForBlocks || $renderForModal || (!isset($item->$name) && null === $formFieldsValue = getFormFieldsValue($form_fields, $name)))
    @push('vuexStore')
        window['{{ config('twill.js_namespace') }}'].STORE.form.fields.push({
        name: '{{ $name }}',
        value:
        @if((isset($item) && $item->$name) || ($formFieldsValue ?? false))
            true
        @else
            false
        @endif
        })
    @endpush
@endunless
