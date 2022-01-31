<?php

namespace A17\Twill\View\Components;

class TimePicker extends DatePicker
{
    public function render()
    {
        $this->timeOnly = true;
        $this->withTime = true;
        $this->altFormat = $this->altFormat ?? (($this->time24Hr ?? false) ? 'H:i' : 'h:i K');
        return view('twill::partials.form._date_picker');
    }
}
