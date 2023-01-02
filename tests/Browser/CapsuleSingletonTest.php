<?php

namespace A17\Twill\Tests\Browser;

use Laravel\Dusk\Browser;

class CapsuleSingletonTest extends BrowserTestCase
{
    public ?string $example = 'tests-modules';

    public function testCapsuleAutoSeeds(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->superAdmin, 'twill_users');
            $browser->visitTwill();
            $browser->clickLink('Homepage');
            $browser->assertDontSee('\Database\Seeders\HomepageSeeder is missing');
        });
    }
}
