@php
    $lang_switcher_locales = getLocales();
@endphp

<section class="filter lang-filter lang-filter-primary" {!! (isset($hidden) && $hidden || count($lang_switcher_locales) < 2 ) ? 'style="display:none;"' : '' !!}>
    <p>Edit content in :</p>
    <div class="lang-switcher lang-num{{ count($lang_switcher_locales) }}" data-behavior="lang_switcher">
        <div class="lang-label">
            @foreach ($lang_switcher_locales as $locale)
                @if (!is_array($locale)))
                    <a href="#" data-lang="{{ $locale }}" title="{{ strtoupper($locale) }}" @if(($loop->first && !request()->has('locale')) || request('locale') === $locale) class="selected" @endif style="width:50px;">
                        <span class="">
                            {{strtoupper($locale)}}
                        </span>
                    </a>
                @endif
            @endforeach
        </div>
        <div class="lang-selector">
            <div class="lang-circle"></div>
        </div>
    </div>
</section>
