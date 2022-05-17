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

    public function withTime(bool $withTime = true): self
    {
        $this->withTime = $withTime;

        return $this;
    }

    public function allowInput(bool $allowInput = true): self
    {
        $this->allowInput = $allowInput;

        return $this;
    }

    public function allowClear(bool $allowClear = true): self
    {
        $this->allowClear = $allowClear;

        return $this;
    }

    public function timeOnly(bool $timeOnly = true): self
    {
        $this->withTime = true;
        $this->timeOnly = $timeOnly;
        $this->altFormat = $this->altFormat ?? (($this->time24Hr ?? false) ? 'H:i' : 'h:i K');

        return $this;
    }

    public function time24h(bool $time24h = true): self
    {
        $this->time24h = $time24h;

        return $this;
    }

    public function altFormat(string $altFormat): self
    {
        $this->altFormat = $altFormat;

        return $this;
    }

    public function hourIncrement(int $hourIncrement = 1): self
    {
        $this->hourIncrement = $hourIncrement;

        return $this;
    }

    public function minuteIncrement(int $minutIncrement = 1): self
    {
        $this->minuteIncrement = $minutIncrement;

        return $this;
    }
}
