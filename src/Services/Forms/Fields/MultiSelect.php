<?php

namespace A17\Twill\Services\Forms\Fields;

use A17\Twill\Services\Forms\Fields\Traits\HasBorder;
use A17\Twill\Services\Forms\Fields\Traits\HasMax;
use A17\Twill\Services\Forms\Fields\Traits\HasMin;
use A17\Twill\Services\Forms\Fields\Traits\HasOptions;
use A17\Twill\Services\Forms\Fields\Traits\HasPlaceholder;
use A17\Twill\Services\Forms\Fields\Traits\Inlineable;
use A17\Twill\Services\Forms\Fields\Traits\IsTranslatable;
use A17\Twill\Services\Forms\Fields\Traits\Unpackable;

class MultiSelect extends BaseFormField
{
    use IsTranslatable;
    use HasOptions;
    use HasMax;
    use HasMin;
    use Inlineable;
    use Unpackable;
    use HasBorder;
    use HasPlaceholder;

    protected bool $searchable = false;

    protected bool $pushTags = false;

    protected bool $taggable = false;

    protected bool $addNew = false;

    protected ?string $moduleName = null;

    protected ?string $storeUrl = null;

    protected ?string $endpoint = null;

    public static function make(): static
    {
        return new self(
            component: \A17\Twill\View\Components\Fields\MultiSelect::class,
            mandatoryProperties: ['name', 'label']
        );
    }

    /**
     * If the options should be searchable.
     */
    public function searchable(bool $searchable = true): static
    {
        $this->searchable = $searchable;

        return $this;
    }

    public function canAddNew(bool $canAddNew = true): static
    {
        $this->addNew = $canAddNew;

        return $this;
    }

    public function moduleName(string $moduleName): static
    {
        $this->moduleName = $moduleName;

        return $this;
    }

    public function storeUrl(string $storeUrl): static
    {
        $this->storeUrl = $storeUrl;

        return $this;
    }

    public function taggable(bool $taggable = true): static
    {
        $this->taggable = $taggable;

        return $this;
    }

    public function pushTags(bool $pushTags = true): static
    {
        $this->pushTags = $pushTags;

        return $this;
    }

    public function endpoint(string $endpoint): static
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    protected function getAdditionalConstructorArguments(): array
    {
        return ['options' => $this->getOptions()];
    }
}
