@php
    $required = $required ?? "";
    $disabled = isset($disabled) && $disabled == 'disabled' ? ['disabled' => 'disabled'] : [];
@endphp

<div class="input text {{ $required }}">
    <label class="text {{ $required }} control-label" for="{{ $field }}_var">
        {{ $fieldname }} {!! !empty($required) ? '<abbr title="required">*</abbr>' : '' !!}
    </label>
    {!! Form::text($field, null, [
        'class' => "string text {$required}",
        'id'=> $field."_var",
        'data-behavior' => 'datetimepicker',
        'data-datetime-settings'=>"date_settings"
    ] + $disabled) !!}

    <script>
        var date_settings = {
            lang:'en',
            format: 'm/d/Y H:i',
            datepicker: true,
            timepicker: true,
            dayOfWeekStart:1,
        }
    </script>
</div>
