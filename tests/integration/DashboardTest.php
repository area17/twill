<?php

namespace A17\Twill\Tests\Integration;

use A17\Twill\Tests\Integration\Anonymous\AnonymousModule;

class DashboardTest extends TestCase
{
    public AnonymousModule $servers;

    public AnonymousModule $licences;

    public function setUp(): void
    {
        parent::setUp();

        $this->servers = AnonymousModule::make('servers', $this->app)->boot();
        $this->licences = AnonymousModule::make('licences', $this->app)->boot();

        $this->actingAs($this->superAdmin(), 'twill_users');
    }

    public function testLogsActivity(): void
    {
        $this->post(route('twill.servers.store'), ['title' => 'Test title'])
            ->assertJsonPath('redirect', 'http://twill.test/twill/servers/1/edit');

        $this->post(route('twill.licences.store'), ['title' => 'Test title'])
            ->assertJsonPath('redirect', 'http://twill.test/twill/licences/1/edit');
    }
}
