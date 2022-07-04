<?php

namespace A17\Twill\Tests\Integration\Controllers\Tables\Filters;

class SearchTest extends FilterTestBase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->author = $this->createAuthor(3);

        $this->author2 = $this->createAuthor();
        $this->author2->year = '2000';
        $this->author2->save();

        $this->author = $this->createAuthor();
        $this->author->year = '2022';
        $this->author->save();
    }

    public function testSearchWithDefault(): void
    {
        // No search.
        $data = $this->controllerWithFiltersAndQuickFilters()->index()->getData();
        $this->assertCount(5, $data['tableData']);

        // Search for the first author name.
        $data = $this->controllerWithFiltersAndQuickFilters(active: ['search' => $this->author->name])->index()->getData();
        $this->assertCount(1, $data['tableData']);

        // Search for another field yield no results.
        $data = $this->controllerWithFiltersAndQuickFilters(active: ['search' => '2022'])->index()->getData();
        $this->assertCount(0, $data['tableData']);
    }

    public function testSearchColumnSetter(): void {
        // Search for another field yield no results.
        $data = $this->controllerWithFiltersAndQuickFilters(active: ['search' => '2022'])->index()->getData();
        $this->assertCount(0, $data['tableData']);

        // Set the search columns.
        $controller = $this->controllerWithFiltersAndQuickFilters(active: ['search' => '2022']);
        $controller->setSearchColumnsTest(['name', 'year']);

        $data = $controller->index()->getData();
        $this->assertCount(1, $data['tableData']);
    }
}
