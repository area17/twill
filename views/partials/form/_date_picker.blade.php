@unless(\A17\Twill\TwillServiceProvider::supportsBladeComponents())
    @php
        $withTime = $withTime ?? true;
        $allowInput = $allowInput ?? false;
        $allowClear = $allowClear ?? false;
        $note = $note ?? false;
        $inModal = $fieldsInModal ?? false;
        $timeOnly = $timeOnly ?? false;
    @endphp
@endunless

<a17-datepicker
    label="{{ $label }}"
    @include('twill::partials.form.utils._field_name')
    place-holder="{{ $placeholder ?? $label }}"
    @if ($withTime) enable-time @endif
    @if ($timeOnly) no-calendar @endif
    @if ($allowInput) allow-input @endif
    @if ($allowClear) clear @endif
    @if (isset($minDate)) min-date="{{ $minDate }}" @endif
    @if (isset($maxDate)) max-date="{{ $maxDate }}" @endif
    @if ($note ?? false) note="{{ $note }}" @endif
    @if ($required ?? false) :required="true" @endif
    @if ($inModal) :in-modal="true" @endif
    @if (isset($time24Hr)) time_24hr="{{ $time24Hr ? 'true' : 'false' }}" @endif
    @if (isset($altFormat)) alt-format="{{ $altFormat }}" @endif
    @if (isset($hourIncrement)) :hour-increment="{{ $hourIncrement }}" @endif
    @if (isset($minuteIncrement)) :minute-increment="{{ $minuteIncrement }}" @endif
    in-store="date"
></a17-datepicker>

@unless($renderForBlocks || $renderForModal || (!isset($item->$name) && null == $formFieldsValue = getFormFieldsValue($form_fields, $name)))
@push('vuexStore')
    window['{{ config('twill.js_namespace') }}'].STORE.form.fields.push({
        name: '{{ $name }}',
        value: {!! json_encode(e($item->$name ?? $formFieldsValue)) !!}
    })
@endpush
@endunless
