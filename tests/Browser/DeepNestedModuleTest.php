<?php

namespace A17\Twill\Tests\Browser;

use Laravel\Dusk\Browser;

class DeepNestedModuleTest extends BrowserTestCase
{
    public ?string $example = 'tests-deep-nested';

    public function testCanVisitDeeplyNestedModules(): void {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->superAdmin, 'twill_users');
            $browser->visitTwill();

            $browser->createModuleEntryWithTitle('Client', 'PrimaryClient');
            $browser->createModuleEntryWithTitle('Client', 'SecondaryClient');

            $browser->clickLink('Clients');

            $browser->inRow('SecondaryClient', function(Browser $browser) {
                // Clicklink does not work "within" another element for some reason.
                // $browser->clickLink('0 children');
                $browser->click('td:nth-of-type(5) a');
            });

            $browser->assertPathIs('/twill/clients/2/projects');

            $browser->createWithTitle('SecondaryClientProject');

            $browser->assertPathIs('/twill/clients/2/projects/1/edit');

            $browser->visit('/twill/clients/2/projects');

            $browser->inRow('SecondaryClientProject', function(Browser $browser) {
                $browser->click('td:nth-of-type(5) a');
            });

            $browser->assertPathIs('/twill/clients/2/projects/1/applications');

            $browser->createWithTitle('SecondaryClientProjectApplication');

            $browser->assertPathIs('/twill/clients/2/projects/1/applications/1/edit');
        });
    }
}
