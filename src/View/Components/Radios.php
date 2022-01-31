<?php

namespace A17\Twill\View\Components;

class Radios extends FieldWithOptions
{
    public $inline;
    public $border;

    public function __construct(
        $name,
        $label,
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
        $fieldsInModal = null,
        $inline = false,
        $border = false
    ) {
        parent::__construct(
            $name,
            $label,
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
    }

    public function render()
    {
        return view('twill::partials.form._radios', [
            'options' => $this->getOptions(),
            'inModal' => $this->inModal()
        ]);
    }
}
