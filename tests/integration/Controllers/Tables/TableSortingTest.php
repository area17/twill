<?php

namespace A17\Twill\Tests\Integration\Controllers\Tables;

use A17\Twill\Services\Listings\Columns\Text;
use A17\Twill\Services\Listings\TableColumns;
use A17\Twill\Tests\Integration\ModulesTestBase;
use App\Http\Controllers\Twill\AuthorController;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class TableSortingTest extends ModulesTestBase
{

    public function setUp(): void
    {
        parent::setUp();
        // Created at order;
        // 9,7,5,3,1,2,4,6,8,10
        for ($i = 0; $i < 10; $i++) {
            Carbon::setTestNow($i % 2 ? Carbon::now()->subDays($i) : Carbon::now()->addDays($i));
            $this->author = $this->createAuthor(1, ['title' => $i, 'year' => '200' . $i]);
        }
    }

    public function testSortsCreatedAtByDefault(): void
    {
        $this->app->bind(
            AuthorController::class,
            function ($app) {
                return new class($app, request()) extends AuthorController {
                    // Set index options empty.
                    protected $indexOptions = [];
                };
            },
        );

        $this->getJson(route('twill.personnel.authors.index'))
            ->assertJsonPath('tableData.0.id', 9)
            ->assertJsonPath('tableData.0.year', '2008')
            ->assertJsonPath('tableData.1.id', 7)
            ->assertJsonPath('tableData.1.year', '2006')
            ->assertJsonPath('tableData.2.id', 5)
            ->assertJsonPath('tableData.2.year', '2004')
            ->assertJsonPath('tableData.3.id', 3)
            ->assertJsonPath('tableData.3.year', '2002')
            ->assertJsonPath('tableData.4.id', 1)
            ->assertJsonPath('tableData.4.year', '2000')
            ->assertJsonPath('tableData.5.id', 2)
            ->assertJsonPath('tableData.5.year', '2001')
            ->assertJsonPath('tableData.6.id', 4)
            ->assertJsonPath('tableData.6.year', '2003')
            ->assertJsonPath('tableData.7.id', 6)
            ->assertJsonPath('tableData.7.year', '2005')
            ->assertJsonPath('tableData.8.id', 8)
            ->assertJsonPath('tableData.8.year', '2007')
            ->assertJsonPath('tableData.9.id', 10)
            ->assertJsonPath('tableData.9.year', '2009');
    }

    public function testSortsByYearAsc(): void
    {
        $this->app->bind(
            AuthorController::class,
            function ($app) {
                return new class($app, request()) extends AuthorController {
                    // Set index options empty.
                    protected $indexOptions = [];
                };
            },
        );

        $this->getJson(route('twill.personnel.authors.index', ['sortDir' => 'asc', 'sortKey' => 'year']))
            ->assertJsonPath('tableData.0.year', '2000')
            ->assertJsonPath('tableData.1.year', '2001')
            ->assertJsonPath('tableData.2.year', '2002')
            ->assertJsonPath('tableData.3.year', '2003')
            ->assertJsonPath('tableData.4.year', '2004')
            ->assertJsonPath('tableData.5.year', '2005')
            ->assertJsonPath('tableData.6.year', '2006')
            ->assertJsonPath('tableData.7.year', '2007')
            ->assertJsonPath('tableData.8.year', '2008')
            ->assertJsonPath('tableData.9.year', '2009');
    }

    public function testSortsByYearDesc(): void
    {
        $this->app->bind(
            AuthorController::class,
            function ($app) {
                return new class($app, request()) extends AuthorController {
                    // Set index options empty.
                    protected $indexOptions = [];
                };
            },
        );

        $this->getJson(route('twill.personnel.authors.index', ['sortDir' => 'desc', 'sortKey' => 'year']))
            ->assertJsonPath('tableData.0.year', '2009')
            ->assertJsonPath('tableData.1.year', '2008')
            ->assertJsonPath('tableData.2.year', '2007')
            ->assertJsonPath('tableData.3.year', '2006')
            ->assertJsonPath('tableData.4.year', '2005')
            ->assertJsonPath('tableData.5.year', '2004')
            ->assertJsonPath('tableData.6.year', '2003')
            ->assertJsonPath('tableData.7.year', '2002')
            ->assertJsonPath('tableData.8.year', '2001')
            ->assertJsonPath('tableData.9.year', '2000');
    }

    public function testSetDefaultSortUsingTableBuilderAsc(): void
    {
        $this->app->bind(
            AuthorController::class,
            function ($app) {
                return new class($app, request()) extends AuthorController {
                    // Set index options empty.
                    protected $indexOptions = [];
                    protected $indexColumns = [];

                    protected function getIndexTableColumns(): TableColumns
                    {
                        return TableColumns::make([
                            Text::make()->field('year')->sortByDefault(direction: 'asc'),
                        ]);
                    }
                };
            },
        );

        $this->getJson(route('twill.personnel.authors.index'))
            ->assertJsonPath('tableData.0.year', '2000')
            ->assertJsonPath('tableData.1.year', '2001')
            ->assertJsonPath('tableData.2.year', '2002')
            ->assertJsonPath('tableData.3.year', '2003')
            ->assertJsonPath('tableData.4.year', '2004')
            ->assertJsonPath('tableData.5.year', '2005')
            ->assertJsonPath('tableData.6.year', '2006')
            ->assertJsonPath('tableData.7.year', '2007')
            ->assertJsonPath('tableData.8.year', '2008')
            ->assertJsonPath('tableData.9.year', '2009');
    }

    public function testSetDefaultSortUsingTableBuilderDesc(): void
    {
        $this->app->bind(
            AuthorController::class,
            function ($app) {
                return new class($app, request()) extends AuthorController {
                    // Set index options empty.
                    protected $indexOptions = [];
                    protected $indexColumns = [];

                    protected function getIndexTableColumns(): TableColumns
                    {
                        return TableColumns::make([
                            Text::make()->field('year')->sortByDefault(direction: 'desc'),
                        ]);
                    }
                };
            },
        );

        $this->getJson(route('twill.personnel.authors.index'))
            ->assertJsonPath('tableData.0.year', '2009')
            ->assertJsonPath('tableData.1.year', '2008')
            ->assertJsonPath('tableData.2.year', '2007')
            ->assertJsonPath('tableData.3.year', '2006')
            ->assertJsonPath('tableData.4.year', '2005')
            ->assertJsonPath('tableData.5.year', '2004')
            ->assertJsonPath('tableData.6.year', '2003')
            ->assertJsonPath('tableData.7.year', '2002')
            ->assertJsonPath('tableData.8.year', '2001')
            ->assertJsonPath('tableData.9.year', '2000');
    }

    public function testCustomSortFunction(): void
    {
        $this->app->bind(
            AuthorController::class,
            function ($app) {
                return new class($app, request()) extends AuthorController {
                    // Set index options empty.
                    protected $indexOptions = [];
                    protected $indexColumns = [];

                    protected function getIndexTableColumns(): TableColumns
                    {
                        return TableColumns::make([
                            Text::make()->field('year')->sortByDefault(direction: 'asc')->order(
                                function (Builder $builder) {
                                    return $builder->orderBy('year', 'desc')->where('id', '!=', 1);
                                    // Here we sort ASCENDING insteadof of DESC just to prove it works.
                                    // We also exclude an ID.
                                }
                            ),
                        ]);
                    }
                };
            },
        );

        $this->getJson(route('twill.personnel.authors.index'))
            ->assertJsonPath('tableData.0.year', '2009')
            ->assertJsonPath('tableData.1.year', '2008')
            ->assertJsonPath('tableData.2.year', '2007')
            ->assertJsonPath('tableData.3.year', '2006')
            ->assertJsonPath('tableData.4.year', '2005')
            ->assertJsonPath('tableData.5.year', '2004')
            ->assertJsonPath('tableData.6.year', '2003')
            ->assertJsonPath('tableData.7.year', '2002')
            ->assertJsonPath('tableData.8.year', '2001')
            // 9 is not here as we excluded the id.
            ->assertJsonpath('tableData.9.year', null);
    }
}
