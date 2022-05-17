<?php

namespace A17\Twill\Services\Forms\Fields;

use A17\Twill\Services\Forms\Fields\Traits\hasBorder;
use A17\Twill\Services\Forms\Fields\Traits\isTranslatable;

class Checkbox extends BaseFormField
{
    use isTranslatable;
    use hasBorder;

    protected ?string $confirmMessageText = null;
    protected ?string $confirmTitleText = null;
    protected bool $requireConfirmation = false;

    public static function make(): static
    {
        return new self(
            component: \A17\Twill\View\Components\Checkbox::class,
            mandatoryProperties: ['name', 'label']
        );
    }

    public function confirmMessageText(string $confirmMessageText): self
    {
        $this->confirmMessageText = $confirmMessageText;

        if (!$this->requireConfirmation) {
            $this->requireConfirmation();
        }

        return $this;
    }

    public function confirmTitleText(string $confirmTitleText): self
    {
        $this->confirmTitleText = $confirmTitleText;

        if (!$this->requireConfirmation) {
            $this->requireConfirmation();
        }

        return $this;
    }

    public function requireConfirmation(bool $requireConfirmation = true): self
    {
        $this->requireConfirmation = $requireConfirmation;

        return $this;
    }
}
