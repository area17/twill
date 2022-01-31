<?php

namespace A17\Twill\View\Components;

class Checkbox extends TwillFormComponent
{
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
        $note = false,
        $default = false,
        $fieldsInModal = false,
        $disabled = false,
        $border = false,
        $confirmMessageText = false,
        $confirmTitleText = false,
        $requireConfirmation = false
    ) {
        parent::__construct($name, $label);
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
