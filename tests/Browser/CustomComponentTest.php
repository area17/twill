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
        $this->artisan('twill:update')
            ->expectsConfirmation('Do you want to run any pending database migrations now?', 'no');
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

        $this->assertFileExists($path . DIRECTORY_SEPARATOR . 'HelloWorld.vue');

        $this->artisan('twill:build', ['--install' => false, '--customComponentsSource' => $path])
            ->expectsConfirmation('Do you want to run any pending database migrations now?', 'no');

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->superAdmin, 'twill_users');

            $browser->visit('/twill');
            $browser->clickLink('Contact Page');
            $browser->assertSee('This is the ContactPage form');
            $browser->assertSee('Content from custom helloWorld vue component');
        });
    }
}
