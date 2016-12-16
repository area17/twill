@php
    if (isset($field_wrapper)) {
        $publishedField = $field_wrapper . '[published]';
    } else {
        $publishedField = 'published';
    }
@endphp

<section class="box status-box {{ (isset($form_fields[$publishedField]) && $form_fields[$publishedField] == 1) ? 'on' : '' }}" data-behavior="status_box">
    <header>
        <h3><b>{{ $field_name or 'Status'}}</b></h3>
        <div class="select">
            <select name='{{ $publishedField }}' class='select' id="status" {!! $currentUser->can('publish') ? '' : "disabled" !!}>
                @foreach(['0' => 'Hidden', '1' => 'Live'] as $key => $value)
                    <option {!! (isset($form_fields[$publishedField]) && $form_fields[$publishedField] == $key) ? 'selected="selected"' : '' !!} value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
        </div>
    </header>
    @if (isset($with_languages) && $with_languages)
        <div class="input boolean optional" style="margin-top: 20px;">
            <h3 style="padding-top: 10px;"><strong>Languages</strong></h3><br><br>
            @foreach (getLocales() as $locale)
                @php
                    if (isset($field_wrapper)) {
                        $activeField = $field_wrapper . '[active_' . $locale . ']';
                    } else {
                        $activeField = 'active.' . $locale;
                    }
                @endphp
                <label class="boolean" style="line-height: 35px;">
                    {!! Form::checkbox($activeField, 1, $form_fields[$activeField] ?? null, ['data-lang' => $locale] + ($currentUser->can('publish') ? [] : ['disabled' => 'disabled'])) !!} {{strtoupper($locale) }}
                </label>
            @endforeach
        </div>
    @endif
</section>
