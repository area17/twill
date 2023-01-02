<?php

namespace A17\Twill\Tests\Integration\Capsules;

use A17\Twill\Tests\Integration\TestCase;

class CapsuleSingletonTest extends TestCase
{
    public $example = 'tests-capsules';

    public function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    public function testSingletonIsAutomaticallySeeded(): void
    {
        $this->httpRequestAssert('/twill/homepage', 'GET', [], 200);
        $this->assertSee("\Database\Seeders\HomepageSeeder");
    }
}
