<?php

namespace A17\Twill\Services\MediaLibrary;

class TwicPicsParamsProcessor extends AbstractParamsProcessor
{
    protected $cropFit;

    public function finalizeParams()
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
                $this->params['crop'] = "{$this->width}x{$this->height}";
            } else {
                $this->params['resize'] = "{$this->width}x{$this->height}";
            }
        }

        return $this->params;
    }

    protected function handleParamFit($key, $value)
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
