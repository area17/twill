@php
    $required = $required ?? "";
    $disabled = isset($disabled) && $disabled == 'disabled' ? ['disabled' => 'disabled'] : [];
    $field_name = $field_name ?? $fieldname;
    $date_settings = $date_settings ?? 'default_date_settings';
@endphp

<div class="input text {{ $required }}">
    <label class="text {{ $required }} control-label" for="{{ $field }}_var">
        {{ $field_name }} {!! !empty($required) ? '<abbr title="required">*</abbr>' : '' !!}
    </label>
    {!! Form::text($field, null, [
        'class' => "string text {$required}",
        'id'=> $field."_var",
        'data-behavior' => 'datetimepicker',
        'data-datetime-settings'=>"{$date_settings}"
    ] + $disabled) !!}

    <script>
        var default_date_settings = {
            lang:'en',
            format: 'm/d/Y H:i',
            datepicker: true,
            timepicker: true,
            dayOfWeekStart:1,
        }
    </script>
</div>
