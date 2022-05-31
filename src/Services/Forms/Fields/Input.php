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
    /**
     * @var string
     */
    public const TYPE_TEXT = 'text';

    /**
     * @var string
     */
    public const TYPE_NUMBER = 'number';

    /**
     * @var string
     */
    public const TYPE_TEXTAREA = 'textarea';

    /**
     * @var string
     */
    public const TYPE_EMAIL = 'email';

    /**
     * @var string
     */
    public const TYPE_URL = 'url';

    use isTranslatable;
    use hasMin;
    use hasMax;
    use hasMaxlength;
    use hasPlaceholder;
    use hasOnChange;

    protected string $type = self::TYPE_TEXT;

    protected string $prefix;

    protected ?string $mask = null;

    protected ?int $rows;

    public static function make(): static
    {
        return new self(
            component: \A17\Twill\View\Components\Input::class,
            mandatoryProperties: ['name', 'label']
        );
    }

    /**
     * Text to display (inside) before the actual input.
     */
    public function prefix(string $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * The type of input field like: text, number, email, ..
     */
    public function type(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Apply a mask to field based on alpinjs mask.
     * This only works with Input::TYPE_TEXT
     *
     * @see https://alpinejs.dev/plugins/mask
     */
    public function mask(string $mask): self {
        $this->mask = $mask;

        return $this;
    }

    /**
     * The amount of rows, only used with textarea type.
     */
    public function rows(int $rows): self
    {
        $this->rows = $rows;

        return $this;
    }
}
