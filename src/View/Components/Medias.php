<?php

namespace A17\Twill\View\Components;

class Medias extends TwillFormComponent
{
    public $max;
    public $required;
    public $note;
    public $fieldNote;
    public $withAddInfo;
    public $withVideoUrl;
    public $withCaption;
    public $altTextMaxLength;
    public $captionMaxLength;
    public $extraMetadatas;
    public $widthMin;
    public $heightMin;
    public $buttonOnTop;
    public $activeCrop;

    public function __construct(
        $name,
        $label,
        $max = 1,
        $required = false,
        $note = null,
        $fieldNote = null,
        $withAddInfo = true,
        $withVideoUrl = true,
        $withCaption = true,
        $altTextMaxLength = false,
        $captionMaxLength = false,
        $extraMetadatas = [],
        $widthMin = 0,
        $heightMin = 0,
        $buttonOnTop = false,
        $activeCrop = true,
    )
    {
        parent::__construct($name, $label);
        $this->max = $max;
        $this->required = $required;
        $this->note = $note;
        $this->fieldNote = $fieldNote;
        $this->withAddInfo = $withAddInfo;
        $this->withVideoUrl = $withVideoUrl;
        $this->withCaption = $withCaption;
        $this->altTextMaxLength = $altTextMaxLength;
        $this->captionMaxLength = $captionMaxLength;
        $this->extraMetadatas = $extraMetadatas;
        $this->widthMin = $widthMin;
        $this->heightMin = $heightMin;
        $this->buttonOnTop = $buttonOnTop;
        $this->activeCrop = $activeCrop;
    }

    public function render()
    {
        return view('twill::partials.form._medias', [
            'multiple' => $this->max > 1 || $this->max === 0,
        ]);
    }
}
