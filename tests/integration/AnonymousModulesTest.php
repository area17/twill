<?php

namespace A17\Twill\Tests\Integration;

use A17\Twill\Tests\Integration\Anonymous\AnonymousModule;

class AnonymousModulesTest extends TestCase
{

    public function testCreateAndList(): void
    {
        AnonymousModule::make('servers', $this->app)->boot();

        $this->actingAs($this->superAdmin(), 'twill_users');

        $this->getJson(route('twill.servers.index'))
            ->assertJsonPath('tableData', []);

        $this->post(route('twill.servers.store'), ['title' => 'Test title',])
            ->assertJsonPath('redirect', 'http://twill.test/twill/servers/1/edit');

        $this->getJson(route('twill.servers.index'))
            ->assertJsonPath('tableData.0.title', '<a href="http://twill.test/twill/servers/1/edit">Test title</a>');
    }
}
