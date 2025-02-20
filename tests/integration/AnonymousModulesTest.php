<?php

namespace A17\Twill\Tests\Integration;

use A17\Twill\Tests\Integration\Anonymous\AnonymousModule;

class AnonymousModulesTest extends TestCase
{
    public function testCreateAndList(): void
    {
        AnonymousModule::make('anonymousmodules', $this->app)->boot();

        $this->actingAs($this->superAdmin(), 'twill_users');

        $this->getJson(route('twill.anonymousmodules.index'))
            ->assertJsonPath('tableData', []);

        $this->post(route('twill.anonymousmodules.store'), ['title' => 'Test title'])
            ->assertJsonPath('redirect', 'http://twill.test/twill/anonymousmodules/1/edit');

        $this->getJson(route('twill.anonymousmodules.index'))
            ->assertJsonPath('tableData.0.title', '<a href="http://twill.test/twill/anonymousmodules/1/edit" data-edit="true">Test title</a>');
    }
}
