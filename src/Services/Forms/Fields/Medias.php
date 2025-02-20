<?php

namespace A17\Twill\Services\Forms\Fields;

use A17\Twill\Services\Forms\Fields\Traits\CanHaveButtonOnTop;
use A17\Twill\Services\Forms\Fields\Traits\HasFieldNote;
use A17\Twill\Services\Forms\Fields\Traits\HasMax;
use A17\Twill\Services\Forms\Fields\Traits\IsTranslatable;

class Medias extends BaseFormField
{
    use IsTranslatable;
    use HasMax;
    use HasFieldNote;
    use CanHaveButtonOnTop;

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
            component: \A17\Twill\View\Components\Fields\Medias::class,
            mandatoryProperties: ['name', 'label']
        );

        // Max needs to be 1 by default for this component.
        // Cannot be null.
        $instance->max = 1;

        return $instance;
    }

    /**
     * Disables the additional metadata input fields.
     */
    public function withoutAddInfo(bool $withoutAddInfo = true): static
    {
        $this->withAddInfo = !$withoutAddInfo;

        return $this;
    }

    /**
     * Removes the video url field from the additional info section.
     */
    public function withoutVideoUrl(bool $withoutVideoUrl = true): static
    {
        $this->withVideoUrl = !$withoutVideoUrl;

        return $this;
    }

    /**
     * Removes the caption field from the additional info section.
     */
    public function withoutCaption(bool $withoutCaption = true): static
    {
        $this->withCaption = !$withoutCaption;

        return $this;
    }

    /**
     * Set the max length of the alt field.
     */
    public function altTextMaxLength(bool $altTextMaxLength): static
    {
        $this->altTextMaxLength = $altTextMaxLength;

        return $this;
    }

    /**
     * Set the max length of the caption field.
     */
    public function captionMaxLength(int $captionMaxLength): static
    {
        $this->captionMaxLength = $captionMaxLength;

        return $this;
    }

    /**
     * Define custom extra metadata.
     *
     * @see https://twillcms.com/docs/form-fields/medias.html#content-extra-metadatas
     */
    public function extraMetadatas(array $extraMetadatas): static
    {
        $this->extraMetadatas = $extraMetadatas;

        return $this;
    }

    /**
     * The minimum width of the image.
     */
    public function minWidth(int $minWidth): static
    {
        $this->widthMin = $minWidth;

        return $this;
    }

    /**
     * The minimum height of the image.
     */
    public function minHeight(int $minHeight): static
    {
        $this->heightMin = $minHeight;

        return $this;
    }

    /**
     * Hide the cropper.
     */
    public function hideActiveCrop(bool $hideActiveCrop = true): static
    {
        $this->activeCrop = !$hideActiveCrop;

        return $this;
    }
}
