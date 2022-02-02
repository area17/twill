<?php

namespace A17\Twill\View\Components;

class Map extends TwillFormComponent
{
    public $showMap;
    public $openMap;
    public $inModal;
    public $saveExtendedData;
    public $autoDetectLatLngValue;

    public function __construct(
        $name,
        $label,
        $renderForBlocks = false,
        $renderForModal = false,
        $showMap = true,
        $openMap = false,
        $fieldsInModal = false,
        $saveExtendedData = false,
        $autoDetectLatLngValue = false
    ) {
        parent::__construct($name, $label, $renderForBlocks, $renderForModal);
        $this->showMap = $showMap;
        $this->openMap = $openMap;
        $this->inModal = $fieldsInModal ?? false;
        $this->saveExtendedData = $saveExtendedData;
        $this->autoDetectLatLngValue = $autoDetectLatLngValue;
    }

    public function render()
    {
        return view('twill::partials.form._map');
    }
}
