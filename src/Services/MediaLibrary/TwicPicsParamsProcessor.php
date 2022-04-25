<?php

namespace A17\Twill\Services\MediaLibrary;

class TwicPicsParamsProcessor extends AbstractParamsProcessor
{
    protected $cropFit;

    public function finalizeParams(): ?array
    {
        if ($this->format) {
            $this->params['output'] = $this->format;
        }

        if ($this->quality) {
            $this->params['quality'] = $this->quality;
        }

        if ($this->width || $this->height) {
            $this->width = $this->width ?: '-';
            $this->height = $this->height ?: '-';

            if ($this->cropFit) {
                $this->params['crop'] = sprintf('%sx%s', $this->width, $this->height);
            } else {
                $this->params['resize'] = sprintf('%sx%s', $this->width, $this->height);
            }
        }

        return $this->params;
    }

    protected function handleParamFit($key, $value): void
    {
        if ($value !== 'crop') {
            return;
        }

        if (isset($this->params['crop'])) {
            return;
        }

        $this->cropFit = true;

        unset($this->params[$key]);
    }
}
