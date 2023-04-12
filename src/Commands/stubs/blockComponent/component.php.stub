<?php

namespace {{namespace}};

use A17\Twill\Services\Forms\Fields\Wysiwyg;
use A17\Twill\Services\Forms\Form;
use A17\Twill\Services\Forms\Fields\Input;
use A17\Twill\View\Components\Blocks\TwillBlockComponent;
use Illuminate\Contracts\View\View;

class {{class}} extends TwillBlockComponent
{
    public function render(): View
    {
        return {{ view }};
    }

    public function getForm(): Form
    {
        return Form::make([
            Input::make()->name('title'),
            Wysiwyg::make()->name('text')
        ]);
    }
}
