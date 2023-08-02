<?php

namespace A17\Twill\Tests\Integration\Controllers\Tables\Filters;

use A17\Twill\Services\Listings\Filters\BasicFilter;
use A17\Twill\Services\Listings\Filters\BooleanFilter;
use App\Repositories\AuthorRepository;

class BooleanFilterTest extends FilterTestBase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->author = $this->createAuthor(5);
        $this->author->public = true;
        $this->author->save();
    }

    public function testInferredFromField(): void
    {
        $filter = BooleanFilter::make()->field('public');
        $this->assertEquals('publics', $filter->getLabel());
        $this->assertEquals('public', $filter->getQueryString());
        $this->assertEquals(
            collect(['yes' => 'Yes', 'no' => 'No', 'all' => 'All']),
            $filter->getOptions(app()->make(AuthorRepository::class))
        );

        $filter = BooleanFilter::make()->field('public')->withoutIncludeAll();

        $this->assertEquals(
            collect(['yes' => 'Yes', 'no' => 'No']),
            $filter->getOptions(app()->make(AuthorRepository::class))
        );
    }

    public function testNoFilterActive(): void
    {
        $filters = [BooleanFilter::make()->field('public')];

        $data = $this->controllerWithFiltersAndQuickFilters($filters)->index()->getData();

        /** @var BasicFilter $basicFilter */
        $basicFilter = $data['hiddenFilters'][0];

        $this->assertEquals('publics', $basicFilter->getLabel());

        $this->assertCount(5, $data['tableData']);
    }

    public function testCustomLabel(): void
    {
        $filters = [BooleanFilter::make()->field('public')->label('Example')];

        $data = $this->controllerWithFiltersAndQuickFilters($filters)->index()->getData();

        $this->assertEquals('Example', $data['hiddenFilters'][0]->getLabel());
    }

    public function testFilterWithDefault(): void
    {
        $filters = [BooleanFilter::make()->field('public')->default(BooleanFilter::TRUE)];

        $data = $this->controllerWithFiltersAndQuickFilters($filters)->index()->getData();

        $this->assertCount(1, $data['tableData']);

        $this->assertStringContainsString($this->author->name, $data['tableData'][0]['name']);
    }

    public function testFilterWithRequestData(): void
    {
        $filters = [BooleanFilter::make()->field('public')];

        $data = $this->controllerWithFiltersAndQuickFilters($filters, active: ['public' => BooleanFilter::TRUE])
            ->index()
            ->getData();

        $this->assertCount(1, $data['tableData']);

        $this->assertStringContainsString($this->author->name, $data['tableData'][0]['name']);

        // Test the negative value.
        $data = $this->controllerWithFiltersAndQuickFilters($filters, active: ['public' => BooleanFilter::FALSE])
            ->index()
            ->getData();

        $this->assertCount(4, $data['tableData']);
    }

    public function testWithInvalidData(): void
    {
        $filters = [BooleanFilter::make()->field('public')];

        $data = $this->controllerWithFiltersAndQuickFilters($filters, active: ['public' => 'SomeValue'])
            ->index()
            ->getData();

        // As the filter is invalid, we just want to see all values.
        $this->assertCount(5, $data['tableData']);
    }
}
