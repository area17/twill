@php
    $required = $required ?? "";
    $value_field = $value_field ?? null;
    $options = [];

    if (isset($disabled)) {
        $options['disabled'] = 'disabled';
    }

    if (isset($placeholder)) {
        $options['placeholder'] = $placeholder;
    }

    if (isset($maxlength)) {
        $options['maxlength'] = $maxlength;
    }

    if (isset($readonly)) {
        $options['readonly'] = $readonly;
    }

    if (isset($field_wrapper)) {
        $fullField = $field_wrapper . '[' . $field . ']';
        $fieldValue = $form_fields[$fullField] ?? (isset($item) && $item->$field_wrapper ? $item->$field_wrapper->$field : null);
    } else {
        $fullField = $field;
        $fieldValue = $form_fields[$fullField] ?? null;
    }

    if (isset($repeater) && $repeater) {
        $fullField = $moduleName . '[' . $repeaterIndex . '][' . $field . ']';
        $fieldValue = $form_fields[$fullField] ?? null;
    }
@endphp

<div class="input string {{ $required }} {{ $fullField }}">
    <label class="string {{ $required }} control-label" for="{{ $fullField }}">
        {!! $field_name !!} {!! !empty($required) ? '<abbr title="required">*</abbr>' : '' !!}
        {!! isset($hint) ? '<span class="hint">'.$hint.'</span>' : '' !!}
    </label>
    {!! Form::text($fullField, $fieldValue, ['class' => "string {$required}", 'id' => $fullField] + $options) !!}
</div>
