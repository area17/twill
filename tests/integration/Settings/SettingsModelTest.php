<?php

namespace A17\Twill\Tests\Integration\Settings;

use A17\Twill\Facades\TwillAppSettings;
use A17\Twill\Models\AppSetting;
use A17\Twill\Services\Settings\SettingsGroup;
use A17\Twill\Tests\Integration\TestCase;

class SettingsModelTest extends TestCase
{
    public $example = 'tests-settings';

    public function setUp(): void
    {
        TwillAppSettings::shouldReceive('settingsAreEnabled')
            ->andReturnTrue();
        parent::setUp();
    }

    public function testSettingsRegistration(): void
    {
        TwillAppSettings::registerSettingsGroup(
            SettingsGroup::make()
                ->name('test')
                ->label('Test label')
                ->description('Test description')
        );

        TwillAppSettings::registerSettingsGroup(
            SettingsGroup::make()
                ->name('demo')
                ->label('Test 2 label')
                ->description('Test 2 description')
        );

        // When we open up the controller it should auto register it and settings should be in the menu.
        $this->actingAs($this->superAdmin(), 'twill_users')->getJson(route('twill.app.settings'))
            ->assertSee('Settings')
            ->assertSee('Test label')
            ->assertSee('Test description')
            // Second.
            ->assertSee('Test 2 label')
            ->assertSee('Test 2 description');
    }

    public function testBooting(): void
    {
        $this->assertTrue(AppSetting::where('name', 'test')->doesntExist());

        TwillAppSettings::registerSettingsGroup(
            SettingsGroup::make()
                ->name('test')
                ->label('Test label')
                ->description('Test description')
        );

        $this->assertTrue(AppSetting::where('name', 'test')->doesntExist());

        TwillAppSettings::getGroupForName('test')->boot();

        $this->assertTrue(AppSetting::where('name', 'test')->exists());
    }

    public function testSettingsPage(): void
    {
        TwillAppSettings::registerSettingsGroup(
            SettingsGroup::make()
                ->name('test')
                ->label('Test label')
                ->description('Test description')
        );

        $this->actingAs($this->superAdmin(), 'twill_users')
            ->getJson(route('twill.app.settings.page', ['group' => 'test']))
            ->assertSee('title field label');
    }

    public function testSettingsUpdate(): void
    {
        TwillAppSettings::registerSettingsGroup(
            $group = SettingsGroup::make()
                ->name('test')
                ->label('Test label')
                ->description('Test description')
        );

        // Manually boot it just for the test.
        $group->boot();
        $model = $group->getSettingsModel();

        // Make a post.
        $this->actingAs($this->superAdmin(), 'twill_users')
            ->putJson(
                route('twill.app.settings.update', [$model]),
                [
                    'blocks' => [
                        [
                            'id' => $model->blocks[0]->id,
                            'editor_name' => 'test',
                            'type' => 'a17-block-appSettings-test-test',
                            'content' => [
                                'title' => [
                                    'en' => 'English title!',
                                ],
                            ],

                        ],
                    ],
                ]
            )
            ->assertStatus(200);

        $model->refresh();

        $this->assertEquals('English title!', $model->blocks[0]->content['title']['en']);
    }
}
