<?php

namespace A17\Twill\Services\Forms\Fields;

use A17\Twill\Services\Forms\Fields\Traits\hasBorder;
use A17\Twill\Services\Forms\Fields\Traits\hasMax;
use A17\Twill\Services\Forms\Fields\Traits\hasMin;
use A17\Twill\Services\Forms\Fields\Traits\hasOptions;
use A17\Twill\Services\Forms\Fields\Traits\hasPlaceholder;
use A17\Twill\Services\Forms\Fields\Traits\inlineable;
use A17\Twill\Services\Forms\Fields\Traits\isTranslatable;
use A17\Twill\Services\Forms\Fields\Traits\unpackable;

class MultiSelect extends BaseFormField
{
    use isTranslatable;
    use hasOptions;
    use hasMax;
    use hasMin;
    use inlineable;
    use unpackable;
    use hasBorder;
    use hasPlaceholder;

    protected bool $searchable = false;

    protected bool $addNew = false;

    protected ?string $moduleName = null;

    protected ?string $storeUrl = null;

    protected ?string $endpoint = null;

    public static function make(): static
    {
        return new self(
            component: \A17\Twill\View\Components\MultiSelect::class,
            mandatoryProperties: ['name', 'label']
        );
    }

    /**
     * If the options should be searchable.
     */
    public function searchable(bool $searchable = true): self
    {
        $this->searchable = $searchable;

        return $this;
    }

    public function canAddNew(bool $canAddNew = true): self
    {
        $this->addNew = $canAddNew;

        return $this;
    }

    public function moduleName(string $moduleName): self
    {
        $this->moduleName = $moduleName;

        return $this;
    }

    public function storeUrl(string $storeUrl): self
    {
        $this->storeUrl = $storeUrl;

        return $this;
    }

    public function endpoint(string $endpoint): self
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    protected function getAdditionalConstructorArguments(): array
    {
        return ['options' => $this->options ?? []];
    }
}
