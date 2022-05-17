<?php

namespace A17\Twill\Services\Forms\Fields;

use A17\Twill\Services\Forms\Fields\Traits\canReorder;
use A17\Twill\Services\Forms\Fields\Traits\hasMax;

class Repeater extends BaseFormField
{
    use hasMax;
    use canReorder;

    protected ?string $type = null;
    protected bool $buttonAsLink = false;

    public static function make(): static
    {
        return new self(
            component: \A17\Twill\View\Components\Repeater::class,
            mandatoryProperties: ['type']
        );
    }

    public function type(string $type): self
    {
        $this->type = $type;

        if (!$this->name) {
            $this->name($type);
        }

        return $this;
    }

    public function buttonAsLink(bool $buttonAsLink = true): self
    {
        $this->buttonAsLink = $buttonAsLink;

        return $this;
    }
}
