<?php

namespace A17\Twill\Services\Forms\Fields;

use A17\Twill\Services\Forms\Fields\Traits\hasMaxlength;
use A17\Twill\Services\Forms\Fields\Traits\hasOnChange;
use A17\Twill\Services\Forms\Fields\Traits\hasPlaceholder;
use A17\Twill\Services\Forms\Fields\Traits\isTranslatable;

/**
 * @todo: Split this? Text, Textarea, Number, ...?
 */
class Wysiwyg extends BaseFormField
{
    use isTranslatable;
    use hasMaxlength;
    use hasPlaceholder;
    use hasOnChange;

    public bool $hideCounter = false;
    public bool $editSource = false;
    public ?array $toolbarOptions = null;
    public ?array $options = null;
    public string $type = 'quill';
    public bool $limitHeight = false;
    public bool $syntax = false;
    public string $customTheme = 'github';
    public ?array $customOptions = null;

    public static function make(): static
    {
        return new self(
            component: \A17\Twill\View\Components\Wysiwyg::class,
            mandatoryProperties: ['name', 'label']
        );
    }

    public function hideCounter(bool $hideCounter = true): self
    {
        $this->hideCounter = $hideCounter;

        return $this;
    }

    public function allowSource(bool $allowSource = true): self
    {
        $this->editSource = $allowSource;

        return $this;
    }

    public function toolbarOptions(array $toolbarOptions): self
    {
        $this->toolbarOptions = $toolbarOptions;

        return $this;
    }

    public function options(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function type(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function limitHeight(bool $limitHeight = true): self
    {
        $this->limitHeight = $limitHeight;

        return $this;
    }

    public function syntax(bool $syntax = true): self
    {
        $this->syntax = $syntax;

        return $this;
    }

    public function customTheme(string $customTheme): self
    {
        $this->customTheme = $customTheme;

        return $this;
    }

    public function customOptions(array $customOptions): self
    {
        $this->customOptions = $customOptions;

        return $this;
    }

}
