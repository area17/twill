<?php

namespace A17\Twill\View\Components\Fields;

use Illuminate\Contracts\View\View;

class Tags extends TwillFormComponent
{
    public function __construct(
        string $label = 'Tags',
        ?string $note = '',
    ) {
        parent::__construct(
            name: 'tags',
            label: $label,
            note: $note
        );
    }

    public function render(): View
    {
        return view('twill::partials.form._tags', $this->data());
    }
}
