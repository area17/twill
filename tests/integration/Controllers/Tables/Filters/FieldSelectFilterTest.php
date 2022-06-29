<?php

namespace A17\Twill\Tests\Integration\Controllers\Tables\Filters;

use A17\Twill\Services\Listings\Filters\BasicFilter;
use A17\Twill\Services\Listings\Filters\FieldSelectFilter;
use App\Repositories\AuthorRepository;

class FieldSelectFilterTest extends FilterTestBase
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

    public function testInferredFromField(): void
    {
        $filter = FieldSelectFilter::make()->field('year');
        $this->assertEquals('years', $filter->getLabel());
        $this->assertEquals('year', $filter->getQueryString());
        $this->assertEquals(
            collect(
                [
                    '2000' => '2000',
                    '2022' => '2022',
                    'all' => 'All',
                ]
            ),
            $filter->getOptions(app()->make(AuthorRepository::class))
        );

        $filter = FieldSelectFilter::make()->field('year')->withoutIncludeAll();

        $this->assertEquals(
            collect(
                [
                    '2000' => '2000',
                    '2022' => '2022',
                ]
            ),
            $filter->getOptions(app()->make(AuthorRepository::class))
        );

        // Enable the without value option.
        $filter = FieldSelectFilter::make()->field('year')->withoutIncludeAll()->withWithoutValueOption();

        $this->assertEquals(
            collect(
                [
                    '2000' => '2000',
                    '2022' => '2022',
                    'null' => 'Without value',
                ]
            ),
            $filter->getOptions(app()->make(AuthorRepository::class))
        );
    }

    public function testNoFilterActive(): void
    {
        $filters = [FieldSelectFilter::make()->field('year')];

        $data = $this->controllerWithFiltersAndQuickFilters($filters)->index()->getData();

        /** @var BasicFilter $basicFilter */
        $basicFilter = $data['hiddenFilters'][0];

        $this->assertEquals('years', $basicFilter->getLabel());

        $this->assertCount(5, $data['tableData']);
    }

    public function testCustomLabel(): void
    {
        $filters = [FieldSelectFilter::make()->field('year')->label('Example')];

        $data = $this->controllerWithFiltersAndQuickFilters($filters)->index()->getData();

        $this->assertEquals('Example', $data['hiddenFilters'][0]->getLabel());
    }

    public function testFilterWithDefault(): void
    {
        $filters = [FieldSelectFilter::make()->field('year')->default('2022')];

        $data = $this->controllerWithFiltersAndQuickFilters($filters)->index()->getData();

        $this->assertCount(1, $data['tableData']);

        $this->assertStringContainsString($this->author->name, $data['tableData'][0]['name']);
    }

    public function testFilterWithRequestData(): void
    {
        $filters = [FieldSelectFilter::make()->field('year')];

        $data = $this->controllerWithFiltersAndQuickFilters($filters, active: ['year' => '2022'])
            ->index()
            ->getData();

        $this->assertCount(1, $data['tableData']);

        $this->assertStringContainsString($this->author->name, $data['tableData'][0]['name']);

        $data = $this->controllerWithFiltersAndQuickFilters($filters, active: ['year' => '2000'])
            ->index()
            ->getData();

        $this->assertCount(1, $data['tableData']);

        $this->assertStringContainsString($this->author2->name, $data['tableData'][0]['name']);
    }

    public function testWithInvalidData(): void
    {
        $filters = [FieldSelectFilter::make()->field('year')];

        $data = $this->controllerWithFiltersAndQuickFilters($filters, active: ['year' => '3000'])
            ->index()
            ->getData();

        $this->assertCount(0, $data['tableData']);
    }

    public function testFilterShowNullOnly(): void
    {
        $filters = [FieldSelectFilter::make()->field('year')];

        $data = $this->controllerWithFiltersAndQuickFilters(
            $filters,
            active: ['year' => FieldSelectFilter::OPTION_NOT_SET]
        )
            ->index()
            ->getData();

        $this->assertCount(3, $data['tableData']);
    }
}
