<?php

namespace A17\Twill\Tests\Unit\MediaLibrary;

use A17\Twill\Tests\Unit\TestCase;
use A17\Twill\Services\MediaLibrary\TwicPicsParamsProcessor;

class TwicPicsParamsProcessorTest extends TestCase
{
    public function testProcessesCompatibleParams()
    {
        $processedParams = (new TwicPicsParamsProcessor)->process([
            'w' => '111',
            'h' => '222',
            'fm' => 'gif',
            'q' => '99',
        ]);

        $this->assertEquals([
            'output' => 'gif',
            'quality' => '99',
            'resize' => '111x222',
        ], $processedParams);
    }

    public function testProcessesFitCropValue()
    {
        $processedParams = (new TwicPicsParamsProcessor)->process([
            'w' => '111',
            'h' => '222',
            'fit' => 'crop',
        ]);

        $this->assertEquals([
            'crop' => '111x222',
        ], $processedParams);
    }

    public function testIgnoresOtherFitValues()
    {
        $processedParams = (new TwicPicsParamsProcessor)->process([
            'fit' => 'fill',
        ]);

        $this->assertEquals([
            'fit' => 'fill',
        ], $processedParams);
    }
}
