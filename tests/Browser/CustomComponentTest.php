<?php

namespace A17\Twill\Tests\Browser;

use App\Repositories\ContactPageRepository;
use Laravel\Dusk\Browser;

class CustomComponentTest extends BrowserTestCase
{
    public ?string $example = 'tests-singleton';

    public function setUp(): void
    {
        parent::setUp();

        // Run the seed manually as it does not work inside dusk.
        app(ContactPageRepository::class)->create([
            'title' => [
                'en' => 'Lorem ipsum',
                'fr' => 'Nullam elementum',
            ],
            'description' => [
                'en' => 'Lorem ipsum dolor sit amet',
                'fr' => 'Nullam elementum sed velit',
            ],
            'active' => [
                'en' => true,
                'fr' => true,
            ],
            'published' => true,
        ]);
    }

    public function testBeforeBuild(): void
    {
        $this->artisan('twill:build', ['--noInstall' => true])
            ->expectsOutputToContain('Compiled views cleared successfully.')
            ->assertSuccessful();

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->superAdmin, 'twill_users');

            $browser->visit('/twill');
            $browser->clickLink('Contact Page');
            $browser->assertSee('This is the ContactPage form');
            $browser->assertDontSee('Content from custom helloWorld vue component');
        });
    }

    public function testWithBuildingCustomComponent(): void
    {
        $path = implode(DIRECTORY_SEPARATOR, [
            realpath(self::getBasePathStatic()),
            'resources',
            'assets',
            'js',
            'components',
        ]);

        $this->artisan('twill:build', ['--noInstall' => true, '--customComponentsSource' => $path])
            ->expectsOutputToContain('Compiled views cleared successfully.')
            ->assertSuccessful();

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->superAdmin, 'twill_users');

            $browser->visit('/twill');
            $browser->clickLink('Contact Page');
            $browser->assertSee('This is the ContactPage form');
            $browser->assertSee('Content from custom helloWorld vue component');
        });
    }
}
