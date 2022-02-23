<?php

namespace A17\Twill\View\Components;

class MultiSelect extends FieldWithOptions
{
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
        $default = false,
        $storeUrl = null,
        $fieldsInModal = false,
        $min = null,
        $max = null
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
        $this->min = $min;
        $this->max = $max;
    }

    public function render()
    {
        return view('twill::partials.form._multi_select', [
            'options' => $this->options,
            'inModal' => $this->isInModal()
        ]);
    }
}
