@php
    $disabled = $disabled ?? null;
    if (isset($value_field_id) && !is_null($value_field_id)) {
        $field_value = $value_field_id;
    } elseif (isset($form_fields[$field])) {
        $field_value = $form_fields[$field];
    } else {
        $field_value = [];
    }
@endphp

<div class="input select" id="input_{{$id or $field}}">
    <label class="select control-label" for="{{$field}}">
        {{$field_name}} {!! !empty($required) ? '<abbr title="required">*</abbr>' : '' !!}
        {!! (!empty($hint) ? "<span class='hint'>{$hint}</span>" : '') !!}
    </label>
    {!!	Form::select("{$field}[]", $list, $field_value, [
        "disabled" => $disabled,
        "id" => $field,
        "data-placeholder" => $placeholder ?? '',
        "multiple" => "multiple",
        "data-behavior" => "selector",
        "data-maximum-selection-length" => $maximumSelectionLength ?? "Infinity",
        "data-language" => "en",
    ]) !!}
    @if (isset($button_all))
        <br /><br />
        <a href="#" class="btn btn-tiny btn-primary-border" data-behavior="select_all">Select all</a>
    @endif
</div>
