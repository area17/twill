<?php

namespace A17\Twill\Tests\Integration\Controllers\Tables\Filters;

use A17\Twill\Services\Listings\Filters\BasicFilter;
use A17\Twill\Services\Listings\Filters\BelongsToFilter;
use A17\Twill\Services\Listings\Filters\Exceptions\MissingModelForFilterException;
use App\Models\Author;
use App\Models\Category;
use App\Repositories\AuthorRepository;
use Illuminate\Support\Str;

class BelongsToFilterTest extends FilterTestBase
{
    private Category $category2;

    public function setUp(): void
    {
        parent::setUp();

        $this->category2 = $this->createCategory();
        $this->createCategory();

        $this->author = $this->createAuthor(5);
        $this->author->public = true;
        $this->author->category_id = $this->category->id;
        $this->author->save();
    }

    public function testInferredFromField(): void
    {
        $filter = BelongsToFilter::make()->field('category');
        $this->assertEquals('categories', $filter->getLabel());
        $this->assertEquals('category', $filter->getQueryString());
        $this->assertEquals(Category::class, $filter->getModel());
        $this->assertEquals(
            collect(
                [
                    $this->category->id => $this->category->title,
                    $this->category2->id => $this->category2->title,
                    'all' => 'All',
                ]
            ),
            $filter->getOptions(app()->make(AuthorRepository::class))
        );

        $filter = BelongsToFilter::make()->field('category')->withoutIncludeAll();

        $this->assertEquals(
            collect([$this->category->id => $this->category->title, $this->category2->id => $this->category2->title]),
            $filter->getOptions(app()->make(AuthorRepository::class))
        );

        $filter = BelongsToFilter::make()->field('category')->valueLabelField('slug')->withoutIncludeAll();

        $this->assertEquals(
            collect(
                [
                    $this->category->id => Str::slug($this->category->title),
                    $this->category2->id => Str::slug($this->category2->title),
                ]
            ),
            $filter->getOptions(app()->make(AuthorRepository::class))
        );
    }

    public function testExceptionWhenUnableToInferModelFromFieldName(): void
    {
        $filter = BelongsToFilter::make()->field('demo');
        $this->expectException(MissingModelForFilterException::class);
        $this->expectExceptionMessage('Model is not set for BelongsToFilter demo');

        $filter->withFilterValue('test');
        $filter->applyFilter(Author::query());
    }

    public function testExceptionWhenUnableToInferModelFromFieldNameWhenGettingOptions(): void
    {
        $filter = BelongsToFilter::make()->field('demo');
        $this->expectException(MissingModelForFilterException::class);
        $this->expectExceptionMessage('Model is not set for BelongsToFilter demo');

        $filter->getOptions(app()->make(AuthorRepository::class));
    }

    public function testNoFilterActive(): void
    {
        $filters = [BelongsToFilter::make()->field('category')];

        $data = $this->controllerWithFiltersAndQuickFilters($filters)->index()->getData();

        /** @var BasicFilter $basicFilter */
        $basicFilter = $data['hiddenFilters'][0];

        $this->assertEquals('categories', $basicFilter->getLabel());

        $this->assertCount(5, $data['tableData']);
    }

    public function testCustomLabel(): void
    {
        $filters = [BelongsToFilter::make()->field('category')->label('Example')];

        $data = $this->controllerWithFiltersAndQuickFilters($filters)->index()->getData();

        $this->assertEquals('Example', $data['hiddenFilters'][0]->getLabel());
    }

    public function testFilterWithDefault(): void
    {
        $filters = [BelongsToFilter::make()->field('category')->label('Example')->default($this->category->id)];

        $data = $this->controllerWithFiltersAndQuickFilters($filters)->index()->getData();

        $this->assertCount(1, $data['tableData']);

        $this->assertStringContainsString($this->author->name, $data['tableData'][0]['name']);
    }

    public function testFilterWithRequestData(): void
    {
        $filters = [BelongsToFilter::make()->field('category')];

        $data = $this->controllerWithFiltersAndQuickFilters($filters, active: ['category' => $this->category->id])
            ->index()
            ->getData();

        $this->assertCount(1, $data['tableData']);

        $this->assertStringContainsString($this->author->name, $data['tableData'][0]['name']);

        // Category 2 is not attached to anything so we should have no results
        $data = $this->controllerWithFiltersAndQuickFilters($filters, active: ['category' => $this->category2->id])
            ->index()
            ->getData();

        $this->assertCount(0, $data['tableData']);
    }

    public function testWithInvalidData(): void
    {
        $filters = [BelongsToFilter::make()->field('category')];

        $data = $this->controllerWithFiltersAndQuickFilters($filters, active: ['category' => 'invalid'])
            ->index()
            ->getData();

        $this->assertCount(0, $data['tableData']);
    }
}
