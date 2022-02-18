<?php

namespace A17\Twill\View\Components;

class Select extends FieldWithOptions
{
    public function render()
    {
        return view('twill::partials.form._select', [
            'options' => $this->options,
            'inModal' => $this->isInModal()
        ]);
    }
}
