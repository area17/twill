<?php

namespace A17\Twill\Tests\Integration\Controllers\Tables;

use A17\Twill\Exceptions\ColumnMissingPropertyException;
use A17\Twill\Models\Model;
use A17\Twill\Services\Listings\Columns\Text;
use A17\Twill\Tests\Integration\ModulesTestBase;

class TextAndColumnBaseTest extends ModulesTestBase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->author = $this->createAuthor();
    }

    public function testMissingData(): void
    {
        $column = Text::make();

        $this->assertEquals('', $column->renderCell($this->author));

        $this->expectException(ColumnMissingPropertyException::class);
        $column->getKey();
    }

    public function testDefaults(): void
    {
        $column = Text::make()->field('name');

        $this->assertEquals('name', $column->getKey());
        $this->assertEquals('name', $column->getSortKey());
        $this->assertEquals('Name', $column->toColumnArray()['label']);
        $this->assertTrue(true, $column->toColumnArray()['visible']);
        $this->assertFalse($column->toColumnArray()['optional']);
        $this->assertFalse($column->toColumnArray()['sortable']);
        $this->assertFalse($column->toColumnArray()['html']);

        $this->assertFalse($column->isDefaultSort());
    }

    public function testLinkCell(): void
    {
        $column = Text::make()->field('name')->linkCell('https://twillcms.com');

        $html = $column->renderCell($this->author);

        $this->assertEquals(
            '<a href="https://twillcms.com" data-edit="false">' . $this->author->name . '</a>',
            str_replace([PHP_EOL, '    '], null, $html)
        );
    }

    public function testLinkWithClosure(): void
    {
        $column = Text::make()->field('name')->linkCell(function (Model $model) {
            return 'https://twillcms.com';
        });

        $html = $column->renderCell($this->author);

        $this->assertEquals(
            '<a href="https://twillcms.com" data-edit="false">' . $this->author->name . '</a>',
            str_replace([PHP_EOL, '    '], null, $html)
        );
    }

    public function testDefaultSort(): void
    {
        // @todo: Check missing implementation?
        $column = Text::make()->field('name')->sortByDefault();

        $this->assertTrue($column->isDefaultSort());
    }

    public function testSetters(): void
    {
        $column = Text::make()->field('name');

        $this->assertEquals('name', $column->getKey());
        $this->assertEquals('Name', $column->toColumnArray()['label']);
        $this->assertEquals('name', $column->getSortKey());
        $this->assertTrue($column->toColumnArray()['visible']);
        $this->assertFalse($column->toColumnArray()['sortable']);
        $this->assertFalse($column->toColumnArray()['html']);
        $this->assertFalse($column->toColumnArray()['optional']);

        $column->title('New Title');
        $this->assertEquals('name', $column->getKey());
        $this->assertEquals('New Title', $column->toColumnArray()['label']);

        $column->hide();
        $this->assertFalse($column->toColumnArray()['visible']);

        $column->sortable(false);
        $this->assertFalse($column->toColumnArray()['sortable']);

        $column->renderHtml();
        $this->assertTrue($column->toColumnArray()['html']);

        $column->optional();
        $this->assertTrue($column->toColumnArray()['optional']);

        $column->sortKey('custom_sort_key');
        $this->assertEquals('custom_sort_key', $column->getSortKey());
    }

    public function testBasicTextField(): void
    {
        $column = Text::make()->field('name');

        $this->assertEquals($this->author->name, $column->renderCell($this->author));
    }

    public function testTextFieldCustomRenderer(): void
    {
        $column = Text::make()->field('name')->renderHtml()->customRender(function (Model $model) {
            return 'CUSTOM:' . $model->name;
        });

        $this->assertEquals('CUSTOM:' . $this->author->name, $column->renderCell($this->author));
    }
}
