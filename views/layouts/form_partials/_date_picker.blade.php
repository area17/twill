@php
    $disabled = isset($disabled) && $disabled == 'disabled' ? ['disabled' => 'disabled'] : [];
    $field_name = $field_name ?? $fieldname;
    $date_settings = $date_settings ?? 'default_date_settings';
    if (isset($repeater) && $repeater) {
        $fieldValue = $form_fields[$moduleName][$repeaterIndex][$field] ?? null;
        $field = $moduleName . '[' . $repeaterIndex . '][' . $field . ']';
    }
@endphp

<div class="input text">
    <label class="text control-label" for="{{ $field }}_var">
        {{ $field_name }} {!! !empty($required) ? '<abbr title="required">*</abbr>' : '' !!}
    </label>
    {!! Form::text($field, $fieldValue ?? null, [
        'class' => "string text",
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
