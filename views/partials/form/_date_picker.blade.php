<a17-datepicker
    label="{{ $label }}"
    {!! $formFieldName() !!}
    place-holder="{{ $placeholder ?? $label }}"
    @if ($disabled) disabled @endif
    @if ($withTime) enable-time @endif
    @if ($timeOnly) no-calendar @endif
    @if ($allowInput) allow-input @endif
    @if ($allowClear) clear @endif
    @if (isset($minDate)) min-date="{{ $minDate }}" @endif
    @if (isset($maxDate)) max-date="{{ $maxDate }}" @endif
    @if ($note ?? false) note="{{ $note }}" @endif
    @if ($required ?? false) :required="true" @endif
    @if ($inModal) :in-modal="true" @endif
    @if (isset($time24Hr)) :time_24hr="{{ $time24Hr ? 'true' : 'false' }}" @endif
    @if (isset($altFormat)) alt-format="{{ $altFormat }}" @endif
    @if (isset($hourIncrement)) :hour-increment="{{ $hourIncrement }}" @endif
    @if (isset($minuteIncrement)) :minute-increment="{{ $minuteIncrement }}" @endif
    in-store="date"
></a17-datepicker>

@unless($renderForBlocks || $renderForModal || (!isset($item->$name) && is_null($formFieldsValue = getFormFieldsValue($form_fields, $name))))
@push('vuexStore')
    window['{{ config('twill.js_namespace') }}'].STORE.form.fields.push({
        name: '{{ $name }}',
        value: {!! json_encode(e($item->$name ?? $formFieldsValue)) !!}
    })
@endpush
@endunless
