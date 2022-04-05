<?php

namespace A17\Twill\Tests\Integration\Commands;

use A17\Twill\Tests\Integration\TestCase;

class UpdateTest extends TestCase
{
    public function testCanExecuteUpdateCommand()
    {
        $this->assertExitCodeIsGood($this->artisan('twill:update')->run());
    }
}
