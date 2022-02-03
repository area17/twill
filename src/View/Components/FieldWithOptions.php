<?php

namespace A17\Twill\View\Components;

abstract class FieldWithOptions extends TwillFormComponent
{
    public $options;
    public $unpack;
    public $columns;
    public $searchable;
    public $note;
    public $placeholder;
    public $disabled;
    public $addNew;
    public $moduleName;
    public $storeUrl;
    public $required;
    public $fieldsInModal;
    public $default;
    /** Below are unused but needed to keep compatible  */
    public $confirmMessageText;
    public $confirmTitleText;
    public $requireConfirmation;

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
        $default = null,
        $fieldsInModal = null
    ) {
        parent::__construct(
            $name,
            $label,
            $renderForBlocks,
            $renderForModal
        );

        $this->options = $options;
        $this->unpack = $unpack;
        $this->columns = $columns;
        $this->searchable = $searchable;
        $this->note = $note;
        $this->placeholder = $placeholder;
        $this->disabled = $disabled;
        $this->addNew = $addNew;
        $this->default = $default;
        $this->moduleName = $moduleName;
        $this->storeUrl = $storeUrl;
        $this->fieldsInModal = $fieldsInModal;

        $this->confirmMessageText = null;
        $this->confirmTitleText = null;
        $this->requireConfirmation = null;
    }

    public function inModal(): bool {
        return $this->fieldsInModal ?? false;
    }

    public function getOptions(): array
    {
        return is_object($this->options) && method_exists($this->options, 'map') ? $this->options->map(
            function ($label, $value) {
                return [
                    'value' => $value,
                    'label' => $label,
                ];
            }
        )->values()->toArray() : $this->options;
    }
}
