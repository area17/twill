@php
    if (isset($repeater) && $repeater) {
        $field = $moduleName . '[' . $repeaterIndex . '][' . $field . ']';
    }
@endphp


<div class="input boolean">
    <label class="boolean">
        {!! Form::checkbox($field, 1, null, [
            "data-behavior" => ($data_behavior ?? ''),
            "data-connected-actions" => ($data_connected_actions ?? '')
        ]) !!}&nbsp;{{ $field_name }}
    </label>
</div>
