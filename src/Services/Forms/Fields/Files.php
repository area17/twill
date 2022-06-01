<?php

namespace A17\Twill\Services\Forms\Fields;

use A17\Twill\Services\Forms\Fields\Traits\canHaveButtonOnTop;
use A17\Twill\Services\Forms\Fields\Traits\hasFieldNote;
use A17\Twill\Services\Forms\Fields\Traits\hasMax;
use A17\Twill\Services\Forms\Fields\Traits\isTranslatable;
use Illuminate\Support\Str;

class Files extends BaseFormField
{
    use isTranslatable;
    use hasMax;
    use hasFieldNote;
    use canHaveButtonOnTop;

    protected ?string $itemLabel = null;

    protected ?int $filesizeMax = 0;

    public static function make(): static
    {
        $instance = new self(
            component: \A17\Twill\View\Components\Fields\Files::class,
            mandatoryProperties: ['name', 'label']
        );

        // Max needs to be 1 by default for this component.
        // Cannot be null.
        $instance->max = 1;

        return $instance;
    }

    /**
     * {@inheritDoc}
     */
    public function label(string $label): BaseFormField
    {
        if (!$this->itemLabel) {
            $this->itemLabel = strtolower($label);
        }

        return parent::label($label);
    }

    /**
     * Default is 0 which is unlimited (depending on server config).
     */
    public function filesizeMax(int $filesizeMax): self
    {
        $this->filesizeMax = $filesizeMax;

        return $this;
    }

    /**
     * The label to display for items, defaults to the field label.
     */
    public function itemLabel(string $itemLabel): self
    {
        $this->itemLabel = $itemLabel;

        return $this;
    }

    public function getNote(): string
    {
        if ($this->note) {
            return $this->note;
        }

        // @todo: Add new translatable string for this.
        if ($this->max > 1) {
            return "Add up to {$this->max} {$this->itemLabel}";
        }

        return 'Add one ' . Str::singular($this->itemLabel);
    }
}
