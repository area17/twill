<?php

namespace A17\Twill\View\Components;

class Wysiwyg extends TwillFormComponent
{
    public $translated;
    public $required;
    public $maxlength;
    public $options;
    public $placeholder;
    public $note;
    public $disabled;
    public $readonly;
    public $editSource;
    public $toolbarOptions;
    public $inModal;
    public $hideCounter;
    public $type;
    public $limitHeight;
    public $syntax;
    public $customTheme;
    public $customOptions;
    public $default;

    public function __construct(
        $name,
        $label,
        $translated = false,
        $required = false,
        $maxlength = null,
        $options = null,
        $placeholder = null,
        $note = null,
        $default = null,
        $disabled = false,
        $readonly = false,
        $editSource = false,
        $toolbarOptions = null,
        $inModal = false,
        $hideCounter = false,
        $type = 'quill',
        $limitHeight = false,
        $syntax = false,
        $customTheme = 'github',
        $customOptions = null
    ) {
        parent::__construct($name, $label);
        $this->translated = $translated;
        $this->required = $required;
        $this->maxlength = $maxlength;
        $this->options = $options;
        $this->placeholder = $placeholder;
        $this->note = $note;
        $this->disabled = $disabled;
        $this->readonly = $readonly;
        $this->editSource = $editSource;
        $this->toolbarOptions = $toolbarOptions;
        $this->inModal = $inModal;
        $this->hideCounter = $hideCounter;
        $this->type = $type;
        $this->limitHeight = $limitHeight;
        $this->syntax = $syntax;
        $this->customTheme = $customTheme;
        $this->customOptions = $customOptions;
        $this->default = $default;
    }

    public function render()
    {
        if ($this->toolbarOptions) {
            $toolbarOptions = array_map(static function ($option) {
                if ($option === 'list-unordered') {
                    return (object)['list' => 'bullet'];
                }
                if ($option === 'list-ordered') {
                    return (object)['list' => 'ordered'];
                }
                if ($option === 'h1') {
                    return (object)['header' => 1];
                }
                if ($option === 'h2') {
                    return (object)['header' => 2];
                }
                return $option;
            }, $this->toolbarOptions);

            $toolbarOptions = [
                'modules' => [
                    'toolbar' => $toolbarOptions,
                    'syntax' => $this->syntax,
                ],
            ];
        }

        $options = $this->customOptions ?? $toolbarOptions ?? false;

        return view('twill::partials.form._wysiwyg', [
            'theme' => $this->customTheme,
            'activeSyntax' => $this->syntax,
            'options' => $options,
        ]);
    }
}
