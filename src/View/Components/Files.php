<?php

namespace A17\Twill\View\Components;

use Illuminate\Support\Str;

class Files extends TwillFormComponent
{

    public $fieldNote;
    public $filesizeMax;
    public $buttonOnTop;
    public $itemLabel;
    public $note;
    public $max;

    public function __construct(
        $name,
        $label,
        $renderForBlocks = false,
        $renderForModal = false,
        $max = 1,
        $itemLabel = null,
        $note = null,
        $fieldNote = null,
        $filesizeMax = 0,
        $buttonOnTop = false
    ) {
        parent::__construct($name, $label, $renderForBlocks, $renderForModal);
        $itemLabel = $itemLabel ?? strtolower($label);
        $this->itemLabel = $itemLabel;
        $this->note = $note ?? 'Add' . ($max > 1 ? " up to $max $itemLabel" : ' one ' . Str::singular($itemLabel));
        $this->fieldNote = $fieldNote;
        $this->filesizeMax = $filesizeMax;
        $this->buttonOnTop = $buttonOnTop;
        $this->max = $max;
    }

    public function render()
    {
        return view('twill::partials.form._files');
    }
}
