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
@endphp

<div class="input string {{ $required }} {{ $field }}">
    <label class="string {{ $required }} control-label" for="{{ $field }}">
        {!! $field_name !!} {!! !empty($required) ? '<abbr title="required">*</abbr>' : '' !!}
        {!! isset($hint) ? '<span class="hint">'.$hint.'</span>' : '' !!}
    </label>
    {!! Form::text($field, $value_field, ['class' => "string {$required}", 'id'=> $field] + $options) !!}
</div>
