<?php

namespace A17\Twill\View\Components;

class Input extends TwillFormComponent
{
    public $type;
    public $translated;
    public $required;
    public $placeholder;
    public $maxlength;
    public $readonly;
    public $rows;
    public $ref;
    public $onChange;
    public $onChangeAttribute;
    public $prefix;
    public $inModal;
    public $note;
    public $disabled;
    public $default;

    public function __construct(
        $name,
        $label,
        $type = null,
        $translated = false,
        $required = false,
        $note = null,
        $placeholder = null,
        $maxlength = null,
        $disabled = false,
        $readonly = false,
        $default = null,
        $rows = null,
        $ref = null,
        $onChange = null,
        $onChangeAttribute = null,
        $prefix = null,
        $inModal = false,
        $renderForBlocks = false,
        $renderForModal = false
    ) {
        parent::__construct($name, $label, $renderForBlocks, $renderForModal);

        $this->type = $type;
        $this->translated = $translated;
        $this->required = $required;
        $this->note = $note;
        $this->placeholder = $placeholder;
        $this->maxlength = $maxlength;
        $this->disabled = $disabled;
        $this->readonly = $readonly;
        $this->default = $default;
        $this->rows = $rows;
        $this->ref = $ref;
        $this->onChange = $onChange;
        $this->onChangeAttribute = $onChangeAttribute;
        $this->prefix = $prefix;
        $this->inModal = $inModal;
    }

    public function render()
    {
        return view('twill::partials.form._input', [
            'onChangeFullAttribute' => $this->onChangeAttribute ? "('".$this->onChangeAttribute."', ...arguments)" : "",
        ]);
    }
}
