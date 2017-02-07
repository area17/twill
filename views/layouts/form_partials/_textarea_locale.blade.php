@php
    $disabled = $disabled ?? null;
    $required = $required ?? "";
    $rows = $rows ?? 3;
    $options = [];
    $behavior = "";
    if (isset($textLimit)) {
        $options['maxlength'] = "{$textLimit}";
        $behavior = "textlimit";
    }
    if (isset($placeholder)) {
        $options['placeholder'] = $placeholder;
    }
@endphp

@foreach (getLocales() as $locale)
    @php
        if (isset($field_wrapper)) {
            $fullField = $field_wrapper . '[' . $field . '_' . $locale . ']';
            $fieldValue = $form_fields[$fullField] ?? (isset($item) && $item->$field_wrapper ? $item->$field_wrapper->getTranslation($locale)[$field] : null);
        } else {
            $fullField = $field . '.' . $locale;
        }
    @endphp
    <div class="input text {{ $required }} {{ $fullField }} field_with_lang" data-lang="{{ $locale }}" >
        <label class="string {{ $required }} control-label" for="{{ $fullField }}" data-behavior="{{ $behavior }}">
            {{ $field_name }}  {!! !empty($required) ? '<abbr title="required">*</abbr>' : '' !!}
            @unless($loop->first && $loop->last)
                <span class="lang_tag" data-behavior="lang_toggle">{{ strtoupper($locale) }}</span>
            @endunless
            {!! isset($hint) ? '<span class="hint">'.$hint.'</span>' : '' !!}
        </label>
    {!! Form::textarea($fullField, $fieldValue ?? null,[
        'disabled' => $disabled,
        'class' => "string {$required}",
        'id'=> $fullField,
        'rows' => $rows] + $options)
    !!}
    @if(isset($textLimit))
        <span class="hint">
            <span class="textlimit-remaining">0</span>/ {{$textLimit}} characters maximum
        </span>
    @endif
</div>
@endforeach
