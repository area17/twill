<?php

namespace A17\Twill\View\Components;

class DatePicker extends TwillFormComponent
{
    public $withTime;
    public $allowInput;
    public $allowClear;
    public $note;
    public $inModal;
    public $timeOnly;
    public $placeholder;
    public $required;
    public $time24Hr;
    public $altFormat;
    public $hourIncrement;
    public $minuteIncrement;

    public function __construct(
        $name,
        $label,
        $withTime = true,
        $allowInput = false,
        $allowClear = false,
        $note = null,
        $inModal = false,
        $placeholder = '',
        $timeOnly = false,
        $required = false,
        $time24Hr = false,
        $altFormat = null,
        $hourIncrement = null,
        $minuteIncrement = null
    ) {
        parent::__construct($name, $label);
        $this->withTime = $withTime;
        $this->allowInput = $allowInput;
        $this->allowClear = $allowClear;
        $this->note = $note;
        $this->inModal = $inModal;
        $this->timeOnly = $timeOnly;
        $this->placeholder = $placeholder;
        $this->required = $required;
        $this->time24Hr = $time24Hr;
        $this->altFormat = $altFormat;
        $this->hourIncrement = $hourIncrement;
        $this->minuteIncrement = $minuteIncrement;
    }

    public function render()
    {
        return view('twill::partials.form._date_picker');
    }
}
