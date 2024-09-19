<?php

namespace A17\Twill\View\Components\Fields;

use Illuminate\Contracts\View\View;

class Assets extends Medias
{
    public function render(): View
    {
        return view(
            'twill::partials.form._assets',
            $this->data()
        );
    }
}
