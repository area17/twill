@php
    $value_field = $value_field ?? null;
    if (isset($repeater) && $repeater) {
        $field = $moduleName . '[' . $repeaterIndex . '][' . $field . ']';
    }
@endphp

{!! Form::hidden($field, $value_field) !!}
