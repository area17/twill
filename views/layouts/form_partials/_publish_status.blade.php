@php
    if (isset($field_wrapper)) {
        $publishedField = $field_wrapper . '[published]';
    } else {
        $publishedField = 'published';
    }

    $fieldValue = $form_fields[$publishedField] ?? null;

    if (isset($repeater) && $repeater) {
        $fullField  = $moduleName . '[' . $repeaterIndex . '][published]';
        $fieldValue = $form_fields[$moduleName][$repeaterIndex]['published'] ?? null;

        $publishedField = $fullField;
    }
@endphp

<section class="box status-box {{ (isset($fieldValue) && $fieldValue == 1) ? 'on' : '' }}" data-behavior="status_box">
    <header>
        @unless (isset($with_label) && !$with_label)
            <h3><b>{{ $field_name or 'Status'}}</b></h3>
        @endunless
        <div class="select">
            <select name='{{ $publishedField }}' class='select' id="status" {!! $currentUser->can('publish') ? '' : "disabled" !!}>
                @foreach(['0' => $hiddenTitle ?? 'Hidden', '1' => $publishedTitle ?? 'Live'] as $key => $value)
                    <option {!! (isset($fieldValue) && $fieldValue == $key) ? 'selected="selected"' : '' !!} value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
        </div>
    </header>
    @if (isset($with_languages) && $with_languages)
        <div class="input boolean optional">
            <p>Live languages</p>
            @foreach (getLocales() as $locale)
                @php
                    if (isset($field_wrapper)) {
                        $activeField = $field_wrapper . '[active_' . $locale . ']';
                    } else {
                        $activeField = 'active.' . $locale;
                    }
                @endphp
                <label class="boolean">
                    {!! Form::checkbox($activeField, 1, $form_fields[$activeField] ?? null, ['data-lang' => $locale] + ($currentUser->can('publish') ? [] : ['disabled' => 'disabled'])) !!} {{strtoupper($locale) }}
                </label>
            @endforeach
        </div>
    @endif
</section>
