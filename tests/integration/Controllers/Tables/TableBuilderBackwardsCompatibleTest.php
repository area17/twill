<?php

namespace A17\Twill\Tests\Integration\Controllers\Tables;

use A17\Twill\Tests\Integration\Anonymous\AnonymousModule;
use A17\Twill\Tests\Integration\ModulesTestBase;

/**
 * This test specifically tests the old $indexColumn implementation.
 */
class TableBuilderBackwardsCompatibleTest extends ModulesTestBase
{
    private AnonymousModule $serverModule;

    public function setUp(): void
    {
        parent::setUp();
        $this->serverModule = AnonymousModule::make('servers', $this->app)
            ->withAdditionalProp('indexColumns', [
                'cover' => ['thumb' => true],
                'title' => [],
            ])
            ->boot();
    }

    public function testCmsArray(): void
    {
        $controller = $this->serverModule->getModelController();

        $indexData = invade($controller)->getIndexData();

        $this->assertEquals([
            [
                'name' => 'published',
                'label' => 'Published',
                'visible' => false,
                'optional' => true,
                'sortable' => true,
                'html' => false,
                'specificType' => null,
            ],
            [
                'name' => 'cover',
                'label' => 'cover',
                'visible' => true,
                'optional' => false,
                'sortable' => false,
                'html' => false,
                'specificType' => 'thumbnail',
                'variation' => 'square',
            ],
            [
                'name' => 'title',
                'label' => 'Title',
                'visible' => true,
                'optional' => false,
                'sortable' => false,
                'html' => false,
                'specificType' => null,
            ],
            [
                'name' => 'languages',
                'label' => 'Languages',
                'visible' => false,
                'optional' => true,
                'sortable' => false,
                'html' => false,
                'specificType' => null,
            ],
        ], $indexData['tableColumns']);
    }
}
