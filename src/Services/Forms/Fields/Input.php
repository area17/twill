<?php

namespace A17\Twill\Services\Forms\Fields;

use A17\Twill\Services\Forms\Fields\Traits\HasMax;
use A17\Twill\Services\Forms\Fields\Traits\HasMaxlength;
use A17\Twill\Services\Forms\Fields\Traits\HasMin;
use A17\Twill\Services\Forms\Fields\Traits\HasOnChange;
use A17\Twill\Services\Forms\Fields\Traits\HasPlaceholder;
use A17\Twill\Services\Forms\Fields\Traits\HasDirection;
use A17\Twill\Services\Forms\Fields\Traits\HasReadOnly;
use A17\Twill\Services\Forms\Fields\Traits\IsTranslatable;

/**
 * @todo: Split this? Text, Textarea, Number, ...?
 */
class Input extends BaseFormField
{
    use IsTranslatable;
    use HasMin;
    use HasMax;
    use HasMaxlength;
    use HasPlaceholder;
    use HasReadOnly;
    use HasDirection;
    use HasOnChange;

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

    protected string $type = self::TYPE_TEXT;

    protected string $prefix;

    protected ?string $mask = null;

    protected ?int $rows;

    public static function make(): static
    {
        return new self(
            component: \A17\Twill\View\Components\Fields\Input::class,
            mandatoryProperties: ['name', 'label']
        );
    }

    /**
     * Text to display (inside) before the actual input.
     */
    public function prefix(string $prefix): static
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * The type of input field like: text, number, email, ..
     */
    public function type(string $type): static
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
    public function mask(string $mask): static
    {
        $this->mask = $mask;

        return $this;
    }

    /**
     * The amount of rows, only used with textarea type.
     */
    public function rows(int $rows): static
    {
        $this->rows = $rows;

        return $this;
    }
}
