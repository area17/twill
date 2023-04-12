<?php

namespace A17\Twill\Tests\Integration\Controllers\Tables;

use A17\Twill\Exceptions\ColumnMissingPropertyException;
use A17\Twill\Services\Listings\Columns\Relation;
use A17\Twill\Tests\Integration\ModulesTestBase;

class RelationColumnTest extends ModulesTestBase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->author = $this->createAuthor();
    }

    public function testKey(): void {
        $column = Relation::make()->field('title')->relation('categories');

        $this->assertEquals('categoriesTitle', $column->getKey());
    }

    public function testWithoutData(): void
    {
        $column = Relation::make()->field('title')->relation('categories');

        $this->assertEquals('', $column->renderCell($this->author));
    }

    public function testWithSingleValue(): void
    {
        $category = $this->createCategory();

        $this->author->categories()->attach($category);

        $column = Relation::make()->field('title')->relation('categories');

        $this->assertEquals($category->title, $column->renderCell($this->author));
    }

    public function testWithMultipleValues(): void
    {
        $category = $this->createCategory();
        $category2 = $this->createCategory();

        $this->author->categories()->attach([$category->id, $category2->id]);
        $column = Relation::make()->field('title')->relation('categories');

        $this->assertEquals($category->title . ', ' . $category2->title, $column->renderCell($this->author));
    }

    public function testExceptionWhenMissingBrowser(): void
    {
        $this->expectException(ColumnMissingPropertyException::class);
        $this->expectExceptionMessage('Relation column missing relation value: categories');

        Relation::make()->field('categories')->renderCell($this->author);
    }

    public function testKeyMissingThrowsException(): void {
        $column = Relation::make();

        $this->expectException(ColumnMissingPropertyException::class);
        $column->getKey();
    }
}
