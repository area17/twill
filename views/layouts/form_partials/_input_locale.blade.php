@php
    $required = $required ?? "";
    $options = [];
    $behavior = "";
    $options['placeholder'] = '';

    if (isset($textLimit)) {
        $options['maxlength'] = "{$textLimit}";
        $behavior = "textlimit";
    }

    if (isset($placeholder)) {
        $options['placeholder'] = $placeholder;
    }

    if (isset($readonly)) {
        $options['readonly'] = $readonly;
    }

    if (isset($disabled)) {
        $options['disabled'] = $disabled;
    }
@endphp

@foreach (getLocales() as $locale)
    @php
        if (isset($field_wrapper)) {
            $fullField = $field_wrapper . '[' . $field . '_' . $locale . ']';
            $fieldValue = $form_fields[$fullField] ?? (isset($item) && $item->$field_wrapper ? $item->$field_wrapper->getTranslation($locale)[$field] : null);
        } else {
            $fullField = $field . '.' . $locale;
            $fieldValue = $form_fields[$fullField] ?? null;
        }
    @endphp
    <div class="input string {{ $required }} {{ $fullField }} field_with_hint field_with_lang" data-lang="{{ $locale }}">
        <label class="string {{ $required }} control-label" for="{{ $fullField }}" data-behavior="{{ $behavior }}">
            {!! $field_name !!} {!! !empty($required) ? '<abbr title="required">*</abbr>' : '' !!}
            @unless($loop->first && $loop->last)
                <span class="lang_tag" data-behavior="lang_toggle">{{ strtoupper($locale) }}</span>
            @endunless
        </label>
        {!! Form::text($fullField, $fieldValue ?? null, ['class' => "string {$fullField} {$required}", 'id'=> $fullField] + $options) !!}
        {!! isset($hint) ? '<span class="hint">'.$hint.'</span>' : '' !!}
        @if (isset($textLimit))
            <span class="hint"><span class="textlimit-remaining">0</span> / {{ $textLimit }} characters maximum</span>
        @endif
    </div>
@endforeach
