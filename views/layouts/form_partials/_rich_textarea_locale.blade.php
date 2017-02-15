@php
    $required = $required ?? "";
    $rows = $rows ?? 10;
    $options = $options ?? [];

    if (isset($textLimit)) {
        $options['maxlength'] = "{$textLimit}";
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

        if (isset($repeater) && $repeater) {
            $fullField = $moduleName . '[' . $repeaterIndex . '][' . $fullField . ']';
        }
    @endphp
    <div class="input text {{ $required }} {{ $fullField }} field_with_lang" data-lang="{{$locale}}" >
        <label class="string {{ $required }} control-label" for="{{ $fullField }}">
            {{ $field_name }}  {!! !empty($required) ? '<abbr title="required">*</abbr>' : '' !!}
            @unless($loop->first && $loop->last)
                <span class="lang_tag" data-behavior="lang_toggle">{{ strtoupper($locale) }}</span>
            @endunless
            {!! isset($hint) ? '<div class="/hint"> '.$hint.'</div>' : '' !!}
        </label>
        {!! Form::textarea($fullField, $fieldValue ?? null,[
            'class' => "textarea-medium-editor string {$required}",
            'id' => $fullField,
            'rows' => $rows,
            'data-behavior' => "markitup"
        ] + $options) !!}
        @if (isset($textLimit))
            <span class="hint"><span class="textlimit-remaining">0</span> / {{ $textLimit }} characters maximum</span>
        @endif
    </div>
@endforeach
