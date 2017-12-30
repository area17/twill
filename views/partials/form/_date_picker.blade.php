<a17-datepicker
    label="{{ $label }}"
    @include('cms-toolkit::partials.form.utils._field_name')
    place-holder="{{ $placeholder or $label }}"
    @if ($withTime ?? true) enable-time @endif
    @if ($allowInput ?? true) allow-input @endif
    @if ($allowClear ?? true) clear @endif
    @if (isset($minDate)) min-date="{{ $minDate }}" @endif
    @if (isset($maxDate)) max-date="{{ $maxDate }}" @endif
    @if ($note ?? false) note="{{ $note }}" @endif
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
