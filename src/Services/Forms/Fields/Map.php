<?php

namespace A17\Twill\Services\Forms\Fields;

use A17\Twill\Services\Forms\Fields\Traits\isTranslatable;

class Map extends BaseFormField
{
    use isTranslatable;

    protected bool $showMap = true;
    protected bool $openMap = false;
    protected bool $saveExtendedData = false;
    protected bool $autoDetectLatLngValue = false;

    public static function make(): static
    {
        return new self(
            component: \A17\Twill\View\Components\Map::class,
            mandatoryProperties: ['name', 'label']
        );
    }

    /**
     * Hides the map which is shown by default.
     */
    public function hideMap(bool $hideMap = true): self
    {
        $this->showMap = !$hideMap;

        return $this;
    }

    public function openMap(bool $openMap = true): self
    {
        $this->openMap = $openMap;

        return $this;
    }

    public function saveExtendedData(bool $saveExtendedData = true): self
    {
        $this->saveExtendedData = $saveExtendedData;

        return $this;
    }

    public function autoDetectLatLang(bool $autoDetectLatLang = true): self
    {
        $this->autoDetectLatLngValue = $autoDetectLatLang;

        return $this;
    }
}
