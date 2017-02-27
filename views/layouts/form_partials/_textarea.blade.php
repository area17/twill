@php
    $rows = $rows ?? 10;
    $options = [];
    $behavior = "";
    if (isset($textLimit)) {
        $options['maxlength'] = "{$textLimit}";
        $behavior = "textlimit";
    }
    if (isset($repeater) && $repeater) {
        $field = $moduleName . '[' . $repeaterIndex . '][' . $field . ']';
    }
@endphp

<div class="input text {{$field}}">
    <label class="string control-label" for="{{$field}}" data-behavior="{{$behavior}}">
        {!!$field_name!!} {!! !empty($required) ? '<abbr title="required">*</abbr>' : '' !!}
    </label>
    {!! Form::textarea($field, null, ['class' => "string", 'id' => $field, 'rows' => $rows] + $options) !!}
    @if(isset($textLimit))
        <span class="hint">
            <span class="textlimit-remaining">0</span>/ {{$textLimit}} characters maximum
        </span>
    @endif
</div>
