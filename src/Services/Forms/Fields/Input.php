<?php

namespace A17\Twill\Services\Forms\Fields;

use A17\Twill\Services\Forms\Fields\Traits\hasMax;
use A17\Twill\Services\Forms\Fields\Traits\hasMaxlength;
use A17\Twill\Services\Forms\Fields\Traits\hasMin;
use A17\Twill\Services\Forms\Fields\Traits\hasOnChange;
use A17\Twill\Services\Forms\Fields\Traits\hasPlaceholder;
use A17\Twill\Services\Forms\Fields\Traits\isTranslatable;

/**
 * @todo: Split this? Text, Textarea, Number, ...?
 */
class Input extends BaseFormField
{
    public const TYPE_TEXT = 'text';
    public const TYPE_NUMBER = 'number';
    public const TYPE_TEXTAREA = 'textarea';

    use isTranslatable;
    use hasMin;
    use hasMax;
    use hasMaxlength;
    use hasPlaceholder;
    use hasOnChange;

    protected string $type = self::TYPE_NUMBER;
    protected string $prefix;
    protected ?int $rows;

    public static function make(): static
    {
        return new self(
            component: \A17\Twill\View\Components\Input::class,
            mandatoryProperties: ['name', 'label']
        );
    }

    public function prefix(string $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function type(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function rows(int $rows): self
    {
        $this->rows = $rows;

        return $this;
    }
}
