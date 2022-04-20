<?php

namespace A17\Twill\View\Components;

use Illuminate\Contracts\View\View;

class Select extends FieldWithOptions
{
    public function render(): View
    {
        return view('twill::partials.form._select', [
            ... $this->data(),
        ]);
    }
}
