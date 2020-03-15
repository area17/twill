<?php

namespace A17\Twill\Tests\Integration;

class UpdateTest extends TestCase
{
    public function testCanExecuteUpdateCommand()
    {
        $this->artisan('twill:update');

        $this->assertTrue(true);
    }
}
