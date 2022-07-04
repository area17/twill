<?php

namespace A17\Twill\Tests\Integration\Controllers\Tables\Filters;

use A17\Twill\Services\Listings\Filters\BasicFilter;
use A17\Twill\Services\Listings\Filters\Exceptions\FilterOptionsMissingException;
use App\Models\Author;
use Illuminate\Database\Eloquent\Builder;

class BasicFilterTest extends FilterTestBase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->author = $this->createAuthor(5);
    }

    public function testNoFilterActive(): void
    {
        $filters = [
            BasicFilter::make()
                ->queryString('title_test')
                ->options(collect(['yes' => 'yes', 'no' => 'no']))
                ->apply(fn(Builder $builder, string $value) => $builder->whereTranslation('name', Author::first()->name)
                ),
        ];

        $data = $this->controllerWithFiltersAndQuickFilters($filters)->index()->getData();

        /** @var BasicFilter $basicFilter */
        $basicFilter = $data['hiddenFilters'][0];

        $this->assertEquals('title tests', $basicFilter->getLabel());

        $this->assertCount(5, $data['tableData']);
    }

    public function testExceptionOnMissingOptions(): void
    {
        $filters = [
            BasicFilter::make()
                ->queryString('title_test')->label('Example'),
        ];

        $this->expectException(FilterOptionsMissingException::class);

        $this->controllerWithFiltersAndQuickFilters($filters)->index()->getData();
    }

    public function testCustomLabel(): void
    {
        $filters = [
            BasicFilter::make()
                ->options(collect(['yes' => 'yes', 'no' => 'no']))
                ->queryString('title_test')->label('Example'),
        ];

        $data = $this->controllerWithFiltersAndQuickFilters($filters)->index()->getData();

        $this->assertEquals('Example', $data['hiddenFilters'][0]->getLabel());
    }

    public function testFilterWithDefault(): void
    {
        $filters = [
            BasicFilter::make()
                ->queryString('title_test')
                ->default('yes')
                ->options(collect(['yes' => 'yes', 'no' => 'no']))
                ->apply(fn(Builder $builder, string $value) => $builder->whereTranslation('name', Author::first()->name)
                ),
        ];

        $data = $this->controllerWithFiltersAndQuickFilters($filters)->index()->getData();

        $this->assertCount(1, $data['tableData']);

        $this->assertStringContainsString(Author::first()->name, $data['tableData'][0]['name']);
    }

    public function testFilterWithRequestData(): void
    {
        $filters = [
            BasicFilter::make()
                ->queryString('title_test')
                ->options(collect(['yes' => 'yes', 'no' => 'no']))
                ->apply(fn(Builder $builder, string $value) => $builder->whereTranslation('name', Author::first()->name)
                ),
        ];

        $data = $this->controllerWithFiltersAndQuickFilters($filters, active: ['title_test' => 'yes'])
            ->index()
            ->getData();

        $this->assertCount(1, $data['tableData']);

        $this->assertStringContainsString(Author::first()->name, $data['tableData'][0]['name']);
    }
}
