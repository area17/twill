<?php

namespace A17\Twill\Tests\Integration\Controllers\Tables;

use A17\Twill\Http\Controllers\Admin\ModuleController;
use A17\Twill\Services\Listings\Columns\Text;
use A17\Twill\Services\Listings\TableColumns;
use A17\Twill\Services\Listings\TableDataContext;
use A17\Twill\Tests\Integration\Anonymous\AnonymousModule;
use A17\Twill\Tests\Integration\ModulesTestBase;

class TableBuilderTest extends ModulesTestBase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->author = $this->createAuthor();
    }

    public function testCmsArray(): void
    {
        $builder = new TableColumns();
        $builder->add(Text::make()->field('name'));

        $this->assertEquals([
            [
                'name' => 'name',
                'label' => 'Name',
                'visible' => true,
                'optional' => false,
                'sortable' => false,
                'html' => false,
                'specificType' => null,
                'shrink' => false,
            ],
        ], $builder->toCmsArray(request()));
    }

    public function testBasicTableBuilder(): void
    {
        $builder = new TableColumns();
        $builder->add(Text::make()->field('name'));

        $this->assertCount(1, $builder);

        $this->assertEquals(['name' => $this->author->name], $builder->getArrayForModel($this->author));
    }

    public function testModelBrowserContext(): void
    {
        $builder = new TableColumns();
        $builder->add(Text::make()->field('name'));
        $context = new TableDataContext('name', 'id', 'authors', 'personnel', 'author', true, []);
        $this->assertEquals(
            [
                'name' => $this->author->name,
                'id' => $this->author->id,
                'edit' => 'http://twill.test/twill/personnel/authors/1/edit',
                'endpointType' => 'author',
                'thumbnail' => 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7',
                'repeaterFields' => [],
            ],
            $builder->getArrayForModelBrowser($this->author, $context)
        );
    }

    public function testAdditionalIndexTableColumns(): void
    {
        $module = AnonymousModule::make('TableBuilderAuthors', $this->app)
            ->withAdditionalTableColumns(
                TableColumns::make([
                    Text::make()->field('description')->title('Description')->linkToEdit(),
                ])
            )
            ->boot();

        $columns = invade($module->getModelController())->getTableColumns('index');

        $this->assertEquals('description', $columns[2]->getKey());
    }
}
