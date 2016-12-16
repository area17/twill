@php
    $required = $required ?? "";
    $rows = $rows ?? 10;
    $options = [];
    $behavior = "";
    if (isset($textLimit)) {
        $options['maxlength'] = "{$textLimit}";
        $behavior = "textlimit";
    }
@endphp

<div class="input text {{ $required or 'optional' }} {{$field}}">
    <label class="string {{ $required or '' }} control-label" for="{{$field}}" data-behavior="{{$behavior}}">
        {!!$field_name!!} {!! !empty($required) ? '<abbr title="required">*</abbr>' : '' !!}
    </label>
    {!! Form::textarea($field, null,['class' =>"string {$required}", 'id'=>$field, 'rows' => $rows]+$options) !!}
    @if(isset($textLimit))
        <span class="hint">
            <span class="textlimit-remaining">0</span>/ {{$textLimit}} characters maximum
        </span>
    @endif
</div>
