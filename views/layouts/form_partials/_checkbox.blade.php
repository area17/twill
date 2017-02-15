@php
    if (isset($repeater) && $repeater) {
        $field = $moduleName . '[' . $repeaterIndex . '][' . $field . ']';
    }
@endphp


<div class="input boolean">
    <label class="boolean"> {!! Form::checkbox($field, 1) !!}&nbsp;{{ $field_name }}</label>
</div>
