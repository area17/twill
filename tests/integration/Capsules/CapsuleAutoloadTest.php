<?php

namespace A17\Twill\Tests\Integration\Capsules;

use A17\Twill\Tests\Integration\TestCase;
use App\Twill\Capsules\Posts\PostsCapsuleServiceProvider;

class CapsuleAutoloadTest extends TestCase
{
    public ?string $example = 'tests-capsules';

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
