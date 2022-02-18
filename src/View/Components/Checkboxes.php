<?php

namespace A17\Twill\View\Components;

class Checkboxes extends FieldWithOptions
{
    public $inline;
    public $border;
    public $min;
    public $max;

    public function __construct(
        $name,
        $label,
        $renderForBlocks = false,
        $renderForModal = false,
        $options = [],
        $unpack = false,
        $columns = 0,
        $searchable = false,
        $note = null,
        $placeholder = null,
        $disabled = false,
        $addNew = false,
        $moduleName = null,
        $storeUrl = null,
        $fieldsInModal = false,
        $default = false,
        $min = null,
        $max = null,
        $inline = false,
        $border = false
    ) {
        parent::__construct(
            $name,
            $label,
            $renderForBlocks,
            $renderForModal,
            $options,
            $unpack,
            $columns,
            $searchable,
            $note,
            $placeholder,
            $disabled,
            $addNew,
            $moduleName,
            $storeUrl,
            $default,
            $fieldsInModal
        );

        $this->inline = $inline;
        $this->border = $border;
        $this->min = $min;
        $this->max = $max;
    }

    public function render()
    {
        return view('twill::partials.form._checkboxes', [
            'options' => $this->getOptions(),
            'inModal' => $this->isInModal()
        ]);
    }
}
