<?php

namespace A17\Twill\Tests\Integration\Controllers\Tables\Filters;

use A17\Twill\Services\Listings\Filters\QuickFilter;
use App\Models\Author;
use App\Repositories\AuthorRepository;
use Illuminate\Database\Eloquent\Builder;

class QuickFilterTest extends FilterTestBase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->author = $this->createAuthor(5);
        $this->author->published = true;
        $this->author->save();
    }

    public function testNoQuickFilterActive(): void
    {
        $filters = [
            QuickFilter::make()
                ->queryString('title_test')
                ->amount(
                    fn() => app()
                        ->make(AuthorRepository::class)
                        ->whereTranslation('name', Author::first()->name)
                        ->count()
                )
                ->apply(fn(Builder $builder) => $builder->whereTranslation('name', Author::first()->name)
                ),
        ];

        $data = $this->controllerWithFiltersAndQuickFilters(extendQuickFilters: $filters)->index()->getData();

        $this->assertCount(6, $data['tableMainFilters']);

        // Last filter is our new one.
        $filter = $data['tableMainFilters'][5];

        $this->assertEquals('title tests', $filter['name']);
        $this->assertEquals('title_test', $filter['slug']);
        $this->assertEquals(1, $filter['number']);

        $this->assertCount(5, $data['tableData']);
    }

    public function testCustomLabel(): void
    {
        $filters = [
            QuickFilter::make()
                ->queryString('title_test')
                ->label('Custom label')
                ->amount(
                    fn() => app()
                        ->make(AuthorRepository::class)
                        ->whereTranslation('name', Author::first()->name)
                        ->count()
                )
                ->apply(fn(Builder $builder) => $builder->whereTranslation('name', Author::first()->name)
                ),
        ];

        $data = $this->controllerWithFiltersAndQuickFilters(quickFilters: $filters)->index()->getData();

        $filter = $data['tableMainFilters'][0];
        $this->assertEquals('Custom label', $filter['name']);
    }

    public function testQuickFilterUsingScope(): void
    {
        $filters = [
            QuickFilter::make()
                ->queryString('title_test')
                ->scope('published')
                ->amount(
                    fn() => app()
                        ->make(AuthorRepository::class)
                        ->getCountByStatusSlug('published')
                ),
        ];

        $data = $this->controllerWithFiltersAndQuickFilters(quickFilters: $filters)->index()->getData();

        $this->assertCount(1, $data['tableData']);
        $this->assertStringContainsString($this->author->name, $data['tableData'][0]['name']);
    }

    public function testDisabledFilterDoesNotShow(): void {
        $filters = [
            QuickFilter::make()
                ->queryString('title_test')
                ->disable()
                ->amount(
                    fn() => app()
                        ->make(AuthorRepository::class)
                        ->whereTranslation('name', Author::first()->name)
                        ->count()
                )
                ->apply(fn(Builder $builder) => $builder->whereTranslation('name', Author::first()->name)
                ),
        ];

        $data = $this->controllerWithFiltersAndQuickFilters(extendQuickFilters: $filters)->index()->getData();

        // 5 + 1 for the default filter, but now we count without the disabled filter.
        $this->assertCount(5, $data['tableMainFilters']);
    }

    public function testSingleQuickerFilterIsAppliedByDefault(): void
    {
        $filters = [
            // Because there is no default quick filter, this becomes the default quick filter.
            QuickFilter::make()
                ->queryString('first_author')
                ->amount(
                    fn() => app()
                        ->make(AuthorRepository::class)
                        ->whereTranslation('name', Author::first()->name)
                        ->count()
                )
                ->apply(fn(Builder $builder) => $builder->whereTranslation('name', Author::first()->name)
                ),
            QuickFilter::make()
                ->queryString('last_author')
                ->amount(
                    fn() => app()
                        ->make(AuthorRepository::class)
                        ->whereTranslation('name', Author::latest()->first()->name)
                        ->count()
                )
                ->apply(
                    fn(Builder $builder) => $builder->whereTranslation(
                        'name',
                        Author::latest()->first()->name
                    )
                ),
        ];

        $data = $this->controllerWithFiltersAndQuickFilters(quickFilters: $filters)->index()->getData();

        $this->assertCount(1, $data['tableData']);

        $this->assertStringContainsString(Author::first()->name, $data['tableData'][0]['name']);
    }

    public function testFilterWithDefault(): void
    {
        $filters = [
            QuickFilter::make()
                ->queryString('first_author')
                ->amount(
                    fn() => app()
                        ->make(AuthorRepository::class)
                        ->whereTranslation('name', Author::first()->name)
                        ->count()
                )
                ->apply(fn(Builder $builder) => $builder->whereTranslation('name', Author::first()->name)
                ),
            QuickFilter::make()
                ->queryString('last_author')
                ->default()
                ->amount(
                    fn() => app()
                        ->make(AuthorRepository::class)
                        ->whereTranslation('name', Author::latest()->first()->name)
                        ->count()
                )
                ->apply(
                    fn(Builder $builder) => $builder->whereTranslation(
                        'name',
                        Author::latest()->first()->name
                    )
                ),
        ];

        $data = $this->controllerWithFiltersAndQuickFilters(quickFilters: $filters)->index()->getData();

        $this->assertCount(1, $data['tableData']);

        $this->assertStringContainsString(Author::latest()->first()->name, $data['tableData'][0]['name']);
    }

    public function testFilterWithRequestData(): void
    {
        $filters = [
            QuickFilter::make()
                ->queryString('first_author')
                ->amount(
                    fn() => app()
                        ->make(AuthorRepository::class)
                        ->whereTranslation('name', Author::first()->name)
                        ->count()
                )
                ->apply(fn(Builder $builder) => $builder->whereTranslation('name', Author::first()->name)
                ),
            QuickFilter::make()
                ->queryString('last_author')
                ->default()
                ->amount(
                    fn() => app()
                        ->make(AuthorRepository::class)
                        ->whereTranslation('name', Author::latest()->first()->name)
                        ->count()
                )
                ->apply(
                    fn(Builder $builder) => $builder->whereTranslation(
                        'name',
                        Author::latest()->first()->name
                    )
                ),
        ];

        $data = $this->controllerWithFiltersAndQuickFilters(quickFilters: $filters, active: ['status' => 'first_author']
        )
            ->index()
            ->getData();

        $this->assertCount(1, $data['tableData']);

        $this->assertStringContainsString(Author::first()->name, $data['tableData'][0]['name']);

        // Switch the quick filter to the last author one.
        $data = $this->controllerWithFiltersAndQuickFilters(quickFilters: $filters, active: ['status' => 'last_author'])
            ->index()
            ->getData();

        $this->assertCount(1, $data['tableData']);

        $this->assertStringContainsString(Author::latest()->first()->name, $data['tableData'][0]['name']);
    }
}
