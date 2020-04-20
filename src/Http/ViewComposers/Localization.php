<?php

namespace A17\Twill\Http\ViewComposers;

use App;
use Illuminate\Support\Facades\Lang;
use Illuminate\Contracts\View\View;

class Localization
{
    /**
     * Create a new profile composer.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
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