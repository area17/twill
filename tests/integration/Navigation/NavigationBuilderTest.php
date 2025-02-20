<?php

namespace A17\Twill\Tests\Integration\Navigation;

use A17\Twill\Exceptions\Navigation\CannotCombineNavigationBuilderWithLegacyConfig;
use A17\Twill\Facades\TwillNavigation;
use A17\Twill\Tests\Integration\TestCase;
use A17\Twill\View\Components\Navigation\NavigationLink;

class NavigationBuilderTest extends TestCase
{
    public function testEmptyNavigation(): void
    {
        $navigation = TwillNavigation::buildNavigationTree();

        $this->assertEquals(['left' => [], 'right' => []], $navigation);
    }

    public function testNavigationWhenAuthenticated(): void
    {
        $this->login();

        $navigation = TwillNavigation::buildNavigationTree();

        $this->assertEmpty($navigation['left']);

        $this->assertCount(1, $navigation['right']);
        $this->assertEquals('Media Library', $navigation['right'][0]->getTitle());
    }

    public function testAddLinkToNavigation(): void
    {
        $this->login();

        TwillNavigation::addLink(NavigationLink::make()->forRoute('twill.users')->title('USERS'));

        $navigation = TwillNavigation::buildNavigationTree();

        $this->assertEquals('USERS', $navigation['left'][0]->getTitle());
    }

    public function testNavigationLinkWithRouteArguments(): void
    {
        $this->login();

        $link = NavigationLink::make()
            ->forRoute('twill.users.update', ['user' => $this->superAdmin()->id])
            ->title('Edit admin');

        $this->assertStringContainsString('/twill/users/' . $this->superAdmin()->id, $link->render());
    }

    public function testTargetBlank(): void
    {
        $this->login();

        $link = NavigationLink::make()->forRoute('twill.users.index')->title('USERS');

        $this->assertStringNotContainsString('target="_blank"', $link->render());

        // Second time we say it should open in a new window.
        $link = NavigationLink::make()->forRoute('twill.users.index')->title('USERS')->shouldOpenInNewWindow();

        $this->assertStringContainsString('target="_blank"', $link->render());
    }

    public function testCannotCombineLegacyWithNavigationBuilder(): void
    {
        config()->set('twill-navigation', ['some-data']);

        $this->expectException(CannotCombineNavigationBuilderWithLegacyConfig::class);
        TwillNavigation::addLink(NavigationLink::make()->forRoute('twill.users')->title('USERS'));
    }

}
