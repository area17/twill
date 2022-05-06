<?php

namespace A17\Twill\Tests\Unit;

/**
 * Tests Twill specific macros.
 */
class MacrosTest extends TestCase
{
    public function testDoesntContainPolyfill(): void
    {
        $this->assertTrue(collect(['a' => 'a'])->doesntContain('b'));
    }
}
