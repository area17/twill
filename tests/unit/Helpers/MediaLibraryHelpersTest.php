<?php

namespace A17\Twill\Tests\Unit\Helpers;

use A17\Twill\Tests\Unit\TestCase;

class MediaLibraryHelpersTest extends TestCase
{
    public function testReplaceAccents()
    {
        $this->assertEquals('aeeiou', replaceAccents('àéèïôû'));
    }

    public function testSanitizeFilename()
    {
        $this->assertEquals('happy-paques-xo-png.jpg', sanitizeFilename('Happy_Pâques - XO.png.jpg'));
    }

}
