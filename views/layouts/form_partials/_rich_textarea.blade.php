@php
    $required = $required ?? "";
    $options = $options ?? [];
    if (isset($textLimit)) {
        $options['maxlength'] = "{$textLimit}";
    }

    if (isset($repeater) && $repeater) {
        $field = $moduleName . '[' . $repeaterIndex . '][' . $field . ']';
    }
@endphp

<div class="input text {{ $required or 'optional' }} {{$field}}">
    <label class="string {{ $required or '' }} control-label" for="{{$field}}">
        {!!$field_name!!} {!! !empty($required) ? '<abbr title="required">*</abbr>' : '' !!}
    </label>
    {!! Form::textarea($field, null,[
        'class' => "textarea-medium-editor string {$required}",
        'id' => $field,
        'data-behavior' => "markitup"] + $options) !!}
    @if (isset($textLimit))
        <span class="hint"><span class="textlimit-remaining">0</span> / {{ $textLimit }} characters maximum</span>
    @endif
</div>
