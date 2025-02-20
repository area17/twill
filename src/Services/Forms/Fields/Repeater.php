<?php

namespace A17\Twill\Services\Forms\Fields;

use A17\Twill\Services\Forms\Fields\Traits\CanReorder;
use A17\Twill\Services\Forms\Fields\Traits\HasMax;

class Repeater extends BaseFormField
{
    use HasMax;
    use CanReorder;

    protected ?string $type = null;
    protected bool $buttonAsLink = false;
    protected bool $allowCreate = true;
    protected ?string $relation = null;
    protected ?array $browserModule = null;


    public static function make(): static
    {
        return new self(
            component: \A17\Twill\View\Components\Fields\Repeater::class,
            mandatoryProperties: ['type']
        );
    }

    /**
     * The name of the repeater, this also sets the name of field if not set yet.
     */
    public function type(string $type): static
    {
        $this->type = $type;

        if (! $this->name) {
            $this->name($type);
        }

        return $this;
    }

    /**
     * Instead of a button show a link to add a new one.
     */
    public function buttonAsLink(bool $buttonAsLink = true): static
    {
        $this->buttonAsLink = $buttonAsLink;

        return $this;
    }

    public function relation(?string $relation = null): static
    {
        $this->relation = $relation;

        return $this;
    }

    public function allowCreate(bool $allowCreate = true): static
    {
        $this->allowCreate = $allowCreate;

        return $this;
    }

    public function browserModule(?array $browserModule = null): static
    {
        $this->browserModule = $browserModule;

        return $this;
    }
}
