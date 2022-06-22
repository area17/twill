<?php

namespace A17\Twill\Tests\Integration\Controllers\Tables;

use A17\Twill\Exceptions\ColumnMissingPropertyException;
use A17\Twill\Services\Listings\Columns\Browser;
use A17\Twill\Tests\Integration\ModulesTestBase;
use App\Models\Category;

class BrowserColumnTest extends ModulesTestBase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->author = $this->createAuthor();
    }

    public function testWithoutData(): void
    {
        $column = Browser::make()->field('title')->browser('categories');

        $this->assertEquals('', $column->renderCell($this->author));
    }

    public function testWithSingleValue(): void
    {
        $category = $this->createCategory();

        $this->author->saveRelated(
            [
                [
                    'id' => $category->id,
                    'endpointType' => Category::class,
                ],
            ],
            'categories'
        );

        $column = Browser::make()->field('title')->browser('categories');

        $this->assertEquals($category->title, $column->renderCell($this->author));
    }

    public function testWithMultipleValues(): void
    {
        $category = $this->createCategory();
        $category2 = $this->createCategory();

        $this->author->saveRelated(
            [
                [
                    'id' => $category->id,
                    'endpointType' => Category::class,
                ],
                [
                    'id' => $category2->id,
                    'endpointType' => Category::class,
                ],
            ],
            'categories'
        );

        $column = Browser::make()->field('title')->browser('categories');

        $this->assertEquals($category->title . ', ' . $category2->title, $column->renderCell($this->author));
    }

    public function testExceptionWhenMissingBrowser(): void
    {
        $this->expectException(ColumnMissingPropertyException::class);
        $this->expectExceptionMessage('Browser column missing browser value: categories');

        Browser::make()->field('categories')->renderCell($this->author);
    }
}
