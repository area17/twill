@php
    $field = $field ?? 'slug';
@endphp

<div class="columns with_right_sidebar">
    <div class="col">
        <div class="input boolean" style="margin-top:45px;">
            <label class="boolean" for="override_slug_checkbox">
                <input class="boolean" id="override_slug_checkbox" name="override_slug_checkbox" type="checkbox" value="0" {{ str_slug($currentName) !== $currentSlug ? 'checked' : '' }} data-behavior="connected_checkbox" data-connected-actions="connected_actions">Custom
            </label>
            <script>
                var connected_actions = [
                    @foreach(getLocales() as $locale)
                        @php
                            if (isset($field_wrapper)) {
                                $fieldId = '#' . $field_wrapper . '\\\[' . $field . '_' . $locale . '\\\]';
                            } else {
                                $fieldId = '#' . $field . '_' . $locale;
                            }
                        @endphp
                        {
                            "target": '{{ $fieldId }}',
                            "value": "false",
                            "perform": "disable"
                        },
                        {
                            "target": '{{ $fieldId }}',
                            "value": "true",
                            "perform": "enable"
                        },
                    @endforeach
                ];
            </script>
        </div>
    </div>
    <div class="col">
        @formField('input_locale', [
            'field' => $field,
            'field_name' => $field_name ?? 'Slug',
            'field_wrapper' => $field_wrapper ?? null
        ])
    </div>
</div>
