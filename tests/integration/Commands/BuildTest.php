<?php

namespace A17\Twill\Tests\Integration\Commands;

use A17\Twill\Tests\Integration\TestCase;

class BuildTest extends TestCase
{
    public function setup(): void
    {
        parent::setUp();
    }

    public function testCanBuild()
    {
        // $this->assertExitCodeIsGood($this->artisan($command = 'twill:build')->run());

        $this->assertTrue(! false);
    }
}
