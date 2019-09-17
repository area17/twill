@if($translated && isset($form_fields['translations']) && isset($form_fields['translations'][$name]))
    window.STORE.form.fields.push({
        name: '{{ $name }}',
        value: {
            @foreach(getLocales() as $locale)
                '{{ $locale }}': {!! json_encode(
                    $form_fields['translations'][$name][$locale] ?? ''
                ) !!}@unless($loop->last),@endif
            @endforeach
        }
    })
@elseif(isset($item->$name) || null !== $formFieldsValue = getFormFieldsValue($form_fields, $name))
    window.STORE.form.fields.push({
        name: '{{ $name }}',
        value: {!! json_encode(isset($item->$name) ? $item->$name : (isset($formFieldsValue)
            ? (is_array($formFieldsValue) && !$translated
                ? Arr::first($formFieldsValue, null, '')
                : $formFieldsValue)
            : '')
        ) !!}
    })
@endif
