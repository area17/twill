<?php

namespace A17\Twill\Services\Forms\Fields;

use A17\Twill\Services\Forms\Fields\Helpers\TiptapWrapper;
use A17\Twill\Services\Forms\Fields\Traits\HasMaxlength;
use A17\Twill\Services\Forms\Fields\Traits\HasOnChange;
use A17\Twill\Services\Forms\Fields\Traits\HasPlaceholder;
use A17\Twill\Services\Forms\Fields\Traits\HasDirection;
use A17\Twill\Services\Forms\Fields\Traits\IsTranslatable;

class Wysiwyg extends BaseFormField
{
    use IsTranslatable;
    use HasMaxlength;
    use HasPlaceholder;
    use HasDirection;
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
        'hr',
        'code',
        'link',
        'clean',
        'table',
    ];

    /**
     * @var TiptapWrapper[]
     */
    protected array $tiptapWrappers = [];

    public ?array $options = null;

    public string $type = 'tiptap';

    public bool $limitHeight = false;

    public bool $syntax = false;

    public string $customTheme = 'github';

    public ?array $customOptions = null;

    public ?array $browserModules;

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
    public function hideCounter(bool $hideCounter = true): static
    {
        $this->hideCounter = $hideCounter;

        return $this;
    }

    /**
     * Adds a edit source button.
     */
    public function allowSource(bool $allowSource = true): static
    {
        $this->editSource = $allowSource;

        return $this;
    }

    /**
     * Allows you to set custom toolbar options. This depends on the editor used.
     */
    public function toolbarOptions(array $toolbarOptions): static
    {
        $this->toolbarOptions = $toolbarOptions;

        return $this;
    }

    /**
     * Allows you to set editor options. This depends on the editor used.
     */
    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    /**
     * The type of editor to use, defaults to quill, options are: tiptap
     */
    public function type(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Limits the height of the editor, otherwise grows infinitely.
     */
    public function limitHeight(bool $limitHeight = true): static
    {
        $this->limitHeight = $limitHeight;

        return $this;
    }

    /**
     * Enables syntax highlight.
     */
    public function syntax(bool $syntax = true): static
    {
        $this->syntax = $syntax;

        return $this;
    }

    /**
     * Set a custom theme for the syntax highlighter.
     */
    public function customTheme(string $customTheme): static
    {
        $this->customTheme = $customTheme;

        return $this;
    }

    /**
     * Additional custom options.
     */
    public function customOptions(array $customOptions): static
    {
        $this->customOptions = $customOptions;

        return $this;
    }

    /**
     * Add wrappers that can be used in the editor.
     */
    public function addTiptapWrapper(TiptapWrapper $wrapper): static
    {
        $this->tiptapWrappers[$wrapper->className] = $wrapper;

        return $this;
    }

    protected function getToolbarOptions(): array
    {
        $base = $this->toolbarOptions;
        if ($this->tiptapWrappers !== []) {
            $wrapperList = [];
            foreach ($this->tiptapWrappers as $wrapper) {
                $wrapperList[] = $wrapper->toArray();
            }
            $base[] = [
                'wrappers' => $wrapperList
            ];
        }

        return $base;
    }

    /**
     * The browser module(s) that can be used to select existing content.
     */
    public function browserModules(?array $modules = null): static
    {
        if (count($modules) === 1 && ! isset($modules[0])) {
            $this->browserModules[] = [
                'name' => getModuleNameByModel(array_pop($modules))
            ];
        } else {
            foreach ($modules as $module) {
                if (isset($module['name'])) {
                    $this->browserModules[] = [
                        'name' => getModuleNameByModel($module['name']),
                        'label' => $module['label']
                    ];
                } else {
                    $this->browserModules[] = [
                        'name' => getModuleNameByModel($module),
                    ];
                }
            }
        }

        return $this;
    }
}
