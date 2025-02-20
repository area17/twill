<?php

namespace A17\Twill\Tests\Integration\Navigation;

use A17\Twill\Facades\TwillNavigation;
use A17\Twill\Tests\Integration\TestCase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class LegacyNavigationTest extends TestCase
{
    public function testNavItemIsNotVisibleIfNotAuthorized(): void
    {
        config()->set('twill-navigation', [
            'pages' => [
                'module' => true,
                'can' => 'see-nav-item',
            ]
        ]);

        $this->login();

        $navigation = TwillNavigation::buildNavigationTree();

        $this->assertFalse(Auth::user()->can('see-nav-item'));
        $this->assertEmpty($navigation['left']);
    }

    public function testNavItemIsVisibleIfAuthorized(): void
    {
        config()->set('twill-navigation', [
            'pages' => [
                'module' => true,
                'can' => 'see-nav-item',
            ]
        ]);

        Gate::define('see-nav-item', function () {
            return true;
        });

        $this->login();

        $navigation = TwillNavigation::buildNavigationTree();

        $this->assertTrue(Auth::user()->can('see-nav-item'));
        $this->assertNotEmpty($navigation['left']);
        $this->assertEquals('Pages', $navigation['left'][0]->getTitle());
    }
}
