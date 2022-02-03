<?php

namespace A17\Twill\View\Components;

use Illuminate\Support\Facades\View;
use Illuminate\View\Component;

abstract class TwillFormComponent extends Component
{
    public $name;
    /**
     * @var \A17\Twill\Models\Model | null
     */
    public $item;
    public $label;
    public $form_fields;
    public $renderForBlocks;
    public $renderForModal;

    public function __construct(
        $name,
        $label,
        $renderForBlocks = false,
        $renderForModal = false
    ) {
        // This can be null. In that case the field might be used outside of a form and we have no shared $form.
        $form = View::shared("form");
        $this->renderForBlocks = $renderForBlocks;
        $this->item = $form['item'] ?? null;
        $this->form_fields = $form['form_fields'] ?? [];
        $this->name = $name;
        $this->label = $label;

        $shared = View::shared('TwillUntilConsumed', []);
        // UnShare.
        View::share('TwillUntilConsumed', []);
        foreach ($shared as $key => $value) {
            $this->{$key} = $value;
        }
    }
}
