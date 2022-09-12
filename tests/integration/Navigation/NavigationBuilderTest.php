<?php

namespace A17\Twill\Tests\Integration\Navigation;

use A17\Twill\Facades\TwillNavigation;
use A17\Twill\Tests\Integration\TestCase;

class NavigationBuilderTest extends TestCase
{
    public function testEmptyNavigation(): void
    {
        $navigation = TwillNavigation::buildNavigationTree();

        $this->assertEquals(['left' => [], 'right' => []], $navigation);
    }

    public function testNavigationWhenAuthenticated(): void {
        $this->login();

        $navigation = TwillNavigation::buildNavigationTree();

        $this->assertEmpty($navigation['left']);

        $this->assertCount(1, $navigation['right']);
        $this->assertEquals('Media Library', $navigation['right'][0]->getTitle());
    }
}
