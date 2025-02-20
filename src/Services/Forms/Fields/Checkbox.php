<?php

namespace A17\Twill\Services\Forms\Fields;

use A17\Twill\Services\Forms\Fields\Traits\HasBorder;
use A17\Twill\Services\Forms\Fields\Traits\IsTranslatable;

class Checkbox extends BaseFormField
{
    use IsTranslatable;
    use HasBorder;

    protected ?string $confirmMessageText = null;

    protected ?string $confirmTitleText = null;

    protected bool $requireConfirmation = false;

    public static function make(): static
    {
        return new self(
            component: \A17\Twill\View\Components\Fields\Checkbox::class,
            mandatoryProperties: ['name', 'label']
        );
    }

    /**
     * The message to show when confirming the checking of the checkbox.
     */
    public function confirmMessageText(string $confirmMessageText): static
    {
        $this->confirmMessageText = $confirmMessageText;

        if (!$this->requireConfirmation) {
            $this->requireConfirmation();
        }

        return $this;
    }

    /**
     * The title to show when confirming the checking of the checkbox.
     */
    public function confirmTitleText(string $confirmTitleText): static
    {
        $this->confirmTitleText = $confirmTitleText;

        if (!$this->requireConfirmation) {
            $this->requireConfirmation();
        }

        return $this;
    }

    /**
     * If the checkbox needs confirmation on ticking.
     */
    public function requireConfirmation(bool $requireConfirmation = true): static
    {
        $this->requireConfirmation = $requireConfirmation;

        return $this;
    }
}
