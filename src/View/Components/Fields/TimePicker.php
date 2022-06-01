<?php

namespace A17\Twill\View\Components\Fields;

use Illuminate\Contracts\View\View;

class TimePicker extends DatePicker
{
    public function render(): View
    {
        $this->timeOnly = true;
        $this->withTime = true;
        $this->altFormat = $this->altFormat ?? (($this->time24Hr ?? false) ? 'H:i' : 'h:i K');
        return view('twill::partials.form._date_picker', $this->data());
    }
}
