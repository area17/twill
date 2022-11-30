<?php

namespace A17\Twill\Tests\Integration;

use Illuminate\Support\Facades\DB;
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

    public function testWithSlugs(): void
    {
        AnonymousModule::make('anonymousmodulesslugs', $this->app)
            ->withFields([
                'title' => ['translatable' => true],
            ])
            ->withSlugs()
            ->boot();

        $this->actingAs($this->superAdmin(), 'twill_users');

        $this->post(route('twill.anonymousmodulesslugs.store'), ['title' => ['en' => 'Test title']]);

        $this->assertEquals(DB::table('anonymousmodulesslug_slugs')->first()->slug, 'test-title');
    }
}
