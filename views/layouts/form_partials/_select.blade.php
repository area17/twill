@php
    $required = $required ?? "";
    if (isset($value_field_id) && !is_null($value_field_id)) {
        $field_value = $value_field_id;
    } elseif (isset($form_fields[$field])) {
        $field_value = $form_fields[$field];
    } else {
        $field_value = [];
    }

    if (isset($repeater) && $repeater) {
        $field = $moduleName . '[' . $repeaterIndex . '][' . $field . ']';
        $field_value = $form_fields[$field] ?? null;
    }
@endphp

<div class="input select {{ $required }}" id="input_{{ $id or $field }}">
    <label class="select {{ $required }} control-label" for="{{ $id or $field }}">
        {{ $field_name }} {!! !empty($required) ? '<abbr title="required">*</abbr>' : '' !!}
    </label>
    {!!	Form::select("{$field}", $list, $field_value, [
        "class" => "select " . $required,
        "id" => $id ?? $field,
        "data-placeholder" => ($placeholder ?? ''),
        "data-allow-clear" => ($allowclear ?? false),
        "data-behavior" => ($data_behavior ?? ''),
        "data-connected-actions" => ($data_connected_actions ?? ''),
        "data-minimum-results-for-search" => ($minimumResultsForSearch ?? 11),
        "data-update-target" => ($data_update_target ?? ''),
        "data-update-url" => ($data_update_url ?? ''),
        "data-update-inputs" => ($data_update_inputs ?? ''),
    ]) !!}
</div>
