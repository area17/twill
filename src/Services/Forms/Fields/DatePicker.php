<?php

namespace A17\Twill\Services\Forms\Fields;

use A17\Twill\Services\Forms\Fields\Traits\hasPlaceholder;
use A17\Twill\Services\Forms\Fields\Traits\isTranslatable;

class DatePicker extends BaseFormField
{
    use isTranslatable;
    use hasPlaceholder;

    protected bool $withTime = true;
    protected bool $allowInput = false;
    protected bool $allowClear = false;
    protected bool $timeOnly = false;
    protected bool $time24h = false;
    protected ?string $altFormat = null;
    protected ?int $minuteIncrement = null;
    protected ?int $hourIncrement = null;

    public static function make(): static
    {
        return new self(
            component: \A17\Twill\View\Components\DatePicker::class,
            mandatoryProperties: ['name', 'label']
        );
    }

    /**
     * Hides the time picker.
     */
    public function withoutTime(bool $withoutTime = true): self
    {
        $this->withTime = !$withoutTime;

        return $this;
    }

    /**
     * Allows manual input.
     */
    public function allowInput(bool $allowInput = true): self
    {
        $this->allowInput = $allowInput;

        return $this;
    }

    /**
     * Allows to clear the input field.
     */
    public function allowClear(bool $allowClear = true): self
    {
        $this->allowClear = $allowClear;

        return $this;
    }

    /**
     * Makes it a time picker only.
     */
    public function timeOnly(bool $timeOnly = true): self
    {
        $this->withTime = true;
        $this->timeOnly = $timeOnly;
        $this->altFormat = $this->altFormat ?? (($this->time24h ?? false) ? 'H:i' : 'h:i K');

        return $this;
    }

    /**
     * If 24h format should be used.
     */
    public function time24h(bool $time24h = true): self
    {
        $this->time24h = $time24h;

        return $this;
    }

    /**
     * Define a custom date format.
     */
    public function altFormat(string $altFormat): self
    {
        $this->altFormat = $altFormat;

        return $this;
    }

    /**
     * Set how many hours are increment when using the + and - actions.
     */
    public function hourIncrement(int $hourIncrement = 1): self
    {
        $this->hourIncrement = $hourIncrement;

        return $this;
    }

    /**
     * Set how many minutes are increment when using the + and - actions.
     */
    public function minuteIncrement(int $minuteIncrement = 1): self
    {
        $this->minuteIncrement = $minuteIncrement;

        return $this;
    }
}
