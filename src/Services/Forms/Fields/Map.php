<?php

namespace A17\Twill\Services\Forms\Fields;

use A17\Twill\Services\Forms\Fields\Traits\IsTranslatable;

class Map extends BaseFormField
{
    use IsTranslatable;

    protected bool $showMap = true;

    protected bool $openMap = false;

    protected bool $saveExtendedData = false;

    protected bool $autoDetectLatLngValue = false;

    public static function make(): static
    {
        return new self(
            component: \A17\Twill\View\Components\Fields\Map::class,
            mandatoryProperties: ['name', 'label']
        );
    }

    /**
     * Completely remove the map.
     */
    public function hideMap(bool $hideMap = true): static
    {
        $this->showMap = !$hideMap;

        return $this;
    }

    /**
     * Show the map by default.
     */
    public function openMap(bool $openMap = true): static
    {
        $this->openMap = $openMap;

        return $this;
    }

    /**
     * Stores extended data into the field like lat/lon.
     */
    public function saveExtendedData(bool $saveExtendedData = true): static
    {
        $this->saveExtendedData = $saveExtendedData;

        return $this;
    }

    /**
     * Make the field try to automatically detect the latitude and longitude.
     */
    public function autoDetectLatLang(bool $autoDetectLatLang = true): static
    {
        $this->autoDetectLatLngValue = $autoDetectLatLang;

        return $this;
    }
}
