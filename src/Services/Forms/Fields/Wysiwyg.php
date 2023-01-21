<?php

namespace A17\Twill\Services\Forms\Fields;

use A17\Twill\Services\Forms\Fields\Traits\HasMaxlength;
use A17\Twill\Services\Forms\Fields\Traits\HasOnChange;
use A17\Twill\Services\Forms\Fields\Traits\HasPlaceholder;
use A17\Twill\Services\Forms\Fields\Traits\IsTranslatable;

/**
 * @todo: Split this? Text, Textarea, Number, ...?
 */
class Wysiwyg extends BaseFormField
{
    use IsTranslatable;
    use HasMaxlength;
    use HasPlaceholder;
    use HasOnChange;

    public bool $hideCounter = false;

    public bool $editSource = false;

    public ?array $toolbarOptions = [
        ['header' => [2, 3, 4, 5, 6, false]],
        'bold',
        'italic',
        'underline',
        'strike',
        'blockquote',
        "code-block",
        'ordered',
        'bullet',
        ["align" => []],
        ["direction" => "rtl"],
        'code',
        'link',
        'clean',
        'table'
    ];

    public ?array $options = null;

    public string $type = 'tiptap';

    public bool $limitHeight = false;

    public bool $syntax = false;

    public string $customTheme = 'github';

    public ?array $customOptions = null;

    public static function make(): static
    {
        return new self(
            component: \A17\Twill\View\Components\Fields\Wysiwyg::class,
            mandatoryProperties: ['name', 'label']
        );
    }

    /**
     * Hides the character counter.
     */
    public function hideCounter(bool $hideCounter = true): self
    {
        $this->hideCounter = $hideCounter;

        return $this;
    }

    /**
     * Adds a edit source button.
     */
    public function allowSource(bool $allowSource = true): self
    {
        $this->editSource = $allowSource;

        return $this;
    }

    /**
     * Allows you to set custom toolbar options. This depends on the editor used.
     */
    public function toolbarOptions(array $toolbarOptions): self
    {
        $this->toolbarOptions = $toolbarOptions;

        return $this;
    }

    /**
     * Allows you to set editor options. This depends on the editor used.
     */
    public function options(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * The type of editor to use, defaults to quill, options are: tiptap
     */
    public function type(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Limits the height of the editor, otherwise grows infinitely.
     */
    public function limitHeight(bool $limitHeight = true): self
    {
        $this->limitHeight = $limitHeight;

        return $this;
    }

    /**
     * Enables syntax highlight.
     */
    public function syntax(bool $syntax = true): self
    {
        $this->syntax = $syntax;

        return $this;
    }

    /**
     * Set a custom theme for the syntax highlighter.
     */
    public function customTheme(string $customTheme): self
    {
        $this->customTheme = $customTheme;

        return $this;
    }

    /**
     * Additional custom options.
     */
    public function customOptions(array $customOptions): self
    {
        $this->customOptions = $customOptions;

        return $this;
    }
}
