<?php

namespace A17\Twill\Tests\Integration\Settings;

use A17\Twill\Exceptions\Settings\SettingsGroupDoesNotExistException;
use A17\Twill\Exceptions\Settings\SettingsSectionDoesNotExistException;
use A17\Twill\Facades\TwillAppSettings;
use A17\Twill\Models\AppSetting;
use A17\Twill\Services\Settings\SettingsGroup;
use A17\Twill\Tests\Integration\TestCase;

class SettingsFacadeTest extends TestCase
{

    public ?string $example = 'tests-settings';

    public function setUp(): void
    {
        TwillAppSettings::shouldReceive('settingsAreEnabled')
            ->andReturnTrue();
        parent::setUp();

        // Create the settings group.
        TwillAppSettings::registerSettingsGroup(
            $group = SettingsGroup::make()
                ->name('test')
                ->label('Site settings')
        );

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
                                'label' => 'Non translated.',
                            ],
                            'blocks' => [
                                'default' => [
                                    [
                                        'id' => time(),
                                        'content' => [
                                            'title' => [
                                                'en' => 'Title english.'
                                            ]
                                        ],
                                        'editor_name' => 'blocks-' . $model->blocks[0]->id . '|default',
                                        'type' => 'a17-block-text',
                                        'is_repeater' => false,
                                    ],
                                ]
                            ]

                        ],
                    ],
                ]
            )
            ->assertStatus(200);

        /** @see AppSetting::booted() */
        $model->unsetRelation('blocks');
    }

    public function testTranslatedSettingsGetter(): void
    {
        $this->assertEquals('English title!', TwillAppSettings::getTranslated('test.test.title'));
    }

    public function testSettingsGetter(): void
    {
        $this->assertEquals('Non translated.', TwillAppSettings::get('test.test.label'));
    }

    public function testCanRenderNestedSettingsBlock(): void
    {
        $output = TwillAppSettings::getBlockServiceForGroupAndSection('test', 'test')
            ->renderData
            ->renderChildren('default');

        $this->assertEquals('Hi, I am the text block rendered!' . PHP_EOL, $output);
    }

    public function testGetInvalidGroup(): void
    {
        $this->expectException(SettingsGroupDoesNotExistException::class);
        TwillAppSettings::getGroupForName('someRandomName');
    }

    public function testGetInvalidSection(): void
    {
        $this->expectException(SettingsSectionDoesNotExistException::class);
        TwillAppSettings::getGroupDataForSectionAndName('test', 'someRandomSection');
    }
}
