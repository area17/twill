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
            ->boot()
            ->getModelClassName();

        $this->actingAs($this->superAdmin(), 'twill_users');

        $this->post(route('twill.servers.store'), ['title' => 'Test title',])
            ->assertStatus(200);

        $featured = $this->getJson(route('twill.servers.index'))
            ->json('tableData.0.featured');

        // Here we use a full fetch as there is a php 8, 8.1 difference regarding how booleans are casted. ('0' and 0).
        $this->assertEquals(0, (int)$featured);

        // Feature the content
        $this->putJson(route('twill.servers.feature'), ['id' => $class::latest()->first()->id, 'active' => false])
            ->assertJsonPath('message', 'Server featured!');

        $featured = $this->getJson(route('twill.servers.index'))
            ->json('tableData.0.featured');

        $this->assertEquals(1, (int)$featured);
    }
}
