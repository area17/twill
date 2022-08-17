<?php

namespace A17\Twill\Tests\Browser;

use Laravel\Dusk\Browser;

class CreateDialogTest extends BrowserTestCase
{
    /**
     * @see https://github.com/area17/twill/issues/1756
     */
    public function testCanCreateWhenFillingSecondaryLanguageFirst(): void
    {
        $class = null;
        $this->tweakApplication(function () use (&$class) {
            \Illuminate\Support\Facades\Config::set('translatable.locales', ['en', 'fr']);
            $class = \A17\Twill\Tests\Integration\Anonymous\AnonymousModule::make('servers', app())
                ->withFields([
                    'title' => ['translatable' => true],
                ])
                ->boot()
                ->getModelClassName();
        });

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->superAdmin, 'twill_users');

            $browser->visitTwill();

            $browser->clickLink('Servers');
            $browser->press('Add new');

            $browser->waitFor('.modal__header');

            $browser->assertDisabled('create');

            $browser->press('FR');
            $browser->type('title[fr]', 'French title');

            $browser->press('EN');
            $browser->type('title[en]', 'Some Server');
            $browser->press('Create');

            $browser->waitForReload();

            $browser->assertSee('Update');
            $browser->assertSee('Some Server');
            $browser->assertDontSee('French title');

            $browser->press('FR');
            $browser->assertSee('French title');
            $browser->assertDontSee('Some Server');
        });
    }
}
