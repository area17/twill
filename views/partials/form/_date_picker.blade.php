@php
    $withTime = $withTime ?? true;
    $allowInput = $allowInput ?? false;
    $allowClear = $allowClear ?? false;
    $note = $note ?? false;
    $inModal = $fieldsInModal ?? false;
@endphp

<a17-datepicker
    label="{{ $label }}"
    @include('twill::partials.form.utils._field_name')
    place-holder="{{ $placeholder ?? $label }}"
    @if ($withTime) enable-time @endif
    @if ($allowInput) allow-input @endif
    @if ($allowClear) clear @endif
    @if (isset($minDate)) min-date="{{ $minDate }}" @endif
    @if (isset($maxDate)) max-date="{{ $maxDate }}" @endif
    @if ($note ?? false) note="{{ $note }}" @endif
    @if ($required ?? false) :required="true" @endif
    @if ($inModal) :in-modal="true" @endif
    in-store="date"
></a17-datepicker>

@unless($renderForBlocks || $renderForModal || !isset($item->$name))
@push('vuexStore')
    window.STORE.form.fields.push({
        name: '{{ $name }}',
        value: {!! json_encode(e($item->$name)) !!}
    })
@endpush
@endunless
