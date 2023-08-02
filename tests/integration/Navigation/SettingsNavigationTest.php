<?php

namespace A17\Twill\Tests\Integration\Navigation;

use A17\Twill\Facades\TwillAppSettings;
use A17\Twill\Facades\TwillNavigation;
use A17\Twill\Services\Settings\SettingsGroup;
use A17\Twill\Tests\Integration\TestCase;

class SettingsNavigationTest extends TestCase
{
    public function testAddSettingAppearsInNavigation(): void
    {
        $this->login();

        TwillAppSettings::registerSettingsGroup(
            SettingsGroup::make()->name('test')
        );

        $navigation = TwillNavigation::buildNavigationTree();

        $this->assertEmpty($navigation['left']);

        $this->assertCount(2, $navigation['right']);
        $this->assertEquals('Settings', $navigation['right'][0]->getTitle());

        $this->assertCount(1, $navigation['right'][0]->getChildren());
        $this->assertEquals('Test', $navigation['right'][0]->getChildren()[0]->getTitle());
    }

    public function testAddSettingWithoutAutoRegisterDoesNotAppearInMenu(): void
    {
        $this->login();

        TwillAppSettings::registerSettingsGroup(
            SettingsGroup::make()
                ->name('test')
                ->doNotAutoRegisterMenu()
        );

        $this->assertSettingsIsNotInNavgationTree(TwillNavigation::buildNavigationTree());
    }

    public function testUnavailableSettingsDontShowInTree(): void
    {
        $this->login();

        TwillAppSettings::registerSettingsGroup(
            SettingsGroup::make()
                ->name('test')
                ->availableWhen(fn() => false)
        );

        $this->assertSettingsIsNotInNavgationTree(TwillNavigation::buildNavigationTree());
    }

    public function testAvailableSettingsShowInTree(): void
    {
        $this->login();

        TwillAppSettings::registerSettingsGroup(
            SettingsGroup::make()
                ->name('test')
                ->availableWhen(fn() => true)
        );

        $navigation = TwillNavigation::buildNavigationTree();

        $this->assertEmpty($navigation['left']);

        $this->assertCount(2, $navigation['right']);
        $this->assertEquals('Settings', $navigation['right'][0]->getTitle());

        $this->assertCount(1, $navigation['right'][0]->getChildren());
        $this->assertEquals('Test', $navigation['right'][0]->getChildren()[0]->getTitle());
    }

    protected function assertSettingsIsNotInNavgationTree(array $navigationTree): void {
        $this->assertCount(1, $navigationTree['right']);
        // The only link is the media library one.
        $this->assertEquals('Media Library', $navigationTree['right'][0]->getTitle());
    }
}
