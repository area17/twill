<?php

namespace A17\Twill\Tests\Integration;

use App\Twill\Capsules\Posts\PostsCapsuleServiceProvider;

class CapsuleAutoloadTest extends TestCase
{
    public $example = 'tests-capsules';

    public function testStubCapsuleIsLoaded(): void
    {
        $this->assertTrue(class_exists(
            "App\Twill\Capsules\Posts\Models\Post"
        ));
    }

    public function testServiceProviderIsBooted(): void
    {
        $this->assertTrue(PostsCapsuleServiceProvider::$isBooted);
    }
}
