<?php

namespace A17\Twill\View\Components;

use Illuminate\View\Component;

abstract class TwillFormComponent extends Component
{
    public $name;
    /**
     * @var \A17\Twill\Models\Model
     */
    public $item;
    public $label;
    public $form_fields;

    public function __construct(
        $name,
        $label,
        $form
    ) {
        $this->item = $form['item'];
        $this->name = $name;
        $this->label = $label;
        $this->form_fields = $form['form_fields'];
    }
}
