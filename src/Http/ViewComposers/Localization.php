<?php

namespace A17\Twill\Http\ViewComposers;

use Illuminate\Support\Facades\Lang;
use Illuminate\Contracts\View\View;

class Localization
{
    /**
     * Bind data to the view.
     *
     * @return void
     */
    public function compose(View $view)
    {
        $currentLang = Lang::get('twill::lang', [], config('twill.locale'));
        $fallbackLang = Lang::get('twill::lang', [], config('twill.fallback_locale', 'en'));
        $lang = array_replace_recursive($fallbackLang, $currentLang);

        $twillLocalization = [
            'locale' => config('twill.locale'),
            'fallback_locale' => config('twill.fallback_locale', 'en'),
            'lang' => $lang
        ];

        $view->with(['twillLocalization' => $twillLocalization]);
    }
}
