@php
    $options = [];
    if (isset($textLimit)) {
        $options['maxlength'] = "{$textLimit}";
    }
@endphp

<div class="input text {{$field}}">
    <label class="string control-label" for="{{$field}}">
        {!!$field_name!!} {!! !empty($required) ? '<abbr title="required">*</abbr>' : '' !!}
    </label>
    {!! Form::textarea($field, null,[
        'class' => "textarea-medium-editor string",
        'id' => $field,
        'data-behavior' => "medium_editor",
        'data-medium-editor-js' => "assets/admin/vendor/medium-editor/medium-editor.min.js",
        'data-medium-editor-css' => "assets/admin/vendor/medium-editor/medium-editor.css, assets/admin/vendor/medium-editor/themes/flat.min.css",
        'data-medium-editor-options' => ($data_medium_editor_options ?? ''),
    ] + $options) !!}
    @if (isset($textLimit))
        <span class="hint"><span class="textlimit-remaining">0</span> / {{ $textLimit }} characters maximum</span>
    @endif
</div>
