<?php

namespace A17\Twill\Tests\Integration\Controllers\Tables;

use A17\Twill\Services\Listings\Columns\FeaturedStatus;
use A17\Twill\Services\Listings\TableColumns;
use A17\Twill\Tests\Integration\Anonymous\AnonymousModule;
use A17\Twill\Tests\Integration\TestCase;

class FeaturedColumnTest extends TestCase
{
    public function testColumn(): void
    {
        $column = FeaturedStatus::make();

        $this->assertEquals('featured', $column->getKey());
    }

    public function testFeatureInListing(): void
    {
        $class = AnonymousModule::make('servers', $this->app)
            ->withFields([
                'title' => [],
                'featured' => ['type' => 'boolean', 'default' => false],
            ])
            ->withSetupMethods([
                'enableFeature',
            ])
            ->withTableColumns(
                TableColumns::make([
                    FeaturedStatus::make(),
                ])
            )
            ->boot();

        $this->actingAs($this->superAdmin(), 'twill_users');

        $this->post(route('twill.servers.store'), ['title' => 'Test title',])
            ->assertStatus(200);

        $this->getJson(route('twill.servers.index'))
            ->assertJsonPath('tableData.0.featured', 0);

        // Feature the content
        $this->putJson(route('twill.servers.feature'), ['id' => $class::latest()->first()->id, 'active' => false])
            ->assertJsonPath('message', 'Server featured!');

        $this->getJson(route('twill.servers.index'))
            ->assertJsonPath('tableData.0.featured', 1);
    }
}
