<?php

namespace A17\Twill\Tests\Unit\Models;

use A17\Twill\Models\Media;
use A17\Twill\Tests\Unit\TestCase;

class MediaTest extends TestCase
{
    public function testAltText()
    {
        $m = new Media();

        $this->assertEquals("Happy Holidays", $m->altTextFrom('Happy_Holidays.jpg'));
        $this->assertEquals("Happy Holidays", $m->altTextFrom('Happy_Holidays@2x.jpg'));
        $this->assertEquals("J'aime la pièce", $m->altTextFrom('J\'aime-la-pièce.jpg'));
    }
}
