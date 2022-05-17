<?php

namespace A17\Twill\Services\Forms\Fields;

use A17\Twill\Services\Forms\Fields\Traits\hasFieldNote;
use A17\Twill\Services\Forms\Fields\Traits\hasMax;
use A17\Twill\Services\Forms\Fields\Traits\isTranslatable;

class Medias extends BaseFormField
{
    use isTranslatable;
    use hasMax;
    use hasFieldNote;

    protected bool $buttonOnTop = false;
    protected ?string $itemLabel = null;
    protected bool $withAddInfo = true;
    protected bool $withVideoUrl = true;
    protected bool $withCaption = true;
    protected ?int $altTextMaxLength = null;
    protected ?int $captionMaxLength = null;
    protected array $extraMetadatas = [];
    protected int $widthMin = 0;
    protected int $heightMin = 0;
    protected bool $activeCrop = true;

    public static function make(): static
    {
        $instance = new self(
            component: \A17\Twill\View\Components\Medias::class,
            mandatoryProperties: ['name', 'label']
        );

        // Max needs to be 1 by default for this component.
        // Cannot be null.
        $instance->max = 1;

        return $instance;
    }

    public function buttonOnTop(bool $buttonOnTop = true): self
    {
        $this->buttonOnTop = $buttonOnTop;

        return $this;
    }

    public function withoutAddInfo(bool $withoutAddInfo = true): self
    {
        $this->withAddInfo = !$withoutAddInfo;

        return $this;
    }

    public function withoutVideoUrl(bool $withoutVideoUrl = true): self
    {
        $this->withVideoUrl = !$withoutVideoUrl;

        return $this;
    }

    public function withoutCaption(bool $withoutCaption = true): self
    {
        $this->withCaption = !$withoutCaption;

        return $this;
    }

    public function altTextMaxLength(bool $altTextMaxLength): self
    {
        $this->altTextMaxLength = $altTextMaxLength;

        return $this;
    }

    public function captionMaxLength(int $captionMaxLength): self
    {
        $this->captionMaxLength = $captionMaxLength;

        return $this;
    }

    public function extraMetadatas(array $extraMetadatas): self
    {
        $this->extraMetadatas = $extraMetadatas;

        return $this;
    }

    public function minWidth(int $minWidth): self
    {
        $this->widthMin = $minWidth;

        return $this;
    }

    public function minHeight(int $minHeight): self
    {
        $this->heightMin = $minHeight;

        return $this;
    }

    public function hideActiveCrop(bool $hideActiveCrop = true): self
    {
        $this->activeCrop = !$hideActiveCrop;

        return $this;
    }

}
