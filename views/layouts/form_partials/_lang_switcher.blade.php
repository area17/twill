@php
    $lang_switcher_locales = getLocales(isset($languages_only) && $languages_only === true ? $form_fields['zone'] : null);
@endphp

<section class="filter lang-filter lang-filter-primary" {!! isset($hidden) && $hidden ? 'style="display:none;"' : '' !!}>
    <p>Edit content in :</p>
    <div class="lang-switcher lang-num{{ count($lang_switcher_locales) }}" data-behavior="lang_switcher">
        <div class="lang-label">
            @php
                $firstClass = "selected";
            @endphp
            @foreach ($lang_switcher_locales as $locale)
                <a href="#" data-lang="{{ $locale }}" title="{{ strtoupper($locale) }}" class="{{ $firstClass }}" style="width:50px;">
                    <span class="">
                        {{strtoupper($locale)}}
                    </span>
                </a>
                @php
                    $firstClass = "";
                @endphp
            @endforeach
        </div>
        <div class="lang-selector">
            <div class="lang-circle"></div>
        </div>
    </div>
</section>
