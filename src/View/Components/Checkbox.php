<?php

namespace A17\Twill\View\Components;

use Illuminate\View\Component;

class Checkbox extends Component
{
    public $name;
    /**
     * @var \A17\Twill\Models\Model
     */
    public $item;
    public $label;
    public $form_fields;
    public $note;
    public $default;
    public $fieldsInModal;
    public $disabled;
    public $border;
    public $confirmMessageText;
    public $confirmTitleText;
    public $requireConfirmation;

    public function __construct(
        $name,
        $label,
        $form,
        $note = false,
        $default = false,
        $fieldsInModal = false,
        $disabled = false,
        $border = false,
        $confirmMessageText = false,
        $confirmTitleText = false,
        $requireConfirmation = false
    ) {
        $this->item = $form['item'];
        $this->name = $name;
        $this->label = $label;
        $this->form_fields = $form['form_fields'];
        $this->note = $note;
        $this->default = $default;
        $this->fieldsInModal = $fieldsInModal;
        $this->disabled = $disabled;
        $this->border = $border;
        $this->confirmMessageText = $confirmMessageText;
        $this->confirmTitleText = $confirmTitleText;
        $this->requireConfirmation = $requireConfirmation;
    }

    public function render()
    {
        return view('twill::partials.form._checkbox');
    }
}
