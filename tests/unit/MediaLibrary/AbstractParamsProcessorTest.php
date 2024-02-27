<?php

namespace A17\Twill\Tests\Unit\MediaLibrary;

use A17\Twill\Services\MediaLibrary\AbstractParamsProcessor;
use A17\Twill\Tests\Unit\TestCase;

class AbstractParamsProcessorTest extends TestCase
{
    public function testProcessesCompatibleParams()
    {
        $processedParams = (new DummyParamsProcessor)->process([
            'w' => '111',
            'h' => '222',
            'fm' => 'gif',
            'q' => '99',
            'fit' => 'crop',
        ]);

        $this->assertEquals([
            'width' => '111',
            'height' => '222',
            'format' => 'gif',
            'quality' => '99',
            'fit' => 'crop',
        ], $processedParams);
    }

    public function testPreservesUnknownParams()
    {
        $processedParams = (new DummyParamsProcessor)->process([
            'w' => '111',
            'h' => '222',
            'unknown' => 'zzz',
        ]);

        $this->assertEquals([
            'width' => '111',
            'height' => '222',
            'unknown' => 'zzz',
        ], $processedParams);
    }

    public function testSupportsCustomHandlers()
    {
        $processedParams = (new DummyParamsProcessor)->process([
            'custom' => 'this_is_a_custom_value',
        ]);

        $this->assertEquals([
            'custom' => 'THIS_IS_A_CUSTOM_VALUE',
        ], $processedParams);
    }
}

/**
 * DummyParamsProcessor simply returns the processed params with modified key names.
 */
class DummyParamsProcessor extends AbstractParamsProcessor
{
    protected $custom;

    public function finalizeParams()
    {
        return collect(
            $this->params + [
                'width' => $this->width,
                'height' => $this->height,
                'format' => $this->format,
                'quality' => $this->quality,
                'fit' => $this->fit,
                'custom' => $this->custom,
            ]
        )->filter()->toArray();
    }

    public function handleParamCUSTOM($key, $value)
    {
        $this->custom = strtoupper($value);

        unset($this->params['custom']);
    }
}
